<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.edit_category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category_name') }} *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon -->
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.icon') }} (Emoji)</label>
                            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" placeholder="📝" maxlength="10"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Common: 🍔 🚗 🛍️ 🎬 🏥 💡 📚 🏠 💅 📝</p>
                            @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.color') }}</label>
                            <input type="color" name="color" id="color" value="{{ old('color', $category->color ?? '#3B82F6') }}"
                                class="mt-1 block h-10 w-20 rounded-md border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between">
                            <div>
                                @if(!$category->is_default)
                                <x-danger-button type="button" onclick="if(confirm('{{ __('messages.confirm_delete') }}')) { document.getElementById('delete-form').submit(); }">{{ __('messages.delete') }}</x-danger-button>
                                @endif
                            </div>
                            <div class="flex space-x-3">
                                <x-secondary-button type="button" onclick="window.location='{{ route('categories.index') }}'">{{ __('messages.cancel') }}</x-secondary-button>
                                <x-primary-button type="submit" class="inline-flex items-center">
                                    <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                    {{ __('messages.update') }}
                                </x-primary-button>
                            </div>
                        </div>

                    </form>

                    @if(!$category->is_default)
                    <form id="delete-form" method="POST" action="{{ route('categories.destroy', $category) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
