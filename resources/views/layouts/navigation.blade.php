<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105 duration-200">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('messages.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                        {{ __('messages.expenses') }}
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                        {{ __('messages.categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                        {{ __('messages.budgets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('messages.reports') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <!-- Language Switcher -->
                <form method="POST" action="{{ route('change.language') }}">
                    @csrf
                    <select name="language" onchange="this.form.submit()"
                        class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-gray-300/50 dark:border-gray-600/50 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/50 dark:focus:ring-indigo-600/50 rounded-lg shadow-sm text-sm py-1.5 px-3 transition-all duration-200 hover:bg-white/80 dark:hover:bg-gray-800/80">
                        <option value="en" {{ auth()->user()->preferred_language === 'en' ? 'selected' : '' }}>
                            {{ __('messages.english') }}</option>
                        <option value="id" {{ auth()->user()->preferred_language === 'id' ? 'selected' : '' }}>
                            {{ __('messages.indonesian') }}</option>
                        <option value="es" {{ auth()->user()->preferred_language === 'es' ? 'selected' : '' }}>
                            {{ __('messages.spanish') }}</option>
                        <option value="zh" {{ auth()->user()->preferred_language === 'zh' ? 'selected' : '' }}>
                            {{ __('messages.chinese') }}</option>
                        <option value="ko" {{ auth()->user()->preferred_language === 'ko' ? 'selected' : '' }}>
                            {{ __('messages.korean') }}</option>
                        <option value="ja" {{ auth()->user()->preferred_language === 'ja' ? 'selected' : '' }}>
                            {{ __('messages.japanese') }}</option>
                    </select>
                </form>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-300/50 dark:border-gray-600/50 hover:bg-white/80 dark:hover:bg-gray-800/80 hover:border-gray-400/50 dark:hover:border-gray-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200">
                            <span>{{ Auth::user()->name }}</span>
                            <x-heroicon-o-chevron-down class="ms-2 h-4 w-4 fill-current" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('messages.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('messages.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 dark:text-gray-400 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm hover:bg-white/80 dark:hover:bg-gray-800/80 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200">
                    <span :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"><x-heroicon-o-bars-3
                            class="h-6 w-6" /></span>
                    <span :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"><x-heroicon-o-x-mark
                            class="h-6 w-6" /></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-t border-gray-200/50 dark:border-gray-700/50">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('messages.dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                {{ __('messages.expenses') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                {{ __('messages.categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                {{ __('messages.budgets') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                {{ __('messages.reports') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-gray-200/50 dark:border-gray-700/50">
            <div class="px-4 mb-3">
                <div class="font-semibold text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <!-- Mobile Language Switcher -->
            <div class="px-4 mb-3">
                <form method="POST" action="{{ route('change.language') }}">
                    @csrf
                    <select name="language" onchange="this.form.submit()"
                        class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-gray-300/50 dark:border-gray-600/50 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/50 dark:focus:ring-indigo-600/50 rounded-lg shadow-sm text-sm py-2 px-3 transition-all duration-200">
                        <option value="en" {{ auth()->user()->preferred_language === 'en' ? 'selected' : '' }}>
                            {{ __('messages.english') }}</option>
                        <option value="id" {{ auth()->user()->preferred_language === 'id' ? 'selected' : '' }}>
                            {{ __('messages.indonesian') }}</option>
                        <option value="es" {{ auth()->user()->preferred_language === 'es' ? 'selected' : '' }}>
                            {{ __('messages.spanish') }}</option>
                        <option value="zh" {{ auth()->user()->preferred_language === 'zh' ? 'selected' : '' }}>
                            {{ __('messages.chinese') }}</option>
                        <option value="ko" {{ auth()->user()->preferred_language === 'ko' ? 'selected' : '' }}>
                            {{ __('messages.korean') }}</option>
                        <option value="ja" {{ auth()->user()->preferred_language === 'ja' ? 'selected' : '' }}>
                            {{ __('messages.japanese') }}</option>
                    </select>
                </form>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('messages.profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('messages.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
