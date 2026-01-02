<div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 hover:shadow-md transition-shadow duration-200">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-5">
        {{ __('messages.i_owe_others') }}
    </h3>

    @if($unpaidDebts->isNotEmpty())
        <div class="mb-5 pb-4 border-b border-gray-200/50 dark:border-gray-700/50">
            <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Total Outstanding</div>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                {{ auth()->user()->preferred_currency }} {{ number_format($iOweOthers, 2) }}
            </div>
        </div>

        <div class="space-y-2 overflow-y-auto max-h-64">
            @foreach($unpaidDebts as $debt)
                <div class="flex justify-between items-center py-3 px-4 rounded-lg hover:bg-red-50/50 dark:hover:bg-red-900/10 transition-colors duration-150 group">
                    <div class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">
                        {{ $debt->owner_name }}
                    </div>
                    <div class="font-semibold text-red-600 dark:text-red-400 whitespace-nowrap">
                        {{ auth()->user()->preferred_currency }} {{ number_format($debt->total_owed, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-5xl font-bold mb-2 text-green-600 dark:text-green-400">
                {{ auth()->user()->preferred_currency }} 0.00
            </div>
            <p class="text-sm font-medium text-green-600 dark:text-green-400 mt-2">
                All settled up!
            </p>
        </div>
    @endif
</div>
