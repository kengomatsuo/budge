<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.add_new_expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('messages.title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('messages.description')" />
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Amount and Currency -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="amount" :value="__('messages.amount')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                            </div>
                            <div>
                                <x-input-label for="currency" :value="__('messages.currency')" />
                                <select id="currency" name="currency" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="USD" {{ old('currency', auth()->user()->preferred_currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency', auth()->user()->preferred_currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="IDR" {{ old('currency', auth()->user()->preferred_currency) === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                    <option value="JPY" {{ old('currency', auth()->user()->preferred_currency) === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                            </div>
                        </div>

                        <!-- Expense Date -->
                        <div>
                            <x-input-label for="expense_date" :value="__('messages.expense_date')" />
                            <x-text-input id="expense_date" name="expense_date" type="date" max="{{ date('Y-m-d') }}" class="mt-1 block w-full" :value="old('expense_date', date('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('expense_date')" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('messages.category')" />
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">{{ __('messages.category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.payment_method') }}</label>
                            <select name="payment_method" id="payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">{{ __('messages.payment_method') }}</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>{{ __('messages.debit_card') }}</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>{{ __('messages.credit_card') }}</option>
                                <option value="e_wallet" {{ old('payment_method') == 'e_wallet' ? 'selected' : '' }}>{{ __('messages.e_wallet') }}</option>
                            </select>
                            @error('payment_method')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Receipt Upload -->
                        <x-file-upload />

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <x-secondary-button type="button" onclick="window.location='{{ route('expenses.index') }}'">
                                <span class="sm:hidden"><x-heroicon-o-x-mark class="w-5 h-5" /></span>
                                <span class="hidden sm:inline md:hidden">{{ __('messages.cancel') }}</span>
                                <span class="hidden md:inline-flex items-center"><x-heroicon-o-x-mark class="w-5 h-5 mr-2" />{{ __('messages.cancel') }}</span>
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
</x-app-layout>
