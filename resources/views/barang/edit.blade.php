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
                        <input type="text" name="kode_sakter" value="{{ old('kode_sakter', $barang->kode_sakter ?? '') }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                        @error('kode_sakter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                        @error('kode_barang')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                        @error('nama_barang')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <input type="text" name="merek" value="{{ old('merek', $barang->merek) }}" 
                               class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                        @error('merek')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Perolehan</label>
                        <input
                            type="date"
                            name="tgl_perolehan"
                            value="{{ old('tgl_perolehan', $barang->tgl_perolehan ? \Carbon\Carbon::parse($barang->tgl_perolehan)->format('Y-m-d') : '') }}"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition"
                        >
                        @error('tgl_perolehan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi" 
                                class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                            @foreach (['Baik','Rusak Ringan','Rusak Berat','Hilang'] as $kondisi)
                                <option value="{{ $kondisi }}" @selected(old('kondisi', $barang->kondisi) == $kondisi)>
                                    {{ $kondisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('kondisi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nilai Perolehan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nilai Perolehan</label>
                        <!-- INPUT TAMPILAN -->
                        <input
                            type="text"
                            id="nilai_perolehan_format"
                            value="{{ old('nilai_perolehan') !== null 
                                        ? number_format(old('nilai_perolehan'), 0, ',', '.') 
                                        : number_format($barang->nilai_perolehan, 0, ',', '.') }}"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 transition"
                            oninput="formatRupiah(this, 'nilai_perolehan')"
                            onblur="formatRupiah(this, 'nilai_perolehan')"
                            onfocus="unformatForEdit(this)"
                            autocomplete="off"
                        >
                        <!-- INPUT RAW (untuk dikirim ke server) -->
                        <input 
                            type="hidden" 
                            name="nilai_perolehan" 
                            id="nilai_perolehan" 
                            value="{{ old('nilai_perolehan', (int)$barang->nilai_perolehan) }}"
                        >
                        @error('nilai_perolehan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>

                <!-- FOTO BARANG MULTIPLE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Foto Barang</label>

                    <!-- Foto Lama -->
                    @if(!empty($barang->foto_url) && is_array($barang->foto_url))
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            @foreach($barang->foto_url as $foto)
                                <img src="{{ asset('storage/'.$foto) }}" 
                                     onclick="openModal('{{ asset('storage/'.$foto) }}')" 
                                     class="w-28 h-28 object-cover rounded-lg border cursor-pointer hover:opacity-80">
                            @endforeach
                        </div>
                    @endif

                    <!-- Input Multiple -->
                    <div class="flex items-center gap-3">
                        <label for="foto_url" 
                               class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                            Pilih Foto
                        </label>
                        <span id="file-chosen" class="text-sm text-gray-500">Belum ada file dipilih</span>
                    </div>

                    <input type="file" name="foto_url[]" id="foto_url" multiple accept=".jpg,.jpeg,.png"
                           class="hidden">

                    <span class="block text-xs text-gray-500 mt-2">Anda dapat memilih lebih dari satu foto.</span>
                </div>

                <!-- Tombol -->
                <div class="pt-6 flex gap-3 justify-end">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                        Simpan
                    </button>
                    <a href="{{ route('barang.index') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium shadow hover:bg-gray-300 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Preview Foto -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-60 hidden justify-center items-center">
        <img id="modal-img" src="" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg">
    </div>

    <script>
        // update nama file
        document.getElementById('foto_url').addEventListener('change', function(){
            const names = Array.from(this.files).map(f => f.name).join(', ');
            document.getElementById('file-chosen').textContent = names || 'Belum ada file dipilih';
        });

        // modal preview
        function openModal(src) {
            document.getElementById('modal-img').src = src;
            document.getElementById('modal').classList.remove('hidden');
        }

        document.getElementById('modal').addEventListener('click', function(){
            this.classList.add('hidden');
        });
    </script>

    <script>
        // Format angka menjadi 1.234.567
        function formatRupiah(el, hiddenId) {
            let onlyNums = (el.value || '').replace(/[^0-9]/g, '');
            const hidden = document.getElementById(hiddenId);
            if (hidden) hidden.value = onlyNums;

            if (!onlyNums) {
                el.value = '';
                return;
            }

            el.value = onlyNums.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Saat fokus, hilangkan titik supaya mudah edit
        function unformatForEdit(el) {
            el.value = (el.value || '').replace(/[^0-9]/g, '');
        }

        // Format ulang saat halaman load (jika ada nilai awal)
        document.addEventListener('DOMContentLoaded', () => {
            const display = document.getElementById('nilai_perolehan_format');
            const hidden = document.getElementById('nilai_perolehan');

            if (display && hidden && hidden.value) {
                display.value = hidden.value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });
    </script>

</x-layout>
