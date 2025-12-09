<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="icon" href="{{ asset('storage/letter-s.ico') }}?v={{ time() }}" type="image/x-icon">
  <title>{{ $title ?? 'Dashboard' }}</title>
  <style>[x-cloak]{display:none!important}</style>
</head>

@stack('scripts')

<body 
  class="h-full" 
  x-data="{ sidebarOpen: window.matchMedia('(min-width: 768px)').matches, profileOpen: false }" 
  @keydown.escape.window="sidebarOpen = false; profileOpen = false">

<div class="min-h-full">
  <!-- Top bar untuk mobile -->
  <x-top-bar>{{ $title }}</x-top-bar>

  <!-- Sidebar versi mobile -->
  <x-mobile-drawer></x-mobile-drawer>

  <!-- Layout Desktop -->
  <div class="mx-auto flex max-w-7xl">
    
    <!-- Sidebar Desktop -->
    <aside 
      class="hidden md:fixed md:inset-y-0 md:flex md:flex-col md:bg-gray-800 transition-all duration-300" 
      :class="sidebarOpen ? 'md:w-64' : 'md:w-0 overflow-hidden'">
      <x-sidebar></x-sidebar>
    </aside>

    <!-- Kolom Utama -->
    <div 
      class="flex w-full flex-col transition-all duration-300" 
      :class="sidebarOpen ? 'md:pl-64' : 'md:pl-0'">
      
      <!-- Header Desktop -->
      {{-- <div class="hidden md:flex items-center justify-between bg-white shadow px-4 py-2">
        <button 
          @click="sidebarOpen = !sidebarOpen" 
          class="p-2 rounded hover:bg-gray-200">
          <!-- Hamburger icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div> --}}

      <!-- Header Mobile tetap menggunakan komponen asli -->
      <x-header class="md:hidden">{{ $title }}</x-header>

      <!-- Area Konten -->
      <main>
        {{ $slot }}
      </main>
    </div>
  </div>
</div>

{{-- CDN SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success') || session('error') || session('ok'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '{{ session('error') ? 'error' : 'success' }}',
                title: {!! json_encode(session('error') ? 'Terjadi Kesalahan' : 'Berhasil') !!},
                text: {!! json_encode(session('error') ?? session('success') ?? session('ok')) !!},
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
        });
    </script>
@endif


</body>
</html>
