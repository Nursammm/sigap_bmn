<x-layout>
    <x-slot name="title">Detail Barang</x-slot>

    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 px-4 mt-8">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-3xl border border-gray-200">

            @php
                $raw = $barang->foto_url;

                if (is_array($raw)) {
                    $fotos = $raw;
                } elseif (is_string($raw) && $raw !== '') {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $fotos = $decoded;
                    } else {
                        $fotos = [$raw];
                    }
                } else {
                    $fotos = [];
                }
            @endphp
            
            <div class="flex items-start mb-8 border-b pb-4">
                <div class="flex-shrink-0">
                    <div class="bg-white p-3 rounded-2xl shadow-md border border-gray-200">
                        @php
                            $qrData = $barang->alternatif_qr;
                        @endphp
                        
                        {!! QrCode::size(130)
                        ->backgroundColor(255,255,255)
                        ->generate($qrData) !!}
                    </div>
                </div>

                <div class="ml-6">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $barang->nama_barang }}</h1>
                    <span class="inline-block mt-2 px-4 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold shadow-sm">
                        {{ $barang->kondisi }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Kode Satker</label>
                    <div class="font-semibold text-gray-800">{{ $barang->kode_sakter }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Special Code</label>
                    <div class="font-semibold text-gray-800">{{ $barang->special_code }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Kode Register</label>
                    <div class="font-semibold text-gray-800">{{ $barang->kode_register }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Kode Barang</label>
                    <div class="font-semibold text-gray-800">{{ $barang->kode_barang }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">NUP</label>
                    <div class="font-semibold text-gray-800">{{ $barang->nup }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Merek</label>
                    <div class="font-semibold text-gray-800">{{ $barang->merek ?? '-' }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Lokasi</label>
                    <div class="font-semibold text-gray-800">{{ $barang->location->name ?? '-' }}</div>
                </div>
                <div>
                    <label class="block text-gray-500 text-sm mb-1">Nilai Perolehan</label>
                    <div class="font-semibold text-green-600">
                        Rp {{ number_format($barang->nilai_perolehan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-500 text-sm mb-1">Keterangan</label>
                    <div class="font-semibold text-gray-800">{{ $barang->keterangan ?? '-' }}</div>
                </div>
            </div>

            @if(count($fotos))
                <div x-data="{ open:false, activeSrc:'' }" class="mt-8">
                    <h2 class="text-sm font-semibold text-gray-600 mb-2">
                        Foto Barang
                    </h2>

                    <div class="flex gap-3 overflow-x-auto pb-2">
                        @foreach($fotos as $foto)
                            <button type="button"
                                        class="flex-shrink-0 focus:outline-none"
                                        @click="open = true; activeSrc = '{{ asset('storage/'.$foto) }}'">
                                <img src="{{ asset('storage/'.$foto) }}"
                                            alt="Foto {{ $barang->nama_barang }}"
                                            class="w-24 h-24 object-cover rounded-lg border border-gray-200 shadow-sm hover:opacity-90 transition">
                            </button>
                        @endforeach
                    </div>

                    <template x-if="open">
                        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
                                 @click.self="open = false">
                            <div class="bg-white p-4 rounded-xl max-w-3xl w-full mx-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="font-semibold text-gray-700 text-sm">
                                        Foto {{ $barang->nama_barang }}
                                    </h3>
                                    <button class="text-gray-500 hover:text-gray-700 text-xl leading-none"
                                            @click="open = false">&times;</button>
                                </div>
                                <div class="flex justify-center">
                                    <img :src="activeSrc"
                                            alt="Foto {{ $barang->nama_barang }}"
                                            class="max-h-[80vh] w-auto rounded-lg">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @else
                <p class="mt-8 text-xs text-gray-400">
                    Tidak ada foto barang yang diunggah.
                </p>
            @endif

            <div class="pt-6 flex justify-end gap-3 mt-6 border-t">
                <a href="{{ route('barang.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium shadow hover:bg-gray-300 transition">
                    Kembali
                </a>
                @auth
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('barang.edit', $barang->id) }}" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                        Edit
                    </a>
                @endif
                @endauth
            </div>

        </div>
    </div>
</x-layout>