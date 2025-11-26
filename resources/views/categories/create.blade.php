<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.create_category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <form method="POST" action="{{ route('categories.store') }}" class="space-y-6" novalidate>
                        @csrf
                        <x-form-errors class="mb-4" />

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('messages.category_name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Icon -->
                        <div>
                            <x-input-label for="icon" value="{{ __('messages.icon') }} (Emoji)" />
                            <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" :value="old('icon')" placeholder="📝" maxlength="10" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Common: 🍔 🚗 🛍️ 🎬 🏥 💡 📚 🏠 💅 📝</p>
                            <x-input-error class="mt-2" :messages="$errors->get('icon')" />
                        </div>

                        <!-- Color -->
                        <div>
                            <x-input-label for="color" :value="__('messages.color')" />
                            <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}"
                                class="mt-1 block h-10 w-20 rounded-md border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <x-input-error class="mt-2" :messages="$errors->get('color')" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4">
                            <x-primary-button class="inline-flex items-center">
                                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                {{ __('messages.save') }}
                            </x-primary-button>
                            <x-secondary-button type="button" onclick="window.location='{{ route('categories.index') }}'">
                                {{ __('messages.cancel') }}
                            </x-secondary-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
