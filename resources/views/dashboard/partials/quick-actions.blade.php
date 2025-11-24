<div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
    <div class="flex flex-wrap gap-4">
        <x-primary-button onclick="window.location='{{ route('expenses.create') }}'">
            {{ __('messages.add_expense') }}
        </x-primary-button>

        <x-secondary-button onclick="window.location='{{ route('budgets.create') }}'">
            {{ __('messages.set_budget') }}
        </x-secondary-button>

        <x-secondary-button onclick="window.location='{{ route('expenses.index') }}'">
            {{ __('messages.view_all_expenses') }}
        </x-secondary-button>
    </div>
</div>
