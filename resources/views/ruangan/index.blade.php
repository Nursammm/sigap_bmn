<x-layout>
    <x-slot name="title">Daftar Barang Ruangan</x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
            {{-- Header & Filter --}}
            <div class="px-6 md:px-8 py-5 border-b bg-gray-50">
                <div class="flex flex-col md:flex-row md:items-end gap-4 md:gap-6">
                    {{-- Pilih Ruangan --}}
                    <form method="GET" action="{{ route('ruangan.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 w-full">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <select name="lokasi" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40" onchange="this.form.submit()">
                                <option value="">— Semua Ruangan —</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected($lokasiId == $loc->id)>
                                        {{ $loc->name }} ({{ $loc->barangs_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pencarian --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Nama/kode/merek…"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40">
                        </div>

                        {{-- Kondisi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                            <select name="kondisi" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40">
                                <option value="">Semua</option>
                                @foreach (['Baik','Rusak Ringan','Rusak Berat','Hilang'] as $k)
                                  <option value="{{ $k }}" @selected($kondisi === $k)>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Terapkan</button>
                            <a href="{{ route('ruangan.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Reset</a>
                            <a href="{{ route('ruangan.print', request()->query()) }}" target="_blank"
                               class="px-4 py-2 rounded-lg bg-white border hover:bg-gray-50">Cetak</a>
                        </div>
                    </form>
                </div>

                {{-- Lokasi aktif --}}
                <div class="mt-4 text-sm text-gray-600">
                    @if ($activeLocation)
                        Menampilkan barang di ruangan: <span class="font-medium text-gray-800">{{ $activeLocation->name }}</span>
                    @else
                        Menampilkan semua ruangan
                    @endif
                </div>
            </div>

            {{-- Statistik --}}
            <div class="px-6 md:px-8 py-5 border-b">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="rounded-lg border bg-white p-3 text-center">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="text-xl font-semibold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="rounded-lg border bg-white p-3 text-center">
                        <div class="text-xs text-gray-500">Baik</div>
                        <div class="text-xl font-semibold text-emerald-600">{{ $stats['baik'] }}</div>
                    </div>
                    <div class="rounded-lg border bg-white p-3 text-center">
                        <div class="text-xs text-gray-500">Rusak Ringan</div>
                        <div class="text-xl font-semibold text-amber-600">{{ $stats['rr'] }}</div>
                    </div>
                    <div class="rounded-lg border bg-white p-3 text-center">
                        <div class="text-xs text-gray-500">Rusak Berat</div>
                        <div class="text-xl font-semibold text-red-600">{{ $stats['rb'] }}</div>
                    </div>
                    <div class="rounded-lg border bg-white p-3 text-center">
                        <div class="text-xs text-gray-500">Hilang</div>
                        <div class="text-xl font-semibold text-gray-700">{{ $stats['hilang'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="px-6 md:px-8 py-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left">
                                <th class="px-3 py-2">Nama Barang</th>
                                <th class="px-3 py-2">Kategori</th>
                                <th class="px-3 py-2">Kode</th>
                                <th class="px-3 py-2">Merek</th>
                                <th class="px-3 py-2">Kondisi</th>
                                <th class="px-3 py-2">Tgl Perolehan</th>
                                <th class="px-3 py-2">Nilai</th>
                                <th class="px-3 py-2">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($barangs as $b)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="font-medium">{{ $b->nama_barang }}</div>
                                        <div class="text-xs text-gray-500 font-mono">#{{ $b->kode_register }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            {{ optional($b->category)->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 font-mono">{{ $b->kode_barang }}</td>
                                    <td class="px-3 py-2">{{ $b->merek }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $badge = match($b->kondisi) {
                                                'Baik' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                                'Rusak Ringan' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                                'Rusak Berat' => 'bg-red-50 text-red-700 ring-red-200',
                                                'Hilang' => 'bg-gray-100 text-gray-700 ring-gray-200',
                                                default => 'bg-gray-100 text-gray-700 ring-gray-200'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                            {{ $b->kondisi ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($b->tgl_perolehan)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2">{{ number_format((float) $b->nilai_perolehan, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">{{ optional($b->location)->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-10 text-center text-gray-500">
                                        Tidak ada data untuk filter saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $barangs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
