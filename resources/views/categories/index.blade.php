<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.expense_categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success messages are shown as global toast in the layout --}}

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-end mb-4">
                    <x-primary-button onclick="window.location='{{ route('categories.create') }}'" class="inline-flex items-center">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        {{ __('messages.add_category') }}
                    </x-primary-button>
                </div>
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
                                        <x-primary-button type="button" onclick="window.location='{{ route('categories.edit', $category) }}'" class="inline-flex items-center px-3 py-2">
                                            <x-heroicon-o-pencil class="w-4 h-4 mr-1" />
                                            {{ __('messages.edit') }}
                                        </x-primary-button>
                                @if(!$category->is_default)
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="inline-flex items-center p-2">
                                        <x-heroicon-o-trash class="w-4 h-4" />
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
