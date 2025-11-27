<div class="mb-6 px-4 sm:px-0">
    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-4">
        <div class="gap-4 w-full sm:w-fit">
            <x-secondary-button onclick="window.location='{{ route('expenses.index') }}'" class="w-full sm:w-fit">
                <x-heroicon-o-eye class="w-5 h-5 mr-2" />
                {{ __('messages.view_all_expenses') }}
            </x-secondary-button>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-4">
            <x-secondary-button onclick="window.location='{{ route('budgets.create') }}'" class="w-full sm:w-fit">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-2" />
                {{ __('messages.set_budget') }}
            </x-secondary-button>

            <x-primary-button onclick="window.location='{{ route('expenses.create') }}'" class="inline-flex items-center w-full sm:w-fit">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                {{ __('messages.add_expense') }}
            </x-primary-button>
        </div>
    </div>
</div>
