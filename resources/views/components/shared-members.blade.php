@props([
    'users' => [],
    'currentUser' => null,
    'selected' => [],
    'expenseAmount' => 0,
    'initialSplits' => [],
    'namePrefix' => 'shared',
    'initialSplitType' => 'equal',
])

<div x-data="sharedMembersComponent({
    allUsers: {{ json_encode($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])) }},
    currentUser: {{ json_encode($currentUser ? ['id' => $currentUser->id, 'name' => $currentUser->name . ' (You)', 'email' => $currentUser->email] : null) }},
    initialSelected: {{ json_encode(array_values((array) $selected)) }},
    amount: {{ json_encode((float) $expenseAmount) }},
    initialSplits: {{ json_encode((array) $initialSplits) }},
    namePrefix: {{ json_encode($namePrefix) }},
    initialSplitType: {{ json_encode($initialSplitType) }},
})" x-init="init()"
@expense-amount-change.window="amount = parseFloat($event.detail); if(splitType === 'equal') computeEqual();"
@currency-change.window="currency = $event.detail">

    <input type="hidden" name="split_type" :value="splitType">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
        <div class="md:col-span-2 relative" @click.outside="suggestions = []; focusIndex = -1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.split_with') }}</label>
            <input type="text" x-model="query" @input.debounce.250="filter()" :placeholder="'{{ __('messages.search_users') }}'"
                @keydown.arrow-down.prevent="moveDown()" @keydown.arrow-up.prevent="moveUp()" @keydown.enter.prevent="selectFocused()" @keydown.escape.prevent="suggestions=[]; focusIndex=-1"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">

            <div x-show="suggestions.length > 0" x-transition
                class="absolute left-0 right-0 w-full mt-1 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow max-h-48 overflow-auto z-50">
                <template x-for="(user, index) in suggestions" :key="user.id">
                    <div @click="addUser(user)" @mouseenter="focusIndex = index" @mouseleave="focusIndex = -1"
                        :class="{ 'bg-primary-50 dark:bg-primary-600 text-primary-700 dark:text-white': focusIndex === index }"
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
            <select x-model="splitType" @change="$dispatch('split-type-change', splitType)"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="equal">{{ __('messages.split_equally') }}</option>
                <option value="manual">{{ __('messages.split_manually') }}</option>
                <option value="items">{{ __('messages.split_by_items') }}</option>
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
                        <div class="text-right font-semibold text-gray-900 dark:text-gray-100"><span
                                x-text="formatMoney(equalAmountForIndex(idx))"></span></div>
                    </template>
                    <template x-if="splitType === 'manual'">
                        <input type="number" step="0.01" min="0" :name="`${namePrefix}_splits[${u.id}]`"
                            x-model.number="customSplits[u.id]" @blur="clamp(u.id)"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-right px-2 py-1">
                    </template>
                    <button type="button" @click.prevent="removeUser(u.id)"
                        x-show="!currentUser || u.id !== currentUser.id"
                        class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" aria-label="{{ __('messages.remove') }}">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                    <div type="button" @click.prevent="removeUser(u.id)"
                        x-show="currentUser && u.id === currentUser.id"
                        class="p-1 rounded text-gray-600 dark:text-gray-300" aria-label="{{ __('messages.remove') }}">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="pt-2">
        <div class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.total_assigned') }}: <span class="font-semibold"
                x-text="formatMoney(totalAssigned)"></span> / <span class="font-semibold" x-text="formatMoney(amount)"></span>
        </div>
        <template x-if="splitType === 'manual' && totalAssigned > amount">
            <div class="text-sm text-red-600 dark:text-red-400">{{ __('messages.assigned_exceeds') }}</div>
        </template>
    </div>

</div>

<script>
    function sharedMembersComponent({
        allUsers,
        currentUser,
        initialSelected,
        amount,
        initialSplits,
        namePrefix,
        initialSplitType
    }) {
        return {
            namePrefix: namePrefix,
            allUsers: allUsers,
            currentUser: currentUser,
            query: '',
            suggestions: [],
            selected: [],
            splitType: initialSplitType === 'items' ? 'manual' : (initialSplitType || 'equal'),
            amount: parseFloat(amount || 0),
            currency: '{{ auth()->user()->preferred_currency }}',
            customSplits: {},
            equalBase: 0,
            equalRemainder: 0,
            focusIndex: -1,
            init() {
                // Get currency from parent form if available
                const parentForm = this.$root.closest('[x-data*="expenseForm"]');
                if (parentForm && parentForm.__x && parentForm.__x.$data && parentForm.__x.$data.form) {
                    this.currency = parentForm.__x.$data.form.currency;
                }

                // Initialize selected users
                this.selected = initialSelected.map(id => this.allUsers.find(u => u.id == id)).filter(Boolean);

                // Ensure current user is in the list if not already
                if (this.currentUser && !this.selected.find(u => u.id === this.currentUser.id)) {
                    this.selected.unshift(this.currentUser);
                }

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

                // Watchers for parent sync
                this.$watch('selected', (value) => {
                    this.$dispatch('selected-users-change', value);
                    if (this.splitType === 'equal') this.computeEqual();
                });
                this.$watch('splitType', (value) => {
                    this.$dispatch('split-type-change', value);
                    if (value === 'equal') this.computeEqual();
                });

                // Initial sync
                this.$nextTick(() => {
                    this.$dispatch('selected-users-change', this.selected);
                    this.$dispatch('split-type-change', this.splitType);
                });
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
                if (this.splitType === 'manual') {
                    this.customSplits[user.id] = 0;
                }
            },
            removeUser(id) {
                // Prevent removing current user
                if (this.currentUser && id === this.currentUser.id) return;

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
                // Split among selected users (which now includes current user)
                const count = this.selected.length;
                const amount = parseFloat(this.amount) || 0;
                const base = Math.floor((amount / count) * 100) / 100;
                const totalAssigned = base * count;
                const remainder = Math.round((amount - totalAssigned) * 100) / 100;

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
                if (this.splitType === 'equal') {
                    const base = parseFloat(this.equalBase) || 0;
                    const remainder = parseFloat(this.equalRemainder) || 0;
                    return base * this.selected.length + remainder;
                }
                if (this.splitType === 'items') return 0; // Handled elsewhere
                return this.selected.reduce((s, u) => s + (parseFloat(this.customSplits[u.id] || 0)), 0);
            },
            format(v) {
                return Number(v || 0).toFixed(2);
            },
            formatMoney(amount) {
                const value = parseFloat(amount) || 0;
                try {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: this.currency || 'USD'
                    }).format(value);
                } catch (e) {
                    // Fallback if currency is invalid
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(value);
                }
            },
        }
    }
</script>
