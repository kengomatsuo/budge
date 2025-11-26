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
                            <x-primary-button type="button" onclick="window.location='{{ route('expenses.edit', $expense) }}'" class="inline-flex items-center">
                                <x-heroicon-o-pencil class="w-5 h-5 mr-2" />
                                {{ __('messages.edit') }}
                            </x-primary-button>
                            <x-secondary-button type="button" onclick="window.location='{{ route('expenses.index') }}'">
                                <span class="sm:hidden"><x-heroicon-o-arrow-left class="w-5 h-5" /></span>
                                <span class="hidden sm:inline md:hidden">{{ __('messages.back') }}</span>
                                <span class="hidden md:inline-flex items-center"><x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />{{ __('messages.back') }}</span>
                            </x-secondary-button>
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

                            @if($expense->is_shared)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.split_expense') }}</label>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ __('messages.shared') ?? 'Shared' }}</p>
                                @if($expense->sharedMembers->count() > 0)
                                <ul class="mt-2 space-y-2">
                                    @foreach($expense->sharedMembers as $member)
                                    <li class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $member->user->name ?? 'User #' . $member->user_id }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $member->user->email ?? '' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold">{{ auth()->user()->preferred_currency }} {{ number_format($member->split_amount, 2) }}</div>
                                            <div class="text-sm {{ $member->is_paid ? 'text-green-600' : 'text-yellow-600' }}">{{ $member->is_paid ? 'Paid' : 'Unpaid' }}</div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endif

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
                                    <x-heroicon-o-document-text class="mx-auto h-16 w-16 text-gray-400" />
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $file->file_name }}</p>
                                </div>
                                @endif

                                <a href="{{ asset('storage/' . $file->file_path) }}" download="{{ $file->file_name }}" class="mt-3 w-full text-center inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                                    {{ __('messages.download') }}
                                </a>
                            </div>
                            @else
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-700">
                                <x-heroicon-o-document class="mx-auto h-16 w-16 text-gray-400" />
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
