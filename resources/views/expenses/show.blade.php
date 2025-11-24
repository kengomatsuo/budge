<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.expense_details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $expense->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $expense->expense_date->format('F d, Y') }} •
                                <span class="capitalize">{{ str_replace('_', ' ', $expense->payment_method) }}</span>
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('expenses.edit', $expense) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded inline-block">
                                {{ __('messages.edit') }}
                            </a>
                            <a href="{{ route('expenses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded inline-block">
                                {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.amount') }}</label>
                                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->preferred_currency }} {{ number_format($expense->amount, 2) }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.category') }}</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded text-white text-base" style="background-color: {{ $expense->category->color ?? '#3B82F6' }}">
                                        {{ $expense->category->icon }} {{ $expense->category->name }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.payment_method') }}</label>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100 capitalize">
                                    {{ str_replace('_', ' ', $expense->payment_method) }}
                                </p>
                            </div>

                            @if($expense->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.description') }}</label>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $expense->description }}</p>
                            </div>
                            @endif
                        </div>

                        <div>
                            @if($expense->files->first())
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('messages.receipt_image') }}</label>
                                @php
                                    $file = $expense->files->first();
                                    $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $file->file_path) }}" alt="{{ $expense->title }}" class="w-full h-auto">
                                </div>
                                @elseif($extension === 'pdf')
                                <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-700">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $file->file_name }}</p>
                                </div>
                                @endif

                                <a href="{{ asset('storage/' . $file->file_path) }}" download="{{ $file->file_name }}" class="mt-3 block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded text-center">
                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ __('messages.download') }}
                                </a>
                            </div>
                            @else
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-700">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_receipt') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('messages.created') }}: {{ $expense->created_at->format('F d, Y h:i A') }}
                            @if($expense->updated_at->ne($expense->created_at))
                            <br>{{ __('messages.last_updated') }}: {{ $expense->updated_at->format('F d, Y h:i A') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
