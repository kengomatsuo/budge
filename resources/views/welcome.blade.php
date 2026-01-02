<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Expense Tracker - Modern Financial Management</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/20 dark:from-gray-900 dark:via-indigo-950/90 dark:to-purple-950/80">
            <!-- Animated Background Elements -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/20 dark:bg-primary-600/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-1/2 -left-40 w-96 h-96 bg-purple-400/20 dark:bg-purple-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute -bottom-40 right-1/3 w-80 h-80 bg-pink-400/20 dark:bg-pink-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
            </div>

            <div class="relative min-h-screen flex flex-col">
                <!-- Navigation -->
                <nav class="sticky top-0 z-50 bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center h-16">
                            <!-- Logo -->
                            <div class="flex items-center gap-3">
                                <x-application-logo class="block h-9 w-auto fill-current text-primary-600 dark:text-primary-400" />
                                <span class="text-xl font-bold bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400 bg-clip-text text-transparent">Expense Tracker</span>
                            </div>

                            <!-- Auth Links - Only show Dashboard when logged in -->
                            @auth
                                <div class="flex items-center gap-2">
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-500 dark:to-primary-600 shadow-sm hover:shadow-md hover:from-primary-700 hover:to-primary-800 dark:hover:from-primary-600 dark:hover:to-primary-700 transition-all duration-200 active:scale-95">
                                        Dashboard
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </nav>

                <!-- Hero Section -->
                <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
                    <div class="max-w-7xl w-full">
                        <!-- Hero Content -->
                        <div class="text-center mb-16">
                            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold mb-6">
                                <span class="bg-gradient-to-r from-primary-600 via-indigo-600 to-purple-600 dark:from-primary-400 dark:via-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                                    Smart Financial
                                </span>
                                <br>
                                <span class="text-gray-900 dark:text-gray-100">
                                    Management
                                </span>
                            </h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                                Track expenses, manage budgets, and gain financial insights with our modern, intuitive expense tracking platform.
                            </p>
                        </div>

                        <!-- Feature Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Track Expenses Card -->
                            <a href="{{ route('expenses.index') }}" class="group p-6 bg-white/60 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-sm hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 hover:-translate-y-2">
                                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 dark:from-indigo-400 dark:to-indigo-600 mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <x-heroicon-o-document-text class="w-7 h-7 text-white" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Track Expenses
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Quickly record and categorize all your expenses with detailed notes and history.
                                </p>
                            </a>

                            <!-- Attach Receipts Card -->
                            <a href="{{ route('expenses.create') }}" class="group p-6 bg-white/60 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-sm hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 hover:-translate-y-2">
                                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 dark:from-purple-400 dark:to-purple-600 mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <x-heroicon-o-photo class="w-7 h-7 text-white" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    Attach Receipts
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Upload photos or PDFs of receipts to keep proof with each expense.
                                </p>
                            </a>

                            <!-- Shared Expenses Card -->
                            <a href="{{ route('expenses.index') }}" class="group p-6 bg-white/60 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-sm hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 hover:-translate-y-2">
                                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-pink-700 dark:from-pink-400 dark:to-pink-600 mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <x-heroicon-o-users class="w-7 h-7 text-white" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                                    Shared Expenses
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Split bills with friends, create groups, and settle balances easily.
                                </p>
                            </a>

                            <!-- Budgets Card -->
                            <a href="{{ route('budgets.index') }}" class="group p-6 bg-white/60 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-sm hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 hover:-translate-y-2">
                                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 dark:from-emerald-400 dark:to-emerald-600 mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <x-heroicon-o-chart-pie class="w-7 h-7 text-white" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    Smart Budgets
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Set budgets, track progress, and manage multi-currency expenses.
                                </p>
                            </a>
                        </div>

                        <!-- CTA Section -->
                        @guest
                            <div class="mt-16 text-center">
                                <div class="inline-flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-500 dark:to-indigo-500 shadow-lg hover:shadow-2xl hover:from-primary-700 hover:to-indigo-700 dark:hover:from-primary-600 dark:hover:to-indigo-600 transition-all duration-200 active:scale-95">
                                        <span>Start Tracking Now</span>
                                        <x-heroicon-o-arrow-right class="w-5 h-5 ml-2" />
                                    </a>
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-semibold text-gray-700 dark:text-gray-300 bg-white/80 dark:bg-gray-800/95 backdrop-blur-sm border border-gray-300/70 dark:border-gray-600/70 shadow-sm hover:bg-white dark:hover:bg-gray-800 hover:shadow-md transition-all duration-200 active:scale-95">
                                        Sign In
                                    </a>
                                </div>
                            </div>
                        @endguest
                    </div>
                </main>

                <!-- Footer -->
                <footer class="py-8 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400 bg-clip-text text-transparent">Expense Tracker</span> — Modern financial management made simple
                    </p>
                </footer>
            </div>
        </div>
    </body>
</html>
