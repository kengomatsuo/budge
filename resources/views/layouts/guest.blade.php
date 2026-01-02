<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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

            <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
                <!-- Logo -->
                <div class="mb-8">
                    <a href="/" class="flex flex-col items-center gap-3 group">
                        <x-application-logo class="w-16 h-16 fill-current text-primary-600 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                        <span class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400 bg-clip-text text-transparent">
                            Expense Tracker
                        </span>
                    </a>
                </div>

                <!-- Card -->
                <div class="w-full sm:max-w-lg">
                    <div class="p-8 bg-white/60 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 transition-shadow duration-300 hover:shadow-2xl">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer Link -->
                <div class="mt-6 text-center">
                    <a href="/" class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
