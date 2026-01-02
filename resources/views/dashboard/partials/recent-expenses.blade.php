<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.recent_expenses') }}
    </h3>
    @php
        $recent = $recentExpenses instanceof \Illuminate\Support\Collection ? $recentExpenses->take(10) : collect($recentExpenses)->take(10);
    @endphp

    <div class="space-y-3 overflow-y-scroll max-h-[32rem]">
        @forelse($recent as $expense)
        <a href="{{ route('expenses.show', $expense) }}" class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer rounded px-2 -mx-2">
            <div>
                <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $expense->title }}
                    @if($expense->is_shared)
                        @if($expense->user_id !== auth()->id())
                            <span class="ml-1 text-xs text-indigo-600 dark:text-indigo-400">({{ __('messages.shared_member') ?? 'Member' }})</span>
                        @endif
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $expense->category->name }} • {{ $expense->expense_date->format('M d, Y') }}
                </div>
            </div>
            <div class="font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                {{ auth()->user()->preferred_currency }} {{ number_format(convert_currency($expense->my_share, $expense->currency, auth()->user()->preferred_currency), 2) }}
            </div>
        </a>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            {{ __('messages.no_expenses') }}
        </p>
        @endforelse
    </div>
</div>
