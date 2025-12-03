<x-layout>
    <x-slot name="title">Daftar Barang</x-slot>

    @php
        $totalNilai = ($barangs)
            ? $barangs->getCollection()->sum('nilai_perolehan')
            : collect($barangs)->sum('nilai_perolehan');
    @endphp

    <form
            method="GET"
            action="{{ route('barang.index') }}"
            class="bg-white border border-slate-200 rounded-2xl shadow-sm px-4 py-4 md:px-6 md:py-5 space-y-4"
        >
            <div class="grid gap-4 md:grid-cols-12 md:items-end">
                <div class="flex flex-col gap-1 md:col-span-5">
                    <label for="customSearch" class="text-xs font-medium text-slate-600">
                        Pencarian (nama, NUP, atau lokasi)
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            id="customSearch"
                            placeholder="Cari"
                            value="{{ request('q') }}"
                            oninput="this.form.submit()"
                            class="w-full border border-slate-300 pl-8 pr-3 py-2.5 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-1 md:col-span-3">
                    <label for="filterKondisi" class="text-xs font-medium text-slate-600">
                        Filter kondisi barang
                    </label>
                    <select
                        name="kondisi"
                        id="filterKondisi"
                        onchange="this.form.submit()"
                        class="w-full border border-slate-300 px-3 py-2.5 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                    >
                        <option value="">Semua kondisi</option>
                        <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="Hilang" {{ request('kondisi') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1 md:col-span-2">
                    <label for="shortData" class="text-xs font-medium text-slate-600">
                        Urutkan data
                    </label>
                    <select
                        name="sort"
                        id="shortData"
                        onchange="this.form.submit()"
                        class="w-full border border-slate-300 px-3 py-2.5 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                    >
                        <option value="">Default</option>
                        <option value="asc"          {{ request('sort') == 'asc' ? 'selected' : '' }}>Nama (A–Z)</option>
                        <option value="desc"         {{ request('sort') == 'desc' ? 'selected' : '' }}>Nama (Z–A)</option>
                        <option value="nilai_asc"    {{ request('sort') == 'nilai_asc' ? 'selected' : '' }}>Nilai Terendah</option>
                        <option value="nilai_desc"   {{ request('sort') == 'nilai_desc' ? 'selected' : '' }}>Nilai Tertinggi</option>
                        <option value="tanggal_asc"  {{ request('sort') == 'tanggal_asc' ? 'selected' : '' }}>Tanggal Lama</option>
                        <option value="tanggal_desc" {{ request('sort') == 'tanggal_desc' ? 'selected' : '' }}>Tanggal Baru</option>
                    </select>
                </div>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="flex md:col-span-2 md:justify-end md:items-end">
                            <a
                                href="{{ route('barang.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl shadow-sm hover:bg-blue-700 transition"
                            >
                                <i class="fas fa-plus mr-2"></i> Tambah Aset
                            </a>
                        </div>
                    @endif
                @endauth
        </div>
    </form>

    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-5">
            <h2 class="text-xl font-bold text-gray-800">
                Data Asset ({{ $barangs->total() }})
            </h2>
            <p class="text-sm text-gray-600 mt-2 md:mt-0">
                Total Nilai:
                <strong class="text-gray-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong>
            </p>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table id="dataTable" class="min-w-full text-sm text-gray-700">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-3 text-left">No</th>
                        <th class="px-3 py-3 text-left">Special Code</th>
                        <th class="px-3 py-3 text-left">Kode Register</th>
                        <th class="px-3 py-3 text-left">Kode Barang</th>
                        <th class="px-3 py-3 text-left">NUP</th>
                        <th class="px-3 py-3 text-left">Nama Barang</th>
                        <th class="px-3 py-3 text-left">Merek</th>
                        <th class="px-3 py-3 text-left">Tanggal</th>
                        <th class="px-3 py-3 text-left">Nomor Seri</th>
                        <th class="px-3 py-3 text-left">Kondisi</th>
                        <th class="px-3 py-3 text-right">Nilai</th>
                        <th class="px-3 py-3 text-center">QR</th>
                        <th class="px-3 py-3 text-left">Lokasi</th>
                        <th class="px-3 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($barangs as $no => $barang)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 align-top">
                                {{ ($barangs->firstItem() ?? 0) + $no }}
                            </td>

                            <td class="px-3 py-2">{{ $barang->special_code }}</td>
                            <td class="px-3 py-2 font-mono text-xs">{{ $barang->kode_register ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $barang->kode_barang }}</td>

                            <td class="px-3 py-2 align-top">
                                {{ str_pad($barang->nup, 1, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-3 py-2 align-top font-medium text-gray-900 whitespace-normal">
                                {{ $barang->nama_barang }}
                            </td>

                            <td class="px-3 py-2 align-top whitespace-normal">
                                {{ $barang->merek ?? '-' }}
                            </td>

                            <td class="px-3 py-2 align-top">
                                {{ $barang->tgl_perolehan ? \Carbon\Carbon::parse($barang->tgl_perolehan)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2">{{ $barang->sn ?? '-' }}</td>

                            <td class="px-3 py-2 align-top">
                                @php
                                    $warna = match($barang->kondisi) {
                                        'Baik' => 'bg-green-100 text-green-700 hover:bg-green-200 hover:text-green-800',
                                        'Rusak Ringan' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800',
                                        'Rusak Berat' => 'bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800',
                                        'Hilang' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-800',
                                        default => 'bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800',
                                    };
                                @endphp
                                <span class="{{ $warna }} inline-flex items-center justify-center text-xs font-medium px-3 py-1 rounded-full">
                                    {{ $barang->kondisi ?? 'Baik' }}
                                </span>
                            </td>

                            <td class="px-3 py-2 align-top text-right">
                                <span class="inline-flex items-center">
                                    <span class="mr-1 text-gray-500">Rp</span>
                                    <span class="font-medium text-gray-900">
                                        {{ number_format($barang->nilai_perolehan ?? 0, 0, ',', '.') }}
                                    </span>
                                </span>
                            </td>

                            <td class="px-3 py-2 align-top text-center">
                                <button type="button"
                                    onclick="openQrModal(
                                                '{{ $barang->id }}',
                                                '{{ $barang->nama_barang }}',
                                                '{{ $barang->kode_register }}',
                                                '{{ $barang->alternatif_qr }}',
                                                '{{ $barang->location->name ?? '-' }}'
                                            )"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-100"
                                    title="Lihat QR">
                                    <i class="fas fa-qrcode text-gray-600"></i>
                                </button>
                            </td>

                            <td class="px-3 py-2 align-top whitespace-normal">
                                {{ $barang->location->name ?? '-' }}
                            </td>

                            <td class="px-3 py-2 align-top text-center">
                                @include('barang.partials.actions', ['barang' => $barang])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-3 py-6 text-center text-gray-500">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangs instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="mt-4">
                {{ $barangs->links() }}
            </div>
        @endif
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            #dataTable thead th {
                background: #f1f5f9;
                color: #1f2937;
                font-weight: 600;
                padding: 14px;
                border-bottom: 2px solid #e5e7eb;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            #dataTable tbody td {
                padding: 10px 12px;
            }

            #dataTable tbody tr {
                background: #ffffff;
                transition: background 0.15s ease;
            }

            #dataTable tbody tr:nth-child(even) {
                background: #f9fafb;
            }

            #dataTable tbody tr:hover {
                background: #f8fafc;
            }
        </style>

        <script>

            function openQrModal(id, nama, kode_register, alternatif_qr, lokasi) {
                const encodedData = encodeURIComponent(alternatif_qr || '');

                document.getElementById('qr-title').innerText = "QR Code - " + nama;
                document.getElementById('qr-image').src = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" + encodedData;
                document.getElementById('qr-kode_register').innerText = "Kode Register: " + (kode_register || '-');
                document.getElementById('qr-location').innerText = "Lokasi: " + (lokasi || '-');

                let modal = document.getElementById('qr-modal');
                let box = document.getElementById('qr-box');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    box.classList.remove('scale-95','opacity-0');
                    box.classList.add('scale-100','opacity-100');
                }, 50);
            }

            function closeQrModal() {
                let modal = document.getElementById('qr-modal');
                let box = document.getElementById('qr-box');
                box.classList.add('scale-95','opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 200);
            }
        </script>
    @endpush
</x-layout>
