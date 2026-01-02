@props(['items', 'total' => null, 'title' => __('messages.spending_by_category'), 'color' => '#3B82F6'])

@php
    // Normalize and sort items by `total` descending. Support Collections and arrays/iterables.
    if ($items instanceof \Illuminate\Support\Collection) {
        $items = $items->sortByDesc('total')->values();
    } elseif (is_array($items) || $items instanceof Traversable) {
        $items = collect($items)->sortByDesc(function ($c) {
            return $c->total ?? ($c['total'] ?? 0);
        })->values();
    }

    $total = $total ?? ($items instanceof \Illuminate\Support\Collection ? $items->sum('total') : 0);
@endphp

<div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 hover:shadow-md transition-shadow duration-200">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-5">{{ $title }}</h3>
    <div class="space-y-4">
        @forelse($items as $category)
        <div class="group">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">{{ $category->name }}</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->preferred_currency }} {{ number_format($category->total, 2) }}</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full transition-all duration-500 ease-out group-hover:brightness-110" style="width: {{ $total ? ($category->total / $total) * 100 : 0 }}%; background-color: {{ $category->color ?? $color }}"></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('messages.no_expenses') }}</p>
        @endforelse
    </div>
</div>
