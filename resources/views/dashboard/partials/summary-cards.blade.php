<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="group p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-700/50 transition-all duration-200 hover:-translate-y-0.5">
        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
            {{ __('messages.total_expenses_month') }}
        </div>
        <div class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            {{ auth()->user()->preferred_currency }} {{ number_format($totalExpensesMonth, 2) }}
        </div>
    </div>

    <div class="group p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-700/50 transition-all duration-200 hover:-translate-y-0.5">
        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
            {{ __('messages.total_budget') }}
        </div>
        <div class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            {{ auth()->user()->preferred_currency }} {{ number_format($totalBudget, 2) }}
        </div>
    </div>

    <div class="group p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-700/50 transition-all duration-200 hover:-translate-y-0.5">
        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
            {{ __('messages.budget_remaining') }}
        </div>
        <div class="mt-3 text-3xl font-bold @if($budgetRemaining >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif transition-colors">
            {{ auth()->user()->preferred_currency }} {{ number_format($budgetRemaining, 2) }}
        </div>
    </div>

    <div class="group p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl shadow-sm hover:shadow-md border border-gray-200/50 dark:border-gray-700/50 transition-all duration-200 hover:-translate-y-0.5">
        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
            {{ __('messages.expenses_today') }}
        </div>
        <div class="mt-3 text-3xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            {{ auth()->user()->preferred_currency }} {{ number_format($expensesToday, 2) }}
        </div>
    </div>
</div>
