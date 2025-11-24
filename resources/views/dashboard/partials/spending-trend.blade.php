<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.spending_trend') }}
    </h3>
    <div class="h-64 flex items-end justify-around space-x-2">
        @php $maxAmount = $spendingTrend->max('total') ?? 1; @endphp
        @forelse($spendingTrend as $day)
        <div class="flex flex-col items-center flex-1">
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                {{ number_format($day->total, 0) }}
            </div>
            <div class="w-full bg-indigo-600 dark:bg-indigo-500 rounded-t" style="height: {{ ($day->total / $maxAmount) * 200 }}px"></div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex flex-col lg:flex-row lg:space-x-1 items-center justify-center">
                <span>{{ \Carbon\Carbon::parse($day->date)->format('M') }}</span>
                <span class="lg:inline block">{{ \Carbon\Carbon::parse($day->date)->format('d') }}</span>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center w-full py-4">
            {{ __('messages.no_expenses') }}
        </p>
        @endforelse
    </div>
</div>
