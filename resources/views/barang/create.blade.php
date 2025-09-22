<x-layout>
    <x-slot name="title">Tambah Barang</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-xl p-8">
            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- ===== Grid 2 Kolom ===== --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- ================== KATEGORI & NAMA BARANG ================== --}}
                    @php
                        // mapping "nama kategori => id" untuk lookup di Alpine
                        $catNameToId = $kategoris->pluck('id','name');
                    @endphp

                    {{-- Hidden field yang benar-benar dikirim ke server --}}
                    <input type="hidden" name="nama_barang" x-model="$store.cat.finalName">

                    {{-- KATEGORI (datalist: pilih atau ketik) --}}
                    <div x-data x-init="$store.cat.init()">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                        <input
                            type="text"
                            name="kategori"
                            list="categoryOptions"
                            x-model="$store.cat.kategori"
                            x-on:input="$store.cat.onKategoriChange($event.target.value)"
                            value="{{ old('kategori') }}"
                            placeholder="Ketik atau pilih kategori…"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <datalist id="categoryOptions">
                            @foreach ($kategoris as $c)
                                <option value="{{ $c->name }}"></option>
                            @endforeach
                        </datalist>
                        @error('kategori')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Jika memilih kategori yang sudah ada, Nama Barang hanya bisa dipilih dari daftar kategori tersebut.
                        </p>
                    </div>

                    {{-- NAMA BARANG: SELECT jika kategori existing; INPUT jika kategori baru --}}
                    <div x-data class="space-y-1">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>

                        {{-- SELECT (kategori existing) --}}
                        <select
                            x-show="$store.cat.isExisting"
                            x-cloak
                            x-model="$store.cat.finalName"
                            :required="$store.cat.isExisting"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Nama Barang</option>
                            <template x-for="nm in $store.cat.suggestions" :key="nm">
                                <option x-bind:value="nm" x-text="nm"></option>
                            </template>
                        </select>

                        {{-- INPUT (kategori baru) --}}
                        <input
                            x-show="!$store.cat.isExisting"
                            x-cloak
                            type="text"
                            placeholder="Ketik nama barang…"
                            x-model="$store.cat.finalName"
                            :required="!$store.cat.isExisting"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        @error('nama_barang')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500" x-show="$store.cat.isExisting" x-cloak>
                            Pilih salah satu nama barang yang tersedia pada kategori dipilih.
                        </p>
                        <p class="text-xs text-gray-500" x-show="!$store.cat.isExisting" x-cloak>
                            Kategori baru: silakan ketik nama barang.
                        </p>
                    </div>
                    {{-- ================== /KATEGORI & NAMA BARANG ================== --}}

                    {{-- Kode Sakter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Sakter</label>
                        <input type="text" name="kode_sakter"
                               value="{{ old('kode_sakter') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('kode_sakter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kode Register --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Register</label>
                        <input type="text" name="kode_register"
                               value="{{ old('kode_register') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('kode_register')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kode Barang --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang"
                               value="{{ old('kode_barang') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('kode_barang')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Merek --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <input type="text" name="merek"
                               value="{{ old('merek') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('merek')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tanggal Perolehan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Perolehan</label>
                        <input type="date" name="tgl_perolehan"
                               value="{{ old('tgl_perolehan') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('tgl_perolehan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nilai Perolehan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" min="1"
                               value="{{ old('nilai_perolehan') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('nilai_perolehan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Lokasi (boleh input manual) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
                        <input type="text" name="lokasi"
                               value="{{ old('lokasi') }}"
                               placeholder="Masukkan nama lokasi"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('lokasi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Kondisi</option>
                            @foreach (['Baik','Rusak Ringan','Rusak Berat'] as $k)
                                <option value="{{ $k }}" @selected(old('kondisi')===$k)>{{ $k }}</option>
                            @endforeach
                        </select>
                        @error('kondisi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Upload Foto (custom UI) --}}
                    <div class="md:col-span-2">
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Foto Barang (opsional)</label>
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
                        @error('foto_url')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="mt-6 flex justify-end gap-3">
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

    {{-- Alpine Store: logika dependent fields untuk Kategori & Nama Barang --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cat', {
                // state
                kategori:  @json(old('kategori','')),
                finalName: @json(old('nama_barang','')),

                // data dari server
                mapCatNameToId: @json($catNameToId),      // { "Elektronik": 1, ... }
                namesByCatId:   @json($namesByCategory),  // { "1": ["Laptop","Printer"], ... }

                suggestions: [],

                init() { this.refreshSuggestions(); },

                normalized(v){ return (v || '').trim().toLowerCase(); },

                get isExisting() {
                    const n = this.normalized(this.kategori);
                    for (const k in this.mapCatNameToId) {
                        if (this.normalized(k) === n) return true;
                    }
                    return false;
                },

                get catId() {
                    const n = this.normalized(this.kategori);
                    for (const k in this.mapCatNameToId) {
                        if (this.normalized(k) === n) return this.mapCatNameToId[k];
                    }
                    return null;
                },

                refreshSuggestions() {
                    const id = this.catId;
                    this.suggestions = id && this.namesByCatId[id] ? this.namesByCatId[id] : [];
                    // kosongkan nama kalau tidak ada dalam opsi saat kategori existing
                    if (this.isExisting && !this.suggestions.includes(this.finalName)) {
                        this.finalName = '';
                    }
                },

                onKategoriChange() {
                    this.refreshSuggestions();
                },
            });
        });
    </script>
</x-layout>
