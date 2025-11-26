<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.edit_expense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ isShared: {{ old('is_shared', $expense->is_shared) ? 'true' : 'false' }} }" novalidate>
                        @csrf
                        @method('PUT')

                        <x-form-errors />

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.expense_title') }} *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $expense->title) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.amount') }} *</label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $expense->amount) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.currency') }} *</label>
                            <select name="currency" id="currency" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="USD" {{ old('currency', $expense->currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency', $expense->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="IDR" {{ old('currency', $expense->currency) === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                <option value="JPY" {{ old('currency', $expense->currency) === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                            </select>
                            @error('currency')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }} *</label>
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">{{ __('messages.category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.expense_date') }} *</label>
                            <input type="date" name="expense_date" id="expense_date" max="{{ date('Y-m-d') }}" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('expense_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.payment_method') }} *</label>
                            <select name="payment_method" id="payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                                <option value="debit_card" {{ old('payment_method', $expense->payment_method) == 'debit_card' ? 'selected' : '' }}>{{ __('messages.debit_card') }}</option>
                                <option value="credit_card" {{ old('payment_method', $expense->payment_method) == 'credit_card' ? 'selected' : '' }}>{{ __('messages.credit_card') }}</option>
                                <option value="e_wallet" {{ old('payment_method', $expense->payment_method) == 'e_wallet' ? 'selected' : '' }}>{{ __('messages.e_wallet') }}</option>
                            </select>
                            @error('payment_method')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.description') }}</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" id="is_shared" name="is_shared" value="1" x-model="isShared" class="rounded text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_shared', $expense->is_shared) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.split_expense') }}</span>
                            </label>
                        </div>

                        <div x-show="isShared" x-cloak class="mt-4">
                            <x-shared-members :users="$users" :selected="old('shared_users', $selected ?? [])" :expenseAmount="old('amount', $expense->amount)" :initialSplits="old('shared_splits', [])" />
                            <x-input-error class="mt-2" :messages="$errors->get('shared_users')" />
                            <x-input-error class="mt-2" :messages="$errors->get('shared_splits')" />
                        </div>

                        <x-file-upload :existing-file="$expense->files->first() ?? null" :file-name="$expense->files->first()?->file_name ?? ''" />
                        <div class="flex justify-between">
                            <x-danger-button type="button" onclick="if(confirm('{{ __('messages.confirm_delete') }}')) { document.getElementById('delete-form').submit(); }">
                                <span class="sm:hidden"><x-heroicon-o-trash class="w-5 h-5" /></span>
                                <span class="hidden sm:inline md:hidden">{{ __('messages.delete') }}</span>
                                <span class="hidden md:inline-flex items-center"><x-heroicon-o-trash class="w-5 h-5 mr-2" />{{ __('messages.delete') }}</span>
                            </x-danger-button>
                            <div class="flex space-x-3">
                                <x-secondary-button type="button" onclick="window.location='{{ route('expenses.index') }}'">
                                    <span class="sm:hidden"><x-heroicon-o-x-mark class="w-5 h-5" /></span>
                                    <span class="hidden sm:inline md:hidden">{{ __('messages.cancel') }}</span>
                                    <span class="hidden md:inline-flex items-center"><x-heroicon-o-x-mark class="w-5 h-5 mr-2" />{{ __('messages.cancel') }}</span>
                                </x-secondary-button>
                                <x-primary-button type="submit" class="inline-flex items-center">
                                    <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                    {{ __('messages.update') }}
                                </x-primary-button>
                            </div>
                        </div>

                    </form>

                    <form id="delete-form" method="POST" action="{{ route('expenses.destroy', $expense) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
