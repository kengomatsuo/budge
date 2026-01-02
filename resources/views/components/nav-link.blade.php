@props(['active'])

@php
$classes = ($active ?? false)
            ? 'pointer-events-none py-2 inline-flex items-center px-3 py-1.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-500 dark:to-indigo-600 rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200'
            : 'h-full inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-700 dark:hover:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
