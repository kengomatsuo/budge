<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.recent_expenses') }}
    </h3>
    <div class="space-y-3">
        @forelse($recentExpenses as $expense)
        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
            <div>
                <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $expense->title }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $expense->category->name }} • {{ $expense->expense_date->format('M d, Y') }}
                </div>
            </div>
            <div class="font-semibold text-gray-900 dark:text-gray-100">
                {{ auth()->user()->preferred_currency }} {{ number_format($expense->amount, 2) }}
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            {{ __('messages.no_expenses') }}
        </p>
        @endforelse
    </div>
</div>
