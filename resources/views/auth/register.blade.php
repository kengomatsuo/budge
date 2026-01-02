<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Account</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Start tracking your expenses today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <x-form-errors class="mb-4" />

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
                <x-heroicon-o-check class="w-5 h-5 ml-2" />
            </x-primary-button>
        </div>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Already have an account?</span>
            <a class="ml-1 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-200" href="{{ route('login') }}">
                {{ __('Sign in instead') }}
            </a>
        </div>
    </form>
</x-guest-layout>
