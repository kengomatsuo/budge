<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-300 via-indigo-50/40 to-purple-50/30 dark:from-gray-950 dark:via-indigo-950/90 dark:to-purple-950/80">
            <!-- Animated Background Elements -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/25 dark:bg-primary-600/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-1/2 -left-40 w-96 h-96 bg-purple-400/25 dark:bg-purple-600/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute -bottom-40 right-1/3 w-80 h-80 bg-pink-400/25 dark:bg-pink-600/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
            </div>

            <div class="relative">
                @include('layouts.navigation')

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                    <x-flash-toast />
                </main>
            </div>
        </div>
    </body>
</html>
