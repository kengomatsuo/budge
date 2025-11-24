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
                    <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

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
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.payment_method') }}</label>
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

                        <div x-data="{ fileName: '{{ $expense->files->first()?->original_filename ?? '' }}', imagePreview: null }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.receipt_image') }}</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
                                 @drop.prevent="
                                    let file = $event.dataTransfer.files[0];
                                    fileName = file?.name;
                                    if (file && file.type.startsWith('image/')) {
                                        let reader = new FileReader();
                                        reader.onload = (e) => imagePreview = e.target.result;
                                        reader.readAsDataURL(file);
                                    }
                                 "
                                 @dragover.prevent>
                                <div class="space-y-1 text-center" x-show="!imagePreview">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label for="receipt" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                                            <span>{{ __('messages.upload_new_file') }}</span>
                                            <input id="receipt" name="receipt" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf"
                                                @change="
                                                    fileName = $event.target.files[0]?.name;
                                                    let file = $event.target.files[0];
                                                    if (file && file.type.startsWith('image/')) {
                                                        let reader = new FileReader();
                                                        reader.onload = (e) => imagePreview = e.target.result;
                                                        reader.readAsDataURL(file);
                                                    }
                                                ">
                                        </label>
                                        <p class="pl-1">{{ __('messages.drag_drop') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.file_types') }}</p>
                                    <p x-show="fileName" x-text="fileName" class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-2"></p>
                                    @if($expense->files->first())
                                        @php
                                            $file = $expense->files->first();
                                            $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('storage/' . $file->file_path) }}" alt="{{ $expense->title }}" class="mt-2 max-h-64 mx-auto rounded border border-gray-300 dark:border-gray-600">
                                        @endif
                                    @endif
                                </div>

                                <div x-show="imagePreview" class="relative">
                                    <img :src="imagePreview" class="max-h-64 mx-auto rounded" alt="Preview">
                                    <button type="button" @click="imagePreview = null; fileName = '{{ $expense->files->first()?->file_name ?? '' }}'; document.getElementById('receipt').value = ''" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('receipt')" />
                        </div>                        <div class="flex justify-between">
                            <button type="button" onclick="if(confirm('{{ __('messages.confirm_delete') }}')) { document.getElementById('delete-form').submit(); }" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                                {{ __('messages.delete') }}
                            </button>
                            <div class="flex space-x-3">
                                <a href="{{ route('expenses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded inline-block">
                                    {{ __('messages.cancel') }}
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                                    {{ __('messages.update') }}
                                </button>
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
