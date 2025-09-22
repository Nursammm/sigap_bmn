<x-layout>
  <x-slot:title>{{ $title ?? 'Dashboard' }}</x-slot:title>
  <div class="px-8 py-6">
    <h1 class="text-4xl font-bold mb-2">Selamat Datang, Admin BMN</h1>
    <p class="text-gray-600 mb-6">Kelola dan pantau aset BMN dengan sistem terpadu</p>
    <div class="grid grid-cols-4 gap-4 mb-6">
      <!-- Total Aset -->
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-start">
        <span class="text-gray-500 mb-2">Total Aset</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold">2</span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2" stroke="currentColor" fill="none"/><rect x="8" y="8" width="8" height="8" rx="2" stroke-width="2" stroke="currentColor" fill="none"/></svg>
          </span>
        </div>
        <span class="text-green-500 text-sm mt-2">+12% dari bulan lalu</span>
      </div>
      <!-- Kondisi Baik -->
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-start border border-green-100">
        <span class="text-gray-500 mb-2">Kondisi Baik</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold">2</span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </span>
        </div>
        <span class="text-green-500 text-sm mt-2">100% dari total</span>
      </div>
      <!-- Perlu Perbaikan -->
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-start border border-yellow-100">
        <span class="text-gray-500 mb-2">Perlu Perbaikan</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold">0</span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
        </div>
        <span class="text-red-500 text-sm mt-2">0% dari total</span>
      </div>
      <!-- Nilai Total Aset -->
      <div class="bg-white rounded-xl shadow p-6 flex flex-col items-start">
        <span class="text-gray-500 mb-2">Nilai Total Aset</span>
        <div class="flex items-center gap-3">
          <span class="text-3xl font-bold">Rp 17.500.000</span>
          <span class="bg-blue-100 text-blue-600 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><text x="6" y="18" font-size="14" fill="currentColor">$</text></svg>
          </span>
        </div>
        <span class="text-green-500 text-sm mt-2">+8.2% YoY</span>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <!-- Kondisi Aset -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M3 17l6-6 4 4 8-8"/></svg>
          <span class="text-xl font-semibold">Kondisi Aset</span>
        </div>
        <div class="space-y-2">
          <div class="flex items-center bg-green-100 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium text-green-700">Baik</span>
            <span class="ml-auto font-bold text-green-700">2</span>
          </div>
          <div class="flex items-center bg-yellow-50 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium text-yellow-700">Rusak Ringan</span>
            <span class="ml-auto font-bold text-yellow-700">0</span>
          </div>
          <div class="flex items-center bg-red-100 rounded-lg px-4 py-2">
            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path stroke="currentColor" stroke-width="2" d="M15 9l-6 6M9 9l6 6"/></svg>
            <span class="font-medium text-red-700">Rusak Berat</span>
            <span class="ml-auto font-bold text-red-700">0</span>
          </div>
        </div>
      </div>
      <!-- Aksi Cepat -->
      <div class="bg-white rounded-xl shadow p-6">
        <span class="text-xl font-semibold mb-4 block">Aksi Cepat</span>
        <div class="space-y-4">
          <div class="flex items-center">
            <span class="bg-blue-100 text-blue-600 rounded-lg p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2" stroke="currentColor" fill="none"/><rect x="8" y="8" width="8" height="8" rx="2" stroke-width="2" stroke="currentColor" fill="none"/></svg>
            </span>
            <div>
              <span class="font-semibold text-blue-900">Tambah Aset Baru</span>
              <div class="text-gray-500 text-sm">Input data pengadaan</div>
            </div>
          </div>
          <div class="flex items-center">
            <span class="bg-yellow-100 text-yellow-600 rounded-lg p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke="currentColor" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
              <span class="font-semibold text-yellow-900">Review Pemeliharaan</span>
              <div class="text-gray-500 text-sm">Periksa permintaan pending</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-layout>