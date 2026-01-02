<x-app-layout>
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
