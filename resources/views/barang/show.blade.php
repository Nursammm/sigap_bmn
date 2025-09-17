{{-- filepath: c:\laragon\www\sigap-bmn\resources\views\barang\show.blade.php --}}
<x-layout>
    <x-slot name="title">Detail Barang</x-slot>

    <div class="max-w-2xl mx-auto mt-10">
        <div class="bg-gradient-to-br from-blue-900 via-blue-700 to-blue-400 p-8 rounded-2xl shadow-2xl border border-blue-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 opacity-20 pointer-events-none">
                <svg width="180" height="180"><circle cx="90" cy="90" r="80" fill="#fff" /></svg>
            </div>
            <div class="flex items-center mb-8">
                <div class="flex-shrink-0">
                    @if($barang->foto_url)
                        <div class="group relative">
                            <img src="{{ asset('storage/'.$barang->foto_url) }}"
                                 alt="Foto {{ $barang->nama_barang }}"
                                 class="w-40 h-40 object-cover rounded-2xl border-4 border-blue-400 shadow-xl ring-4 ring-blue-200 transition duration-300 group-hover:scale-105 cursor-pointer">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black bg-opacity-40 rounded-2xl">
                                <a href="{{ asset('storage/'.$barang->foto_url) }}" target="_blank"
                                   class="px-4 py-2 bg-white text-blue-700 rounded-lg font-semibold shadow hover:bg-blue-100 transition">
                                    Lihat Ukuran Penuh
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="w-40 h-40 flex items-center justify-center bg-gray-100 rounded-2xl border text-gray-400">
                            <span>Tidak ada foto</span>
                        </div>
                    @endif
                </div>
                <div class="ml-8">
                    <h1 class="text-3xl font-extrabold text-white mb-2 tracking-wide drop-shadow-lg">{{ $barang->nama_barang }}</h1>
                    <span class="inline-block px-4 py-1 rounded-full bg-blue-200 text-blue-900 text-xs font-bold shadow">
                        {{ $barang->kondisi }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 text-base mb-8">
                <div>
                    <span class="text-blue-200">Kode Sakter</span>
                    <div class="font-semibold text-white">{{ $barang->kode_sakter }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Special Code</span>
                    <div class="font-semibold text-white">{{ $barang->special_code }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Kode Register</span>
                    <div class="font-semibold text-white">{{ $barang->kode_register }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Kode Barang</span>
                    <div class="font-semibold text-white">{{ $barang->kode_barang }}</div>
                </div>
                <div>
                    <span class="text-blue-200">NUP</span>
                    <div class="font-semibold text-white">{{ $barang->nup }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Merek</span>
                    <div class="font-semibold text-white">{{ $barang->merek ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Lokasi</span>
                    <div class="font-semibold text-white">{{ $barang->location->name ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Nilai Perolehan</span>
                    <div class="font-semibold text-green-300">Rp {{ number_format($barang->nilai_perolehan, 0, ',', '.') }}</div>
                </div>
                <div>
                    <span class="text-blue-200">Keterangan</span>
                    <div class="font-semibold text-white">{{ $barang->keterangan}}</div>
                </div>
            </div>

            <div class="flex flex-col items-center mt-4">
                <span class="text-blue-100 mb-2 font-semibold tracking-wide">QR Code Barang</span>
                <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-blue-300">
                    {!! QrCode::size(140)->backgroundColor(255,255,255)->generate(route('barang.show', $barang->id)) !!}
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('barang.index') }}"
                   class="px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-400 text-white rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-layout>
