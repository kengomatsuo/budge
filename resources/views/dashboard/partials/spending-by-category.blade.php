<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.spending_by_category') }}
    </h3>
    <div class="space-y-3">
        @forelse($spendingByCategory as $category)
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $category->name }}
                </span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ auth()->user()->preferred_currency }} {{ number_format($category->total, 2) }}
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ ($category->total / $spendingByCategory->sum('total')) * 100 }}%"></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            {{ __('messages.no_expenses') }}
        </p>
        @endforelse
    </div>
</div>
