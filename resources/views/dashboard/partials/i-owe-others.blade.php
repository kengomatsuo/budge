<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        {{ __('messages.i_owe_others') }}
    </h3>

    @if($unpaidDebts->isNotEmpty())
        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Outstanding</div>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                {{ auth()->user()->preferred_currency }} {{ number_format($iOweOthers, 2) }}
            </div>
        </div>

        <div class="space-y-3 overflow-y-auto max-h-64">
            @foreach($unpaidDebts as $debt)
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
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
            <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                All settled up!
            </p>
        </div>
    @endif
</div>
