<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.my_expenses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success messages are shown as global toast in the layout --}}

            <!-- Filter Section -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                <form method="GET" action="{{ route('expenses.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4" x-data="{ date_from: '{{ request('date_from') }}', date_to: '{{ request('date_to') }}' }">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.from') }}</label>
                        <input type="date" name="date_from" x-model="date_from" :max="date_to || '{{ date('Y-m-d') }}'" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.to') }}</label>
                        <input type="date" name="date_to" x-model="date_to" :min="date_from" max="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.category') }}</label>
                        <select name="category_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <option value="">{{ __('messages.category') }}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.payment_method') }}</label>
                        <select name="payment_method" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <option value="">{{ __('messages.payment_method') }}</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                            <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>{{ __('messages.debit_card') }}</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>{{ __('messages.credit_card') }}</option>
                            <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>{{ __('messages.e_wallet') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                    </div>
                    <div class="md:col-span-5 flex flex-wrap gap-4">
                        <x-primary-button type="submit" class="inline-flex items-center">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 mr-2" />
                            {{ __('messages.apply_filters') }}
                        </x-primary-button>
                        <x-secondary-button type="button" onclick="window.location='{{ route('expenses.index') }}'">
                            <span class="sm:hidden"><x-heroicon-o-x-mark class="w-5 h-5" /></span>
                            <span class="hidden sm:inline md:hidden">{{ __('messages.clear_filters') }}</span>
                            <span class="hidden md:inline-flex items-center"><x-heroicon-o-x-mark class="w-5 h-5 mr-2" />{{ __('messages.clear_filters') }}</span>
                        </x-secondary-button>
                    </div>
                </form>
            </div>

            <!-- Expenses Table -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    <div class="mb-4 flex">
                        <x-primary-button type="button" onclick="window.location='{{ route('expenses.create') }}'" class="ml-auto inline-flex items-center">
                            <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                            {{ __('messages.add_expense') }}
                        </x-primary-button>
                    </div>

                    @if($expenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.title') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.amount') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.payment_method') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $expense->expense_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 min-w-72 text-sm font-medium text-gray-900 dark:text-gray-100 text-ellipsis break-words">
                                        {{ $expense->title }}
                                        @if($expense->files->count() > 0)
                                        <span class="text-gray-400">📎</span>
                                        @endif
                                        @if($expense->is_shared)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium text-white" style="background-color:#6B7280">Shared</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white" style="background-color: {{ $expense->category->color ?? '#3B82F6' }}">
                                            {{ $expense->category->icon ?? '' }} {{ $expense->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ auth()->user()->preferred_currency }} {{ number_format(convert_currency($expense->my_share, $expense->currency, auth()->user()->preferred_currency), 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $expense->payment_method ? __('messages.' . $expense->payment_method) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <x-secondary-button type="button" onclick="window.location='{{ route('expenses.show', $expense) }}'">
                                            <span class="inline-flex items-center"><x-heroicon-o-eye class="w-5 h-5" /></span>
                                        </x-secondary-button>
                                        <x-secondary-button type="button" onclick="window.location='{{ route('expenses.edit', $expense) }}'">
                                            <span class="inline-flex items-center"><x-heroicon-o-pencil-square class="w-5 h-5" /></span>
                                        </x-secondary-button>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit">
                                                <span class="inline-flex items-center"><x-heroicon-o-trash class="w-5 h-5" /></span>
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $expenses->onEachSide(1)->links() }}
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('messages.no_expenses') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
