<x-layout>
    <x-slot name="title">Tambah Barang</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-xl p-8">

            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Grid 2 Kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Kode Sakter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Sakter</label>
                        <input type="text" name="kode_sakter"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Kode Register -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Register</label>
                        <input type="text" name="kode_register"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Nama Barang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Kode Barang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Merk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <input type="text" name="merek"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Tanggal Perolehan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Perolehan</label>
                        <input type="date" name="tgl_perolehan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Nilai Perolehan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" min="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
                        <input type="text" name="lokasi"
                            value="{{ old('lokasi', $barang->location->name ?? '') }}"
                            placeholder="Masukkan nama lokasi"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Custom File Input -->
                <div class="mb-4">
                    <div class="flex items-center gap-3">
                        <label for="foto_url" 
                            class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                            📂 Pilih File
                        </label>
                        <span id="file-chosen" class="text-sm text-gray-500">Belum ada file dipilih</span>
                    </div>

                    <input type="file" name="foto_url" id="foto_url" accept=".jpg,.jpeg,.png" 
                        class="hidden"
                        onchange="document.getElementById('file-chosen').textContent = this.files[0]?.name || 'Belum ada file dipilih'">

                    <span class="block text-xs text-gray-500 mt-2">
                        Format: jpg, jpeg, png. Maksimal 2MB.
                    </span>
                </div>

              <!-- Tombol -->
            <div class="mt-20 flex justify-end gap-3">
                <button type="submit"
                    class="flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <a href="{{ route('barang.index') }}"
                    class="flex items-center px-5 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            </div>
            </form>
        </div>
    </div>
</x-layout>
