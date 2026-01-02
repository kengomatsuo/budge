@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-2 focus:ring-primary-500/30 dark:focus:ring-primary-600/30 rounded-lg shadow-sm hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500']) }}>
