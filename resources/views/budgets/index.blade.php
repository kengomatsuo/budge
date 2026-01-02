<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calculator class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.my_budgets') }}</h3>
            </div>

            <div class="p-6 bg-white/60 dark:bg-gray-800/90 backdrop-blur-xl rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50">
                <div class="flex justify-end mb-4">
                    <x-primary-button onclick="window.location='{{ route('budgets.create') }}'" class="inline-flex items-center">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        {{ __('messages.set_new_budget') }}
                    </x-primary-button>
                </div>

            <div>
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
                                                {{ auth()->user()->preferred_currency }} {{ number_format(convert_currency($budget->amount, $budget->currency, auth()->user()->preferred_currency), 2) }}
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <x-secondary-button type="button" onclick="window.location='{{ route('budgets.edit', $budget) }}'" class="inline-flex items-center px-2 py-1 text-blue-600 dark:text-blue-400">
                                            <span class="sm:hidden"><x-heroicon-o-pencil class="w-5 h-5" /></span>
                                            <span class="hidden sm:inline md:hidden">{{ __('messages.edit') }}</span>
                                            <span class="hidden md:inline-flex items-center"><x-heroicon-o-pencil class="w-5 h-5 mr-2" />{{ __('messages.edit') }}</span>
                                        </x-secondary-button>
                                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit" class="p-2">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </x-danger-button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('messages.total_spent') }}: {{ auth()->user()->preferred_currency }} {{ number_format(convert_currency($budget->spent, $budget->currency ?? ($budget->currency ?? 'IDR'), auth()->user()->preferred_currency), 2) }}
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
