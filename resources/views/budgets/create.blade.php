<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.set_new_budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('budgets.store') }}" class="space-y-6" novalidate>
                        @csrf
                        <x-form-errors class="mb-4" />

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }} *</label>
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
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

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.budget_amount') }} *</label>
                            <div class="mt-1 flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                    {{ auth()->user()->preferred_currency }}
                                </span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" required
                                    class="flex-1 rounded-none rounded-r-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
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
                                    <input type="radio" name="period_type" value="daily" {{ old('period_type') == 'daily' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.daily') }}</span>
                                </label>
                                <label class="inline-flex items-center mr-6">
                                    <input type="radio" name="period_type" value="weekly" {{ old('period_type') == 'weekly' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.weekly') }}</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="period_type" value="monthly" {{ old('period_type', 'monthly') == 'monthly' ? 'checked' : '' }} required
                                        class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.monthly') }}</span>
                                </label>
                            </div>
                            @error('period_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start & End Date -->
                        <div x-data="{ startDate: '{{ old('start_date', date('Y-m-d')) }}', hasEndDate: {{ old('end_date') ? 'true' : 'false' }} }">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.start_date') }} *</label>
                                <input x-model="startDate" min="{{ date('Y-m-d') }}" type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
                                @error('start_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Budgets must start today or later.</p>
                            </div>

                            <div class="mt-4">
                                <label class="inline-flex items-center mb-2">
                                    <input type="checkbox" x-model="hasEndDate" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('messages.end_date') }}</span>
                                </label>
                                <div x-show="hasEndDate">
                                    <input :min="startDate" type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave unchecked to create a budget with no end date.</p>
                                @error('end_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <x-secondary-button type="button" onclick="window.location='{{ route('budgets.index') }}'">{{ __('messages.cancel') }}</x-secondary-button>
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
