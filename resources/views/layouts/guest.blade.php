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
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 p-4">

        <!-- Card Utama -->
        <div class="w-full max-w-5xl bg-white shadow-2xl rounded-3xl flex flex-col md:flex-row overflow-hidden">

            <!-- Bagian Kiri (Form Login/Register) -->
            <div class="w-full md:w-1/2 flex items-center justify-center p-8 md:p-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

            <!-- Bagian Kanan (Logo / Ilustrasi) -->
            <div class="hidden md:flex w-1/2 bg-gradient-to-br from-purple-600 to-indigo-700 items-center justify-center p-10">
                <img src="{{ asset('storage/gap2.png') }}" alt="Logo SIGAP" class="w-72 h-auto drop-shadow-lg">
            </div>
        </div>
    </div>
</body>
</html>
