<x-layout>
    <x-slot name="title">Tambah Barang</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-xl p-8">
            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input
                            type="text"
                            name="nama_barang"
                            id="nama_barang"
                            value="{{ old('nama_barang') }}"
                            list="namaBarangList"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        <datalist id="namaBarangList">
                            @foreach(($nameCodeMap ?? []) as $nama => $kode)
                                <option value="{{ $nama }}"></option>
                            @endforeach
                        </datalist>

                        @error('nama_barang')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Kode Barang
                        </label>
                        <input
                            type="text"
                            name="kode_barang"
                            id="kode_barang"
                            value="{{ old('kode_barang') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('kode_barang')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Sakter</label>
                        <input type="text" name="kode_sakter"
                               value="{{ old('kode_sakter') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('kode_sakter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kode Register</label>
                        <input type="text" name="kode_register"
                               value="{{ old('kode_register') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('kode_register')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nomor Seri --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Seri</label>
                        <input type="text" name="sn"
                               value="{{ old('sn') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('sn')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Merek --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <input type="text" name="merek"
                               value="{{ old('merek') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('merek')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tanggal Perolehan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Perolehan</label>
                        <input type="date" name="tgl_perolehan"
                               value="{{ old('tgl_perolehan') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('tgl_perolehan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nilai Perolehan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nilai Perolehan</label>

                        <!-- Input tampilan (formatted) -->
                        <input
                            type="text"
                            id="nilai_perolehan_format"
                            value="{{ old('nilai_perolehan') ? number_format(old('nilai_perolehan'), 0, ',', '.') : '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            oninput="formatRupiahNP(this)"
                        >

                        <!-- Input hidden yang dikirim ke server -->
                        <input
                            type="hidden"
                            name="nilai_perolehan"
                            id="nilai_perolehan"
                            value="{{ old('nilai_perolehan') }}"
                            required
                        >

                        @error('nilai_perolehan')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ showOther: {{ old('lokasi_baru') ? 'true' : 'false' }} }">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>

                        <select name="location_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @change="showOther = ($event.target.value === 'other')"
                                >
                            <option value="">Pilih Lokasi</option>

                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>
                                    {{ $loc->name }}
                                </option>
                            @endforeach

                            <option value="other" @selected(old('lokasi_baru'))>Lainnya…</option>
                        </select>
                        @error('location_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                        <div x-show="showOther" x-cloak class="mt-2">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi Baru</label>
                            <input type="text"
                                   name="lokasi_baru"
                                   value="{{ old('lokasi_baru') }}"
                                   placeholder="Tulis lokasi baru…"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('lokasi_baru')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih ini jika lokasi belum ada di daftar.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Kondisi</option>
                            @foreach (['Baik','Rusak Ringan','Rusak Berat'] as $k)
                                <option value="{{ $k }}" @selected(old('kondisi') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                        @error('kondisi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Foto Barang (opsional)
                            </label>

                            <div class="flex items-center gap-3">
                                <label for="foto_url"
                                    class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                                    Pilih File
                                </label>
                                <span id="file-chosen" class="text-sm text-gray-500">
                                    Belum ada file dipilih
                                </span>
                            </div>

                            <input type="file"
                                   name="foto_url[]"
                                   id="foto_url"
                                   accept=".jpg,.jpeg,.png"
                                   multiple
                                   class="hidden"
                                   onchange="
                                        const span = document.getElementById('file-chosen');
                                        if (!this.files.length) {
                                            span.textContent = 'Belum ada file dipilih';
                                        } else {
                                            const names = Array.from(this.files).map(f => f.name).join(', ');
                                            span.textContent = names;
                                        }
                                   ">

                            <span class="block text-xs text-gray-500 mt-2">
                                Format: jpg, jpeg, png. Maksimal 2MB.
                            </span>
                        </div>

                        @if($errors->has('foto_url') || $errors->has('foto_url.*'))
                            <p class="text-xs text-red-600">
                                {{ $errors->first('foto_url') ?: $errors->first('foto_url.*') }}
                            </p>
                        @endif
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="submit"
                            class="flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                        Simpan
                    </button>
                    <a href="{{ route('barang.index') }}"
                       class="flex items-center px-5 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameToCode = @json($nameCodeMap ?? []);

            const namaInput = document.getElementById('nama_barang');
            const kodeInput = document.getElementById('kode_barang');

            if (namaInput && kodeInput) {
                namaInput.addEventListener('change', function () {
                    const nama = this.value;
                    if (nameToCode[nama]) {
                        kodeInput.value = nameToCode[nama];
                    }
                });
            }
        });
    </script>

    <script>
        function formatRupiahNP(el) {
            let angka = el.value.replace(/\./g, '').replace(/,/g, '');
            document.getElementById('nilai_perolehan').value = angka;
            el.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>

</x-layout>
