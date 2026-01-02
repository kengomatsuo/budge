<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome Back</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <x-form-errors class="mb-4" />

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-primary-600 shadow-sm focus:ring-2 focus:ring-primary-500/30 dark:focus:ring-primary-600/30 transition-all duration-200" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-600 dark:text-primary-300 hover:text-primary-700 dark:hover:text-primary-200 transition-colors duration-200" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
                <x-heroicon-o-arrow-right class="w-5 h-5 ml-2" />
            </x-primary-button>
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="mt-6 text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Don't have an account?</span>
                <a class="ml-1 text-sm font-semibold text-primary-600 dark:text-primary-300 hover:text-primary-700 dark:hover:text-primary-200 transition-colors duration-200" href="{{ route('register') }}">
                    {{ __('Create one now') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
