<x-layout>
    <x-slot name="title">Mutasi Barang</x-slot>

    @php
        $user    = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
    @endphp

    <div class="max-w-3xl mx-auto mt-10">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Mutasi Lokasi</p>
                    <h1 class="text-xl font-semibold">
                        {{ $isAdmin ? 'Pindahkan Barang' : 'Ajukan Mutasi Barang' }}
                    </h1>
                    <p class="text-sm text-blue-100">
                        Lengkapi detail tujuan dan tanggal mutasi.
                    </p>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1 text-[11px] rounded-full bg-white/15 border border-white/30 uppercase tracking-wide">
                    <span class="size-2 rounded-full bg-white/80"></span>
                    {{ $isAdmin ? 'Admin' : 'Pengelola' }}
                </span>
            </div>

            <div class="px-6 pt-5 pb-4 bg-gray-50 border-b border-gray-100 text-sm text-gray-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-4">
                    <div>
                        <p class="text-xs text-gray-500">Barang</p>
                        <p class="font-semibold text-gray-900">{{ $barang->nama_barang }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kode Register</p>
                        <p class="font-mono text-sm text-gray-800">{{ $barang->kode_register ?? '–' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Lokasi Saat Ini</p>
                        <p class="font-semibold text-gray-900">{{ optional($barang->location)->name ?? '–' }}</p>
                    </div>
                </div>
            </div>

            <form
                action="{{ $isAdmin
                    ? route('mutasi.store', $barang)     
                    : route('mutasi.request', $barang)   
                }}"
                method="POST"
                class="px-6 pb-6 pt-4 space-y-4"
            >
                @csrf

                <div x-data="{ showOther: {{ old('lokasi_baru') ? 'true' : 'false' }} }" class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">
                        Lokasi Tujuan <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="lokasi"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi') border-red-500 @enderror"
                        @change="showOther = ($event.target.value === 'other')"
                    >
                        <option value="">Pilih Lokasi Tujuan</option>

                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}"
                                @selected(old('lokasi') === $loc->name)>
                                {{ $loc->name }}
                            </option>
                        @endforeach

                        <option value="other" @selected(old('lokasi') === 'other')>Lainnya…</option>
                    </select>

                    @error('lokasi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <div x-show="showOther" x-cloak class="mt-2 space-y-1">
                        <label class="block text-sm font-semibold text-gray-800">
                            Lokasi Baru
                        </label>
                        <input
                            type="text"
                            name="lokasi_baru"
                            value="{{ old('lokasi_baru') }}"
                            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi_baru') border-red-500 @enderror"
                            placeholder="Tulis lokasi tujuan baru…"
                        >
                        @error('lokasi_baru')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            Pilih ini jika lokasi tujuan belum ada di daftar. Jika memilih lokasi dari daftar, tidak perlu mengisi lokasi baru.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">
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

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">
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
