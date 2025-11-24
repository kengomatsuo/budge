<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ __('messages.total_expenses_month') }}
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ auth()->user()->preferred_currency }} {{ number_format($totalExpensesMonth, 2) }}
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ __('messages.total_budget') }}
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ auth()->user()->preferred_currency }} {{ number_format($totalBudget, 2) }}
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ __('messages.budget_remaining') }}
        </div>
        <div class="mt-2 text-3xl font-bold @if($budgetRemaining >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
            {{ auth()->user()->preferred_currency }} {{ number_format($budgetRemaining, 2) }}
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ __('messages.expenses_today') }}
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ auth()->user()->preferred_currency }} {{ number_format($expensesToday, 2) }}
        </div>
    </div>
</div>
