<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('dashboard.partials.summary-cards')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('dashboard.partials.spending-by-category')
                @include('dashboard.partials.spending-trend')
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('dashboard.partials.recent-expenses')
                @include('dashboard.partials.budget-status')
            </div>

            @include('dashboard.partials.quick-actions')
        </div>
    </div>
</x-app-layout>

