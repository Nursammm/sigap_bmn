{{-- resources/views/mutasi/create.blade.php --}}
<x-layout>
    <x-slot name="title">Mutasi Barang</x-slot>

    @php
        $user    = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
    @endphp

    <div class="max-w-xl mx-auto mt-10">
        {{-- Kartu utama --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            {{-- Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">
                        {{ $isAdmin ? 'Mutasi Barang' : 'Ajukan Mutasi Barang' }}
                    </h1>
                    <p class="text-xs text-blue-100 mt-0.5">
                        Isi data berikut untuk memindahkan lokasi barang.
                    </p>
                </div>
                <span class="px-3 py-1 text-[11px] rounded-full bg-white/10 border border-white/30 uppercase tracking-wide">
                    {{ $isAdmin ? 'Admin' : 'Pengelola' }}
                </span>
            </div>

            {{-- Info barang --}}
            <div class="px-6 pt-5 pb-3 bg-gray-50 border-b border-gray-100 text-sm text-gray-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1">
                    <div>
                        <span class="font-semibold">Barang:</span>
                        <span>{{ $barang->nama_barang }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Kode Register:</span>
                        <span class="font-mono text-xs">{{ $barang->kode_register }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="font-semibold">Lokasi Saat Ini:</span>
                        <span>{{ optional($barang->location)->name ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form
                action="{{ $isAdmin
                    ? route('mutasi.store', $barang)        {{-- admin: mutasi langsung --}}
                    : route('mutasi.request', $barang)      {{-- pengelola: ajukan mutasi --}}
                }}"
                method="POST"
                class="px-6 pb-6 pt-4 space-y-4"
            >
                @csrf

                {{-- Lokasi tujuan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Tujuan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi') border-red-500 @enderror"
                        placeholder="Contoh: Gedung Utama Lt. 2"
                    >
                    @error('lokasi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Mutasi <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ old('tanggal', now()->toDateString()) }}"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal') border-red-500 @enderror"
                    >
                    @error('tanggal')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan
                    </label>
                    <textarea
                        name="catatan"
                        rows="3"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan') border-red-500 @enderror"
                        placeholder="(Opsional) Tambahkan keterangan mutasi, misalnya alasan perpindahan."
                    >{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info sesuai role --}}
                <div class="text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2">
                    @if ($isAdmin)
                        Mutasi ini akan langsung mengubah lokasi barang ke lokasi tujuan yang diisi.
                    @else
                        Permintaan mutasi akan dikirim ke Admin sebagai notifikasi. Barang belum berpindah sebelum disetujui Admin.
                    @endif
                </div>

                {{-- Tombol aksi --}}
                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('barang.index') }}"
                       class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm rounded-xl text-white font-medium
                               {{ $isAdmin ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700' }}"
                    >
                        {{ $isAdmin ? 'Simpan Mutasi' : 'Kirim Permintaan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
