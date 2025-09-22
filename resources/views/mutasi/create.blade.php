<x-layout>
    <x-slot name="title">Mutasi Barang</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 md:px-8 py-5 border-b bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold tracking-tight">
                            Mutasi Lokasi • {{ $barang->nama_barang }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Pindahkan barang ke lokasi lain dan simpan sebagai riwayat.
                        </p>
                    </div>
                    <a href="{{ route('barang.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                        ← Kembali
                    </a>
                </div>
            </div>

            {{-- RINGKAS BARANG --}}
            <div class="px-6 md:px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-gray-200">
                        <div class="px-4 py-3 border-b bg-gray-50/60 rounded-t-xl">
                            <p class="text-sm font-medium text-gray-700">Ringkasan Barang</p>
                        </div>
                        <div class="p-4 text-sm">
                            <dl class="space-y-2">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Nama</dt>
                                    <dd class="font-medium truncate">{{ $barang->nama_barang }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Kode Barang</dt>
                                    <dd class="font-mono">{{ $barang->kode_barang }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Kode Register</dt>
                                    <dd class="font-mono break-all text-right">{{ $barang->kode_register }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Merek</dt>
                                    <dd>{{ $barang->merek }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-gray-500">Lokasi Saat Ini</dt>
                                    <dd>
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                            {{ optional($barang->location)->name ?? '—' }}
                                        </span>
                                    </dd>
                                </div>
                                @if(!empty($barang->kondisi))
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Kondisi</dt>
                                    <dd>{{ $barang->kondisi }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200">
                        <div class="px-4 py-3 border-b bg-gray-50/60 rounded-t-xl">
                            <p class="text-sm font-medium text-gray-700">Petunjuk</p>
                        </div>
                        <div class="p-4 text-sm text-gray-600 leading-relaxed">
                            Pilih <span class="font-medium">Lokasi Tujuan</span> dari daftar atau ketik nama lokasi baru.
                            Sistem akan membuat lokasi baru bila belum ada. Pastikan lokasi tujuan
                            <span class="font-medium">berbeda</span> dari lokasi saat ini.
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM MUTASI --}}
            <div class="px-6 md:px-8 pb-8">
                <form action="{{ route('mutasi.store', $barang) }}" method="POST" class="space-y-6"
                      x-data="{
                        toLocName: @js(old('lokasi','')),
                        currentName: @js(optional($barang->location)->name),
                        same: false,
                        checkSame(){ this.same = this.toLocName.trim() && this.currentName && this.toLocName.trim() === this.currentName.trim() }
                      }"
                      x-init="checkSame()">
                    @csrf

                    {{-- Lokasi (datalist: dropdown + boleh ketik) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Tujuan</label>
                        <input type="text"
                               name="lokasi"
                               list="locationOptions"
                               x-model="toLocName"
                               x-on:input.debounce.150ms="checkSame()"
                               placeholder="Ketik atau pilih lokasi…"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                               required>
                        <datalist id="locationOptions">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->name }}"></option>
                            @endforeach
                        </datalist>
                        <div class="mt-1 flex items-center gap-2 text-xs"
                             :class="same ? 'text-red-600' : 'text-gray-500'">
                            <span x-show="!same">Pilih dari daftar atau ketik nama baru.</span>
                            <span x-show="same" class="inline-flex items-center">
                                ⚠️ Lokasi tujuan tidak boleh sama dengan lokasi saat ini.
                            </span>
                        </div>
                        @error('lokasi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="tanggal"
                                   value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                   required>
                            @error('tanggal')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="catatan" rows="1"
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                      placeholder="Opsional">{{ old('catatan') }}</textarea>
                            @error('catatan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('barang.index') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                        <a href="{{ route('barang.index') }}"
                            class="flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            <i class="fas fa-exchange-alt mr-2"></i> Simpan Mutasi
                        </a>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layout>
