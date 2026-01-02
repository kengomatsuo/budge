@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block mx-3 px-4 py-2.5 rounded-lg text-start text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-500 dark:to-indigo-600 shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200'
            : 'block mx-3 px-4 py-2.5 rounded-lg text-start text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-gray-800/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
