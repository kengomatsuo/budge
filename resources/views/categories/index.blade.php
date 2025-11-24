<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.expense_categories') }}
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
                <x-primary-button onclick="window.location='{{ route('categories.create') }}'">
                    {{ __('messages.add_category') }}
                </x-primary-button>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    @if($categories->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categories as $category)
                        <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-5 flex flex-col justify-between hover:shadow focus-within:ring-2 focus-within:ring-blue-500 transition">
                            <div class="flex items-center space-x-3 mb-3">
                                <span class="text-2xl">{{ $category->icon ?? '📝' }}</span>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $category->expenses_count }} {{ __('messages.expenses') }}
                                        @if($category->is_default)
                                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ __('messages.default') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2 mt-auto">
                                <x-primary-button :href="route('categories.edit', $category)" class="inline-flex items-center px-3 py-2">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    {{ __('messages.edit') }}
                                </x-primary-button>
                                @if(!$category->is_default)
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="inline-flex items-center px-3 py-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        {{ __('messages.delete') }}
                                    </x-danger-button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">{{ __('messages.no_categories') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
