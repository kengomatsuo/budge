@props(['items', 'title' => __('messages.spending_trend_7_day'), 'colorClass' => 'bg-blue-500', 'startRight' => false])
@php
    $maxAmount = ($items instanceof \Illuminate\Support\Collection) ? $items->max('total') ?? 1 : 1;
@endphp

<div class="p-4 sm:p-8 flex flex-col bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $title }}</h3>

    <div class="flex items-end justify-around space-x-2 flex-1 overflow-x-scroll {{ $startRight ? 'js-scroll-right' : '' }}">
        @forelse($items as $day)
            <div class="flex flex-col items-center flex-1 h-full min-h-72">
                <!-- Amount label -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                    {{ number_format($day->total, 0) }}
                </div>

                <!-- Bar area - takes remaining space -->
                <div class="w-full flex-1 relative flex items-end">
                    <div class="w-full rounded-t {{ $colorClass }} absolute bottom-0"
                         style="height: {{ $maxAmount == 0 ? 0 : ($day->total / $maxAmount * 100) }}%">
                    </div>
                </div>

                <!-- Date label -->
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

@if($startRight)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-scroll-right').forEach(function(el) {
                // Move horizontal scrollbar to the rightmost position
                try { el.scrollLeft = el.scrollWidth; } catch(e) { /* noop */ }
            });
        });
    </script>
@endif
