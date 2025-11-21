{{-- resources/views/ruangan/index.blade.php --}}
<x-layout>
    <x-slot name="title">Daftar Barang Ruangan</x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
            {{-- Header & Filter --}}
            <div class="px-6 md:px-8 py-5 border-b bg-gray-50">
                <form id="filterForm" method="GET" action="{{ route('ruangan.index') }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    {{-- Ruangan --}}
                    <div class="flex flex-col min-w-[180px] max-w-[240px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                        <select name="lokasi"
                                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="">— Semua Ruangan —</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" @selected($lokasiId == $loc->id)>
                                    {{ $loc->name }} ({{ $loc->barangs_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cari --}}
                    <div class="flex flex-col min-w-[180px] max-w-[250px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Nama/kode/merek…"
                               class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                               oninput="debouncedSubmitFilter()">
                    </div>

                    {{-- Kondisi --}}
                    <div class="flex flex-col min-w-[150px] max-w-[180px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                        <select name="kondisi"
                                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach (['Baik','Rusak Ringan','Rusak Berat','Hilang'] as $k)
                                <option value="{{ $k }}" @selected($kondisi === $k)>
                                    {{ $k }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Urutkan --}}
<div class="flex flex-col min-w-[180px] max-w-[220px]">
    <label class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
    <select name="sort"
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
            onchange="document.getElementById('filterForm').submit()">

        <option value="">Default</option>

        {{-- Tanggal Perolehan --}}
        <option value="tgl_perolehan_desc" @selected(request('sort') === 'tgl_perolehan_desc')>
            Tanggal Perolehan — Terbaru
        </option>
        <option value="tgl_perolehan_asc"  @selected(request('sort') === 'tgl_perolehan_asc')}>
            Tanggal Perolehan — Terlama
        </option>

        {{-- Nama Barang --}}
        <option value="nama_asc" @selected(request('sort') === 'nama_asc')}>
            Nama Barang — A ↦ Z
        </option>
        <option value="nama_desc" @selected(request('sort') === 'nama_desc')}>
            Nama Barang — Z ↦ A
        </option>

        {{-- Kode Barang --}}
        <option value="kode_asc" @selected(request('sort') === 'kode_asc')}>
            Kode Barang — Kecil → Besar
        </option>
        <option value="kode_desc" @selected(request('sort') === 'kode_desc')}>
            Kode Barang — Besar → Kecil
        </option>

        {{-- Tanggal Input --}}
        <option value="input_desc" @selected(request('sort') === 'input_desc')}>
            Tanggal Input — Terbaru
        </option>
        <option value="input_asc" @selected(request('sort') === 'input_asc')}>
            Tanggal Input — Terlama
        </option>

    </select>
</div>


                {{-- Lokasi aktif --}}
                <div class="mt-4 text-sm text-gray-600">
                    @if ($activeLocation)
                        Menampilkan barang di ruangan:
                        <span class="font-medium text-gray-800">{{ $activeLocation->name }}</span>
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
                                <th class="px-3 py-2 text-center">Maintenance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($barangs as $b)
                                @php
                                    $hasActiveMaintenance = \App\Models\Maintenance::where('barang_id', $b->id)
                                        ->whereIn('status', ['Diajukan','Disetujui','Proses'])
                                        ->exists();
                                @endphp
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
                                    <td class="px-3 py-2">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('maintenance.index', ['barang_id' => $b->id]) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-white border rounded-lg text-xs hover:bg-gray-50 shadow">
                                                Riwayat
                                            </a>
                                            @if(!$hasActiveMaintenance)
                                                <a href="{{ route('maintenance.create', $b) }}"
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 shadow">
                                                    + Ajukan
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium
                                                             bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12a.75.75 0 00-1.5 0v4.25c0 .414.336.75.75.75h3a.75.75 0 000-1.5H10.75V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Sedang maintenance
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-10 text-center text-gray-500">
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

    {{-- Auto-submit untuk input Cari --}}
    <script>
        let filterTimer;
        function debouncedSubmitFilter() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(function () {
                document.getElementById('filterForm').submit();
            }, 400); // delay 0.4 detik setelah user berhenti mengetik
        }
    </script>
</x-layout>
