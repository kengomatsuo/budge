<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.add_new_expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-6"
                        x-data="expenseForm(@js($categories))"
                        @@scan-receipt.window="scanReceipt($event.detail.file)"
                        @@selected-users-change.window="selectedUsers = $event.detail"
                        @@split-type-change.window="splitType = $event.detail"
                        novalidate>
                        @csrf

                        <x-form-errors />

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('messages.title') . ' *'" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" x-model="form.title" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <!-- Receipt Upload -->
                        <div x-data>
                            <x-file-upload />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('messages.description')" />
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Amount and Currency -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="amount" :value="__('messages.amount') . ' *'" />
                                <div class="relative">
                                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01"
                                        class="mt-1 block w-full"
                                        :value="old('amount')"
                                        x-model="grandTotal"
                                        x-bind:readonly="items.length > 0"
                                        x-bind:class="{'bg-gray-100 dark:bg-gray-700': items.length > 0}"
                                        required />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="items.length === 0">
                                        <button type="button" @click="addItem()" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-200 dark:hover:text-primary-100">
                                            {{ __('messages.add_item') }}
                                        </button>
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                            </div>
                            <div>
                                <x-input-label for="currency" :value="__('messages.currency') . ' *'" />
                                <select id="currency" name="currency" required x-model="form.currency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    @foreach($currencies as $code => $details)
                                        <option value="{{ $code }}">{{ $code }} ({{ $details['symbol'] }})</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                            </div>
                        </div>

                        <!-- Items Editor (Shown if items exist) -->
                        <div x-show="items.length > 0" x-transition>
                            <x-expense-items-editor />
                        </div>

                        <!-- Expense Date -->
                        <div>
                            <x-input-label for="expense_date" :value="__('messages.expense_date') . ' *'" />
                            <x-text-input id="expense_date" name="expense_date" type="date" max="{{ date('Y-m-d') }}" class="mt-1 block w-full" :value="old('expense_date', date('Y-m-d'))" x-model="form.date" required />
                            <x-input-error class="mt-2" :messages="$errors->get('expense_date')" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('messages.category') . ' *'" />
                            <select name="category_id" id="category_id" required x-model="form.category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
                                <option value="">{{ __('messages.category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <x-input-label for="payment_method" :value="__('messages.payment_method') . ' *'" />
                            <select name="payment_method" id="payment_method" x-model="form.payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
                                <option value="">{{ __('messages.payment_method') }}</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>{{ __('messages.debit_card') }}</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>{{ __('messages.credit_card') }}</option>
                                <option value="e_wallet" {{ old('payment_method') == 'e_wallet' ? 'selected' : '' }}>{{ __('messages.e_wallet') }}</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('messages.bank_transfer') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                        </div>

                        <!-- Split / Shared Expense -->
                        <div class="mt-4">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" id="is_shared" name="is_shared" value="1" x-model="isShared" class="rounded text-primary-600 shadow-sm focus:ring-primary-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.split_expense') }}</span>
                            </label>
                        </div>

                        <div x-show="isShared" x-cloak class="mt-4 space-y-6">
                            <x-shared-members :users="$users" :currentUser="auth()->user()" :selected="old('shared_users', [])" :expenseAmount="old('amount', 0)" :initialSplits="old('shared_splits', [])" />

                            <!-- Item Splitter (Shown if split type is items) -->
                            <x-item-splitter />

                            <x-input-error class="mt-2" :messages="$errors->get('shared_users')" />
                            <x-input-error class="mt-2" :messages="$errors->get('shared_splits')" />
                        </div>

                        <!-- Hidden OCR Data -->
                        <input type="hidden" name="ocr_data" :value="JSON.stringify(ocrData)">

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                            <x-secondary-button type="button" onclick="window.location='{{ route('expenses.index') }}'">
                                {{ __('messages.cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit" class="inline-flex items-center">
                                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                {{ __('messages.save') }}
                            </x-primary-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('ocr', {
                scanning: false
            });

            Alpine.data('expenseForm', (categories) => ({
                categories: categories,
                isShared: {{ old('is_shared') ? 'true' : 'false' }},
                splitType: 'equal',
                items: [],
                subtotal: 0,
                taxAmount: 0,
                serviceCharge: 0,
                discountAmount: 0,
                grandTotal: {{ old('amount', 0) }},
                selectedUsers: [],
                itemAssignments: [], // Array of objects {userId: qty} matching items index
                ocrData: null,
                form: {
                    title: '{{ old('title') }}',
                    date: '{{ old('expense_date', date('Y-m-d')) }}',
                    currency: '{{ old('currency', auth()->user()->preferred_currency) }}',
                    payment_method: '{{ old('payment_method') }}',
                    category_id: '{{ old('category_id') }}'
                },

                init() {
                    this.$watch('items', () => {
                        this.calculateTotals();
                        this.initAssignments();
                    });
                    this.$watch('taxAmount', () => this.calculateGrandTotal());
                    this.$watch('serviceCharge', () => this.calculateGrandTotal());
                    this.$watch('discountAmount', () => this.calculateGrandTotal());
                    this.$watch('grandTotal', (value) => {
                        window.dispatchEvent(new CustomEvent('expense-amount-change', { detail: value }));
                    });
                    this.$watch('form.currency', (value) => {
                        window.dispatchEvent(new CustomEvent('currency-change', { detail: value }));
                    });
                },

                addItem() {
                    this.items.push({
                        name: '',
                        quantity: 1,
                        unit_price: 0,
                        total_price: 0
                    });
                    this.initAssignments();
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.itemAssignments.splice(index, 1);
                    this.calculateTotals();
                },

                calculateItemTotal(index) {
                    const item = this.items[index];
                    item.total_price = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                    this.calculateTotals();
                },

                calculateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (parseFloat(item.total_price) || 0), 0);
                    this.calculateGrandTotal();
                },

                calculateGrandTotal() {
                    if (this.items.length > 0) {
                        this.grandTotal = (
                            parseFloat(this.subtotal) +
                            parseFloat(this.taxAmount || 0) +
                            parseFloat(this.serviceCharge || 0) -
                            parseFloat(this.discountAmount || 0)
                        ).toFixed(2);
                    }
                },

                initAssignments() {
                    // Ensure itemAssignments matches items length
                    while (this.itemAssignments.length < this.items.length) {
                        this.itemAssignments.push({});
                    }
                    while (this.itemAssignments.length > this.items.length) {
                        this.itemAssignments.pop();
                    }
                },

                calculateAssignedQty(itemIndex) {
                    const assignments = this.itemAssignments[itemIndex] || {};
                    return Object.values(assignments).reduce((sum, qty) => sum + (parseFloat(qty) || 0), 0);
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: this.form.currency
                    }).format(amount || 0);
                },

                async scanReceipt(file) {
                    if (!file) return;

                    Alpine.store('ocr').scanning = true;
                    const formData = new FormData();
                    formData.append('receipt', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch('{{ route('expenses.ocr') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            const data = result.data;
                            this.ocrData = data;

                            // Populate form
                            if (data.merchant_name) this.form.title = data.merchant_name;
                            if (data.description) this.form.description = data.description;
                            if (data.date) this.form.date = data.date;
                            if (data.currency) this.form.currency = data.currency;

                            // Populate Payment Method
                            if (data.payment_method) {
                                const validMethods = ['cash', 'debit_card', 'credit_card', 'e_wallet', 'bank_transfer'];
                                if (validMethods.includes(data.payment_method)) {
                                    this.form.payment_method = data.payment_method;
                                }
                            }

                            // Populate Category
                            if (data.category_guess) {
                                const guess = data.category_guess.toLowerCase();
                                const match = this.categories.find(c => c.name.toLowerCase() === guess);
                                if (match) {
                                    this.form.category_id = match.id;
                                }
                            }

                            // Populate items
                            if (data.items && Array.isArray(data.items)) {
                                this.items = data.items.map(item => ({
                                    name: item.name,
                                    quantity: parseFloat(item.quantity) || 1,
                                    unit_price: parseFloat(item.unit_price) || 0,
                                    total_price: parseFloat(item.total_price) || 0
                                }));
                            }

                            // Populate totals
                            this.subtotal = parseFloat(data.total_amount) || 0; // Fallback if calc fails
                            this.taxAmount = parseFloat(data.tax_amount) || 0;
                            this.serviceCharge = parseFloat(data.service_charge) || 0;
                            this.discountAmount = parseFloat(data.discount_amount) || 0;

                            // Recalculate to be safe
                            if (this.items.length > 0) {
                                this.calculateTotals();
                            } else {
                                this.grandTotal = this.subtotal.toFixed(2);
                            }
                        } else {
                            alert('{{ __('messages.ocr_failed') }}' + (result.message ? ': ' + result.message : ''));
                        }
                    } catch (error) {
                        console.error('Error scanning receipt:', error);
                        alert('{{ __('messages.ocr_failed') }}');
                    } finally {
                        Alpine.store('ocr').scanning = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
