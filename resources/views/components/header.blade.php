<!-- Header Desktop -->
<header class="hidden md:flex items-center justify-between bg-white shadow px-4 py-2">
  <div class="flex items-center gap-4">
    <!-- Tombol Toggle Sidebar -->
    <button 
      @click="sidebarOpen = !sidebarOpen" 
      class="p-2 rounded hover:bg-gray-200">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Judul Halaman -->
    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
      {{ $slot }}
    </h1>
  </div>
</header>
