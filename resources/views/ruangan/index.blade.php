<x-layout>
    <x-slot name="title">Daftar Barang Ruangan</x-slot>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">

            <div class="px-6 md:px-8 py-5 border-b bg-gray-50"
                 x-data="{ openExport:false }">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <form id="filterForm" method="GET" action="{{ route('ruangan.index') }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end w-full">
                        <div class="flex flex-col min-w-[180px] max-w-[240px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <select name="lokasi"
                                    class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                    onchange="document.getElementById('filterForm').submit()">
                                <option value="" @selected(($lokasiParam ?? '') === '' || ($lokasiParam ?? '') === null)>- Semua Ruangan -</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected($lokasiId == $loc->id)>
                                        {{ $loc->name }} ({{ $loc->barangs_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col min-w-[180px] max-w-[250px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                            <input type="text" name="q" value="{{ $search }}"
                                   placeholder="Nama/kode/merek"
                                   class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                   oninput="debouncedSubmitFilter()">
                        </div>

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

                        <div class="flex flex-col min-w-[180px] max-w-[220px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                            <select name="sort"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                    onchange="document.getElementById('filterForm').submit()">
                                @php $sort = request('sort'); @endphp
                                <option value="">Default</option>
                                <option value="tgl_perolehan_desc" @selected($sort === 'tgl_perolehan_desc')>
                                    Tanggal Perolehan → Terbaru
                                </option>
                                <option value="tgl_perolehan_asc" @selected($sort === 'tgl_perolehan_asc')>
                                    Tanggal Perolehan → Terlama
                                </option>
                                <option value="nama_asc" @selected($sort === 'nama_asc')>
                                    Nama Barang → A - Z
                                </option>
                                <option value="nama_desc" @selected($sort === 'nama_desc')>
                                    Nama Barang → Z - A
                                </option>
                                <option value="kode_asc" @selected($sort === 'kode_asc')>
                                    Kode Barang → Kecil - Besar
                                </option>
                                <option value="kode_desc" @selected($sort === 'kode_desc')>
                                    Kode Barang → Besar - Kecil
                                </option>
                                <option value="input_desc" @selected($sort === 'input_desc')>
                                    Tanggal Input → Terbaru
                                </option>
                                <option value="input_asc" @selected($sort === 'input_asc')>
                                    Tanggal Input → Terlama
                                </option>
                            </select>
                        </div>
                    </form>

                    <div class="flex justify-end md:ml-4">
                        <div class="relative">
                            <button type="button"
                                    @click="openExport = !openExport"
                                    @click.outside="openExport = false"
                                    class="inline-flex items-center gap-2 px-3 py-3 rounded-lg border border-blue-600 bg-blue-600 text-xs font-medium text-white hover:bg-blue-700 shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 10l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Export</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-cloak
                                 x-show="openExport"
                                 x-transition
                                 class="absolute right-0 mt-2 w-40 rounded-lg border border-gray-200 bg-white shadow-lg z-20 text-sm">
                                <a href="{{ route('ruangan.exportExcel', request()->query()) }}"
                                   class="flex items-center px-3 py-2 hover:bg-gray-50">
                                    <span class="mr-2"></span> Export Excel
                                </a>
                                <a href="{{ route('ruangan.exportPdf', request()->query()) }}"
                                   class="flex items-center px-3 py-2 hover:bg-gray-50 border-t border-gray-100">
                                    <span class="mr-2"></span> Export PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    @if ($activeLocation)
                        Menampilkan barang di ruangan:
                        <span class="font-medium text-gray-800">{{ $activeLocation->name }}</span>
                    @else
                        Menampilkan semua ruangan
                    @endif
                </div>
            </div>

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

            <div class="px-6 md:px-8 py-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left">
                                <th class="px-3 py-2">Nama Barang</th>
                                <th class="px-3 py-2">Kode</th>
                                <th class="px-3 py-2">Merek</th>
                                <th class="px-3 py-2">Kondisi</th>
                                <th class="px-3 py-2">Tgl Perolehan</th>
                                <th class="px-3 py-2">Nilai</th>
                                <th class="px-3 py-2">Ruangan</th>
                                <th class="px-3 py-2 text-center">QR</th>
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
                                            {{ $b->kondisi ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ \Illuminate\Support\Carbon::parse($b->tgl_perolehan)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ number_format((float) $b->nilai_perolehan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2">{{ optional($b->location)->name ?? '' }}</td>

                                    <td class="px-3 py-2 text-center">
                                    <button type="button"
                                        onclick="openQrModal(
                                            '{{ $b->id }}',
                                            '{{ $b->nama_barang }}',
                                            '{{ $b->kode_register }}',
                                            '{{ $b->alternatif_qr }}',
                                            '{{ optional($b->location)->name ?? '' }}'
                                        )"
                                        class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-100 shadow-sm"
                                        title="Lihat QR">
                                        <i class="fas fa-qrcode text-gray-600"></i>
                                    </button>
                                </td>

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
                                                        <path fill-rule="evenodd"
                                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12a.75.75 0 00-1.5 0v4.25c0 .414.336.75.75.75h3a.75.75 0 000-1.5H10.75V6z"
                                                              clip-rule="evenodd"/>
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
    
    <div id="qr-modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0" id="qr-box">
            <div class="px-6 pt-6 text-center">
                <h3 id="qr-title" class="text-lg font-semibold text-gray-900">QR Code</h3>
            </div>
            <div class="px-6 py-4 text-center">
                <img id="qr-image" src="" alt="QR Code" class="mx-auto mb-4" />
                <p id="qr-kode_register" class="text-sm font-medium text-gray-700"></p>
                <p id="qr-location" class="text-sm text-gray-500"></p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button onclick="closeQrModal()" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Tutup</button>
                <button onclick="window.print()" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Print</button>
            </div>
        </div>
    </div>

<script>
    function openQrModal(id, nama, kode_register, alternatif_qr, lokasi) {
        const qrData = alternatif_qr;
        const encodedData = encodeURIComponent(qrData);

        document.getElementById('qr-title').innerText = "QR Code - " + nama;
        document.getElementById('qr-image').src =
            "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" + encodedData;

        document.getElementById('qr-kode_register').innerText =
            "Kode Register: " + kode_register;

        document.getElementById('qr-location').innerText =
            "Lokasi: " + lokasi;

        let modal = document.getElementById('qr-modal');
        let box = document.getElementById('qr-box');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeQrModal() {
        let modal = document.getElementById('qr-modal');
        let box = document.getElementById('qr-box');

        box.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 200);
    }
</script>

    <script>
        let filterTimer;
        function debouncedSubmitFilter() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(function () {
                document.getElementById('filterForm').submit();
            }, 400);
        }
    </script>
</x-layout>

