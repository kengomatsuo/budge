<div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 hover:shadow-md transition-shadow duration-200">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.recent_expenses') }}
    </h3>
    @php
        $recent = $recentExpenses instanceof \Illuminate\Support\Collection ? $recentExpenses->take(10) : collect($recentExpenses)->take(10);
    @endphp

    <div class="space-y-2 overflow-y-scroll max-h-[32rem]">
        @forelse($recent as $expense)
        <a href="{{ route('expenses.show', $expense) }}" class="flex justify-between items-center py-3 px-4 rounded-lg border border-transparent hover:border-primary-200/50 dark:hover:border-primary-700/50 hover:bg-primary-50/50 dark:hover:bg-primary-900/20 hover:shadow-sm transition-all duration-200 cursor-pointer group">
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors truncate">
                    {{ $expense->title }}
                    @if($expense->is_shared)
                        @if($expense->user_id !== auth()->id())
                            <span class="ml-1 text-xs text-primary-600 dark:text-primary-400 font-semibold">({{ __('messages.shared_member') ?? 'Member' }})</span>
                        @endif
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $expense->category->color ?? '#3B82F6' }}20; color: {{ $expense->category->color ?? '#3B82F6' }}">
                        {{ $expense->category->name }}
                    </span>
                    <span class="mx-1">•</span>
                    {{ $expense->expense_date->format('M d, Y') }}
                </div>
            </div>
            <div class="font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap ml-4 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors">
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
