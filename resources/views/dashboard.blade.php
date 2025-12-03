<x-layout>
  <x-slot:title>{{ $title ?? 'Dashboard' }}</x-slot:title>

  <div class="px-8 py-6 space-y-8">

    <div>
      <h1 class="text-4xl font-bold text-gray-800 mb-1">
        Selamat Datang, {{ Auth::user()->name }}
      </h1>
      <p class="text-gray-600">Kelola dan pantau aset dengan sistem terpadu</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Aset -->
      <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-start hover:shadow-lg transition">
        <span class="text-gray-500 mb-2">Total Aset</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold text-gray-800">{{ $totalAset }}</span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
              <rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2"/>
              <rect x="8" y="8" width="8" height="8" rx="2" stroke-width="2"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Kondisi Baik -->
      <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-start border border-green-100 hover:shadow-lg transition">
        <span class="text-gray-500 mb-2">Kondisi Baik</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold text-green-700">{{ $asetBaik }}</span>
          <span class="bg-green-100 text-green-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Perlu Perbaikan -->
      <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-start border border-yellow-100 hover:shadow-lg transition">
        <span class="text-gray-500 mb-2">Perlu Perbaikan</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold text-yellow-600">{{ $perluPerbaikan }}</span>
          <span class="bg-yellow-100 text-yellow-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Nilai Total Aset -->
      <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-start hover:shadow-lg transition">
        <span class="text-gray-500 mb-2">Nilai Total Aset</span>
        <div class="flex items-center gap-3">
          <span class="text-2xl font-bold text-gray-800">
            Rp {{ number_format($nilaiTotalAset, 0, ',', '.') }}
          </span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
              <text x="6" y="18" font-size="14" fill="currentColor">$</text>
            </svg>
          </span>
        </div>
      </div>
    </div>

    <!-- Detail Kondisi & Aksi Cepat -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Kondisi Aset -->
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" 
               fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M3 17l6-6 4 4 8-8"/>
          </svg>
          <span class="text-lg font-semibold text-gray-800">Kondisi Aset</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center bg-green-100 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium text-green-700">Baik</span>
            <span class="ml-auto font-bold text-green-700">{{ $asetBaik }}</span>
          </div>
          <div class="flex items-center bg-yellow-50 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium text-yellow-700">Rusak Ringan</span>
            <span class="ml-auto font-bold text-yellow-700">{{ $asetRusakRingan }}</span>
          </div>
          <div class="flex items-center bg-red-100 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="12" cy="12" r="10" stroke-width="2" fill="none"/>
              <path stroke-width="2" d="M15 9l-6 6M9 9l6 6"/>
            </svg>
            <span class="font-medium text-red-700">Rusak Berat</span>
            <span class="ml-auto font-bold text-red-700">{{ $asetRusakBerat }}</span>
          </div>
        </div>
      </div>

      <!-- Aksi Cepat -->
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <span class="text-lg font-semibold text-gray-800 mb-4 block">Aksi Cepat</span>
        <div class="space-y-3">

          <!-- Tambah Aset Baru -->
          @admin
          <a href="{{ route('barang.create') }}" 
             class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition">
            <span class="bg-blue-100 text-blue-600 rounded-lg p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2"/>
                <rect x="8" y="8" width="8" height="8" rx="2" stroke-width="2"/>
              </svg>
            </span>
            <div>
              <span class="font-semibold text-blue-900">Tambah Aset Baru</span>
              <div class="text-gray-500 text-sm">Input data pengadaan</div>
            </div>
          </a>
          @endadmin

          <!-- Review Pemeliharaan -->
          <a href="{{ route('maintenance.index') }}" 
             class="flex items-center p-3 rounded-lg hover:bg-yellow-50 transition">
            <span class="bg-yellow-100 text-yellow-600 rounded-lg p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </span>
            <div>
              <span class="font-semibold text-yellow-900">Review Pemeliharaan</span>
              <div class="text-gray-500 text-sm">Periksa permintaan pending</div>
            </div>
          </a>

        </div>
      </div>

    </div>
  </div>
</x-layout>
