<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <x-heroicon-o-chart-pie class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            {{ __('messages.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('dashboard.partials.quick-actions')

            @include('dashboard.partials.summary-cards')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('dashboard.partials.spending-by-category')
                @include('dashboard.partials.spending-trend')
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('dashboard.partials.i-owe-others')
                @include('dashboard.partials.budget-status')
            </div>

            <div class="grid grid-cols-1 gap-6">
                @include('dashboard.partials.recent-expenses')
            </div>
        </div>
    </div>
</x-app-layout>
