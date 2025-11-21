<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/letter-s.ico') }}?v={{ time() }}" type="image/x-icon">
</head>
<body class="font-sans antialiased">
    <!-- Lapis 1: Background gambar layar penuh -->
    <div
        class="min-h-screen bg-no-repeat bg-cover bg-center"
        style="background-image: url('{{ asset('storage/bg.jpg') }}');"
    >
        <!-- (Opsional) overlay supaya konten tetap terbaca -->
        <div class="min-h-screen bg-black/30">
            <!-- Lapis 2: Konten -->
            <div class="min-h-screen flex items-center justify-center p-4">
                <div class="w-full max-w-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
