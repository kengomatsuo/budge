<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.expense_reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-chart-pie class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.expense_reports') }}</h3>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ type: '{{ $type }}' }">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-4 items-end">
                    <!-- Type Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.report_type') }}</label>
                        <select name="type" x-model="type" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <option value="monthly">{{ __('messages.monthly') }}</option>
                            <option value="yearly">{{ __('messages.yearly') }}</option>
                            <option value="custom">{{ __('messages.custom_range') }}</option>
                        </select>
                    </div>

                    <!-- Year Selector -->
                    <div x-show="type === 'monthly' || type === 'yearly'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.year') }}</label>
                        <select name="year" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Month Selector -->
                    <div x-show="type === 'monthly'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.month') }}</label>
                        <select name="month" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Range -->
                    <div x-show="type === 'custom'" class="flex gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.start_date') }}</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.end_date') }}</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.category') }}</label>
                        <select name="category_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <option value="">{{ __('messages.all_categories') }}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-primary-button type="submit" class="mb-0.5">
                        {{ __('messages.apply_filters') }}
                    </x-primary-button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.total_spent') }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->preferred_currency }} {{ number_format($totalSpent, 2) }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.average_spending') }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->preferred_currency }} {{ number_format($averageSpending, 2) }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.largest_expense') }}</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">
                        @if($largestExpense)
                        {{ auth()->user()->preferred_currency }} {{ number_format($largestExpense->converted_amount ?? $largestExpense->amount ?? 0, 2) }}
                        <div class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $largestExpense->title }}</div>
                        @else
                        -
                        @endif
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-spending-by-category :items="$spendingByCategory" :total="$totalSpent" />

                @php
                    $chartTitle = match($type) {
                        'monthly' => __('messages.spending_trend') . ' (' . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . ')',
                        'yearly' => __('messages.spending_trend') . ' (' . $year . ')',
                        'custom' => __('messages.spending_trend') . ' (' . __('messages.custom_range') . ')',
                        default => __('messages.spending_trend')
                    };
                @endphp
                <x-spending-trend :items="$spendingTrend" :title="$chartTitle" :startRight="true" />
            </div>

            <!-- Top Categories -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.top_category') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.category') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.amount') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($spendingByCategory->take(5) as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-white" style="background-color: {{ $category->color ?? '#3B82F6' }}">
                                        {{ $category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->preferred_currency }} {{ number_format($category->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format(($category->total / $totalSpent) * 100, 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($spendingByCategory->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('messages.no_expenses') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
