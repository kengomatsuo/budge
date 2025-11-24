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

<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $title }}</h3>
    <div class="space-y-3">
        @forelse($items as $category)
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->preferred_currency }} {{ number_format($category->total, 2) }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $total ? ($category->total / $total) * 100 : 0 }}% ; background-color: {{ $category->color ?? $color }}"></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('messages.no_expenses') }}</p>
        @endforelse
    </div>
</div>
