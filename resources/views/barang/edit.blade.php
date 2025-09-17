<x-layout>
    <x-slot name="title">Edit Barang</x-slot>

    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 px-4 mt-8">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-3xl border border-gray-200">
            
            <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Grid 2 kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Satker</label>
                        <input type="text" name="kode_sakter" value="{{ $barang->kode_sakter ?? '' }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang" value="{{ $barang->kode_barang }}" required
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}" required
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <input type="text" name="merek" value="{{ $barang->merek }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Perolehan</label>
                        <input type="date" name="tgl_perolehan" value="{{ $barang->tgl_perolehan }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi" 
                                class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="Baik" {{ $barang->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ $barang->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ $barang->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="Hilang" {{ $barang->kondisi == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $barang->location->name ?? '') }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" value="{{ $barang->nilai_perolehan }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>

                <!-- Foto barang -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Foto Barang</label>
                    @if($barang->foto_url)
                        <div class="mb-3">
                            <img src="{{ asset('storage/'.$barang->foto_url) }}" alt="Foto Barang" 
                                 class="w-28 h-28 object-cover rounded-lg border border-gray-300 shadow-sm">
                        </div>
                    @endif

                    <!-- Custom file input -->
                    <div class="flex items-center gap-3">
                        <label for="foto_url" 
                               class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                            📂 Pilih File
                        </label>
                        <span id="file-chosen" class="text-sm text-gray-500">Belum ada file dipilih</span>
                    </div>
                    <input type="file" name="foto_url" id="foto_url" accept=".jpg,.jpeg,.png" class="hidden" onchange="document.getElementById('file-chosen').textContent = this.files[0]?.name || 'Belum ada file dipilih'">

                    <span class="block text-xs text-gray-500 mt-2">Format: jpg, jpeg, png. Maksimal 2MB.</span>
                </div>

                <!-- Tombol -->
                <div class="pt-6 flex gap-3 justify-end">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                        💾 Simpan
                    </button>
                    <a href="{{ route('barang.index') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium shadow hover:bg-gray-300 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script kecil untuk update nama file -->
    <script>
        const fileInput = document.getElementById('foto_url');
        const fileChosen = document.getElementById('file-chosen');

        fileInput.addEventListener('change', function(){
            fileChosen.textContent = this.files[0]?.name || 'Belum ada file dipilih';
        });
    </script>
</x-layout>
