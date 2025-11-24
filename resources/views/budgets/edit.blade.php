<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.edit_budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }} *</label>
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">{{ __('messages.category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.budget_amount') }} *</label>
                            <div class="mt-1 flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                    {{ auth()->user()->preferred_currency }}
                                </span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount', $budget->amount) }}" required
                                    class="flex-1 rounded-none rounded-r-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            </div>
                            @error('amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Period Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.period_type') }} *</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center mr-6">
                                    <input type="radio" name="period_type" value="daily" {{ old('period_type', $budget->period_type) == 'daily' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.daily') }}</span>
                                </label>
                                <label class="inline-flex items-center mr-6">
                                    <input type="radio" name="period_type" value="weekly" {{ old('period_type', $budget->period_type) == 'weekly' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.weekly') }}</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="period_type" value="monthly" {{ old('period_type', $budget->period_type) == 'monthly' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.monthly') }}</span>
                                </label>
                            </div>
                            @error('period_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.start_date') }} *</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $budget->start_date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('start_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div x-data="{ hasEndDate: {{ old('end_date', $budget->end_date) ? 'true' : 'false' }} }">
                            <label class="inline-flex items-center mb-2">
                                <input type="checkbox" x-model="hasEndDate" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('messages.end_date') }}</span>
                            </label>
                            <div x-show="hasEndDate">
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $budget->end_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            </div>
                            @error('end_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between">
                            <button type="button" onclick="if(confirm('{{ __('messages.confirm_delete') }}')) { document.getElementById('delete-form').submit(); }" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                                {{ __('messages.delete') }}
                            </button>
                            <div class="flex space-x-3">
                                <a href="{{ route('budgets.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded inline-block">
                                    {{ __('messages.cancel') }}
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                                    {{ __('messages.update') }}
                                </button>
                            </div>
                        </div>

                    </form>

                    <form id="delete-form" method="POST" action="{{ route('budgets.destroy', $budget) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
