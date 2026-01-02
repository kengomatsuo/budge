<div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 hover:shadow-md transition-shadow duration-200">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-5">
        {{ __('messages.budget_status') }}
    </h3>
    <div class="space-y-4 max-h-[32rem] overflow-y-auto">
        @php
            // Normalize to a collection, sort by severity and percentage, then limit to 10
            $severityMap = ['over_budget' => 3, 'warning' => 2];
            $budgets = $budgetStatus instanceof \Illuminate\Support\Collection
                ? $budgetStatus
                : collect($budgetStatus);

            $sortedBudgets = $budgets->sortByDesc(function ($b) use ($severityMap) {
                $sev = $severityMap[$b->status] ?? 1;
                return ($sev * 1000) + (float) ($b->percentage ?? 0);
            })->values();
        @endphp

        @forelse($sortedBudgets as $budget)
        <div class="group">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">
                    {{ $budget->category->name }}
                </span>
                <span class="text-sm font-semibold @if($budget->status === 'over_budget') text-red-600 dark:text-red-400 @elseif($budget->status === 'warning') text-yellow-600 dark:text-yellow-400 @else text-green-600 dark:text-green-400 @endif">
                    {{ number_format($budget->percentage, 1) }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full transition-all duration-500 ease-out group-hover:brightness-110 @if($budget->status === 'over_budget') bg-red-600 dark:bg-red-500 @elseif($budget->status === 'warning') bg-yellow-500 dark:bg-yellow-400 @else bg-green-600 dark:bg-green-500 @endif"
                     style="width: {{ min($budget->percentage, 100) }}%"></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            {{ __('messages.no_budgets') }}
        </p>
        @endforelse
    </div>
</div>
