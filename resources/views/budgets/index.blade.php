<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.my_budgets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="p-4 sm:p-8 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="mb-6">
                <x-primary-button onclick="window.location='{{ route('budgets.create') }}'">
                    {{ __('messages.set_new_budget') }}
                </x-primary-button>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    @if($budgets->count() > 0)
                    <div class="space-y-6">
                        @foreach($budgets as $budget)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $budget->category->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($budget->period_type) }} •
                                        {{ $budget->start_date->format('M d, Y') }} -
                                        {{ $budget->end_date ? $budget->end_date->format('M d, Y') : __('messages.no_end_date') }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.budget_amount') }}</div>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ auth()->user()->preferred_currency }} {{ number_format($budget->amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('budgets.edit', $budget) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit" class="p-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </x-danger-button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('messages.total_spent') }}: {{ auth()->user()->preferred_currency }} {{ number_format($budget->spent, 2) }}
                                    </span>
                                    <span class="text-sm font-medium">
                                        <span class="px-2 py-1 rounded text-white {{ $budget->status === 'over_budget' ? 'bg-red-600' : ($budget->status === 'warning' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                            {{ number_format($budget->percentage, 1) }}% • {{ __('messages.' . $budget->status) }}
                                        </span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-600">
                                    <div class="h-4 rounded-full {{ $budget->status === 'over_budget' ? 'bg-red-600' : ($budget->status === 'warning' ? 'bg-yellow-500' : 'bg-green-500') }}"
                                         style="width: {{ min($budget->percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('messages.no_budgets') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
