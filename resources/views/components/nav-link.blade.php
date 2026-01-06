@props(['active'])

@php
$classes = ($active ?? false)
            ? 'pointer-events-none py-2 inline-flex items-center px-3 py-1.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-500 dark:to-primary-600 rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all duration-200'
            : 'h-full inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-700 dark:hover:text-primary-400 focus:outline-none ring-0 outline-none transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
