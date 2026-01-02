@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/30 dark:focus:ring-indigo-600/30 rounded-lg shadow-sm hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500']) }}>
