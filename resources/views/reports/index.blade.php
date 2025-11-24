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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-4">
                    <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>{{ __('messages.this_week') }}</option>
                        <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>{{ __('messages.this_month') }}</option>
                        <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>{{ __('messages.this_year') }}</option>
                    </select>
                    <select name="category_id" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        <option value="">{{ __('messages.category') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
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
                        {{ auth()->user()->preferred_currency }} {{ number_format($largestExpense->amount, 2) }}
                        <div class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $largestExpense->title }}</div>
                        @else
                        -
                        @endif
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.spending_by_category') }}</h3>
                    <div class="space-y-3">
                        @foreach($spendingByCategory as $category)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->preferred_currency }} {{ number_format($category->total, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="h-2.5 rounded-full" style="width: {{ ($category->total / $totalSpent) * 100 }}%; background-color: {{ $category->color ?? '#3B82F6' }}"></div>
                            </div>
                        </div>
                        @endforeach
                        @if($spendingByCategory->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('messages.no_expenses') }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.spending_trend') }}</h3>
                    <div class="h-64 flex items-end justify-around space-x-2">
                        @php $maxAmount = $spendingTrend->max('total') ?? 1; @endphp
                        @foreach($spendingTrend as $day)
                        <div class="flex flex-col items-center flex-1">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ number_format($day->total, 0) }}</div>
                            <div class="w-full bg-blue-500 rounded-t" style="height: {{ ($day->total / $maxAmount) * 200 }}px"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</div>
                        </div>
                        @endforeach
                        @if($spendingTrend->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center w-full py-8">{{ __('messages.no_expenses') }}</p>
                        @endif
                    </div>
                </div>
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
