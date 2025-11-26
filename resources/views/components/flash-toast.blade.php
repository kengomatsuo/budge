@props([])

@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="fixed left-1/2 transform -translate-x-1/2 top-20 z-50">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center space-x-3 px-4 py-3 rounded shadow-lg border border-green-200 bg-white/95 text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-white">
            <div class="flex-shrink-0">
                <x-heroicon-o-check class="w-5 h-5" />
            </div>
            <div class="flex-1 text-sm font-medium">
                {{ session('success') }}
            </div>
            <div class="flex-shrink-0">
                <button @click="show = false" class="text-gray-600 dark:text-gray-200 p-1 rounded hover:bg-gray-100 dark:hover:bg-green-900/30">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@props([])

@php
    $success = session('success');
    $error = session('error');
    $message = $success ?? $error ?? null;
    $type = $success ? 'success' : ($error ? 'error' : null);
@endphp

@if($message)
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
     x-transition:enter="transform transition ease-out duration-300"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-x-0 top-20 z-50 flex justify-center pointer-events-none">

    <div class="max-w-sm w-full">
        <div class="rounded-lg shadow-lg overflow-hidden">
              <div class="p-4 flex items-start space-x-3"
                  :class="{ 'rounded-lg bg-green-950 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': '{{ $type }}' === 'success', 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': '{{ $type }}' === 'error' }">
                <div class="flex-shrink-0">
                    <svg x-show="'{{ $type }}' === 'success'" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="'{{ $type }}' === 'error'" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 text-sm font-medium">
                    <div class="text-sm">
                        {{ $message }}
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button @click="show = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
