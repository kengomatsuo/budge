@props([
    'users' => [],
    'selected' => [],
    'expenseAmount' => 0,
    'initialSplits' => [],
    'namePrefix' => 'shared',
])

<div x-data="sharedMembersComponent({
    allUsers: {{ json_encode($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
    initialSelected: {{ json_encode(array_values((array) $selected)) }},
    amount: {{ json_encode((float) $expenseAmount) }},
    initialSplits: {{ json_encode((array) $initialSplits) }},
    namePrefix: {{ json_encode($namePrefix) }},
})" x-init="init()">

    <input type="hidden" name="split_type" :value="splitType">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
        <div class="md:col-span-2 relative" @click.outside="suggestions = []; focusIndex = -1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.split_with') }}</label>
            <input type="text" x-model="query" @input.debounce.250="filter()" :placeholder="'{{ __('messages.search_users') }}'"
                @keydown.arrow-down.prevent="moveDown()" @keydown.arrow-up.prevent="moveUp()" @keydown.enter.prevent="selectFocused()" @keydown.escape.prevent="suggestions=[]; focusIndex=-1"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

            <div x-show="suggestions.length > 0" x-transition
                class="absolute left-0 right-0 w-full mt-1 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow max-h-48 overflow-auto z-50">
                <template x-for="(user, index) in suggestions" :key="user.id">
                    <div @click="addUser(user)" @mouseenter="focusIndex = index" @mouseleave="focusIndex = -1"
                        :class="{ 'bg-indigo-50 dark:bg-indigo-600 text-indigo-700 dark:text-white': focusIndex === index }"
                        class="px-3 py-2 cursor-pointer w-full">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="user.name"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="user.email"></div>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.split_method') }}</label>
            <select x-model="splitType"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="equal">{{ __('messages.equal') }}</option>
                <option value="custom">{{ __('messages.custom') }}</option>
            </select>
        </div>
    </div>

    <template x-if="selected.length === 0">
        <p class="text-sm text-gray-500">{{ __('messages.no_users_selected') }}</p>
    </template>

    <div class="space-y-2">
        <template x-for="(u, idx) in selected" :key="u.id">
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-2 rounded">
                <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100" x-text="u.name"></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="u.email"></div>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="hidden" :name="`${namePrefix}_users[]`" :value="u.id">
                    <template x-if="splitType === 'equal'">
                        <div class="text-right font-semibold text-gray-900 dark:text-gray-100"> <span x-text="currency" class="mr-1"></span><span
                                x-text="format(equalAmountForIndex(idx))"></span></div>
                    </template>
                    <template x-if="splitType === 'custom'">
                        <input type="number" step="0.01" min="0" :name="`${namePrefix}_splits[${u.id}]`"
                            x-model.number="customSplits[u.id]" @blur="clamp(u.id)"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-right px-2 py-1">
                    </template>
                    <button type="button" @click.prevent="removeUser(u.id)" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" aria-label="{{ __('messages.remove') }}">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div class="pt-2">
        <div class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.total_assigned') }}: <span class="font-semibold"
                x-text="format(totalAssigned)"></span> / <span class="font-semibold" x-text="format(amount)"></span>
        </div>
        <template x-if="splitType === 'custom' && totalAssigned > amount">
            <div class="text-sm text-red-600 dark:text-red-400">{{ __('messages.assigned_exceeds') }}</div>
        </template>
    </div>

</div>

<script>
    function sharedMembersComponent({
        allUsers,
        initialSelected,
        amount,
        initialSplits,
        namePrefix
    }) {
        return {
            namePrefix: namePrefix,
            allUsers: allUsers,
            query: '',
            suggestions: [],
            selected: [],
            splitType: 'equal',
            amount: parseFloat(amount || 0),
            currency: '{{ auth()->user()->preferred_currency }}',
            customSplits: {},
            equalBase: 0,
            equalRemainder: 0,
            focusIndex: -1,
            init() {
                this.selected = initialSelected.map(id => this.allUsers.find(u => u.id == id)).filter(Boolean);
                // apply initial custom splits if provided
                if (typeof initialSplits === 'object') {
                    Object.keys(initialSplits).forEach(k => {
                        this.customSplits[k] = parseFloat(initialSplits[k] || 0);
                    });
                }
                this.selected.forEach(u => {
                    this.customSplits[u.id] = parseFloat(this.customSplits[u.id] || 0);
                });
                this.computeEqual();
            },
            filter() {
                const q = this.query.trim().toLowerCase();
                if (!q) {
                    this.suggestions = [];
                    this.focusIndex = -1;
                    return;
                }
                this.suggestions = this.allUsers.filter(u => (u.name + u.email).toLowerCase().includes(q) && !this
                    .selected.find(s => s.id === u.id)).slice(0, 6);
                this.focusIndex = this.suggestions.length ? 0 : -1;
            },
            moveDown() {
                if (!this.suggestions.length) return;
                this.focusIndex = (this.focusIndex + 1) % this.suggestions.length;
                this.scrollFocusedIntoView();
            },
            moveUp() {
                if (!this.suggestions.length) return;
                this.focusIndex = (this.focusIndex - 1 + this.suggestions.length) % this.suggestions.length;
                this.scrollFocusedIntoView();
            },
            selectFocused() {
                if (this.focusIndex >= 0 && this.suggestions[this.focusIndex]) {
                    this.addUser(this.suggestions[this.focusIndex]);
                }
            },
            scrollFocusedIntoView() {
                this.$nextTick(() => {
                    const container = this.$root.querySelector('[x-show]');
                    const items = container?.querySelectorAll('[role="option"]') || [];
                    if (items[this.focusIndex]) items[this.focusIndex].scrollIntoView({ block: 'nearest' });
                });
            },
            addUser(user) {
                this.selected.push(user);
                this.query = '';
                this.suggestions = [];
                this.focusIndex = -1;
                if (this.splitType === 'equal') this.computeEqual();
                if (this.splitType === 'custom') {
                    this.customSplits[user.id] = 0;
                }
            },
            removeUser(id) {
                this.selected = this.selected.filter(u => u.id !== id);
                delete this.customSplits[id];
                if (this.splitType === 'equal') this.computeEqual();
            },
            computeEqual() {
                if (this.selected.length === 0) {
                    this.equalBase = 0;
                    this.equalRemainder = 0;
                    return;
                }
                const base = Math.floor((this.amount / this.selected.length) * 100) / 100;
                const totalAssigned = base * this.selected.length;
                const remainder = Math.round((this.amount - totalAssigned) * 100) / 100;
                this.equalBase = base;
                this.equalRemainder = remainder;
            },
            equalAmountForIndex(idx) {
                return Number((this.equalBase + (idx === 0 ? this.equalRemainder : 0)).toFixed(2));
            },
            clamp(id) {
                const val = parseFloat(this.customSplits[id] || 0);
                if (val < 0) this.customSplits[id] = 0;
                const others = this.selected.filter(u => u.id != id).reduce((s, u) => s + (parseFloat(this.customSplits[
                    u.id] || 0)), 0);
                const maxAllowed = Math.max(0, parseFloat((this.amount - others).toFixed(2)));
                if (val > maxAllowed) {
                    this.customSplits[id] = maxAllowed;
                }
            },
            get totalAssigned() {
                if (this.splitType === 'equal') return (this.equalBase || 0) * this.selected.length + (this
                    .equalRemainder || 0);
                return this.selected.reduce((s, u) => s + (parseFloat(this.customSplits[u.id] || 0)), 0);
            },
            format(v) {
                return Number(v || 0).toFixed(2);
            },
        }
    }
</script>
