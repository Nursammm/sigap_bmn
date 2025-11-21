<x-layout>
    <x-slot name="title">Daftar Barang</x-slot>

    @php
        $totalNilai = ($barangs instanceof \Illuminate\Contracts\Pagination\Paginator)
            ? $barangs->getCollection()->sum('nilai_perolehan')
            : collect($barangs)->sum('nilai_perolehan');
    @endphp

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex-1">
            <input
                type="text"
                id="customSearch"
                placeholder="🔍 Cari nama, NUP, atau lokasi..."
                class="w-full border border-gray-300 p-2.5 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"
            />
        </div>
        <div class="flex items-center gap-2">
            <select
                id="filterKondisi"
                class="border border-gray-300 p-2.5 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
                <option value="">🔍 Filter Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
                <option value="Hilang">Hilang</option>
            </select>

            <!-- 🔽 Dropdown Sort -->
            <select
                id="shortData"
                class="border border-gray-300 p-2.5 rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
                <option value="">↕️ Urutkan</option>
                <option value="asc">Nama (A–Z)</option>
                <option value="desc">Nama (Z–A)</option>
                <option value="nilai_asc">Nilai Terendah</option>
                <option value="nilai_desc">Nilai Tertinggi</option>
                <option value="tanggal_asc">Tanggal Lama</option>
                <option value="tanggal_desc">Tanggal Baru</option>
            </select>

            <!-- Tombol Export -->
            <button
                id="exportExcel"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-xl shadow-sm hover:bg-green-700 transition"
            >
                <i class="fas fa-file-export mr-2"></i> Export
            </button>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a
                        href="{{ route('barang.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl shadow-sm hover:bg-blue-700 transition"
                    >
                        <i class="fas fa-plus mr-2"></i> Tambah Aset
                    </a>
            @endif
            @endauth

        </div>
    </div>

    <!-- Card -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-5">
            <h2 class="text-xl font-bold text-gray-800">
                📦 Data Aset ({{ count($barangs) }})
            </h2>
            <p class="text-sm text-gray-600 mt-2 md:mt-0">
                Total Nilai:
                <strong class="text-gray-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong>
            </p>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table id="dataTable" class="min-w-full text-sm text-gray-700">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-3 text-left">No</th>
                        <th class="px-3 py-3 text-left">Kode Sakter</th>
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
                    @foreach($barangs as $no => $barang)
                        <tr class="hover:bg-gray-50 even:bg-gray-50/50">
                            <td class="px-3 py-2">{{ $no + 1 }}</td>
                            <td class="px-3 py-2">{{ $barang->kode_sakter }}</td>
                            <td class="px-3 py-2">{{ $barang->special_code }}</td>
                            <td class="px-3 py-2 font-mono text-xs">{{ $barang->kode_register }}</td>
                            <td class="px-3 py-2">{{ $barang->kode_barang }}</td>
                            <td class="px-3 py-2">{{ str_pad($barang->nup, 1, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $barang->nama_barang }}</td>
                            <td class="px-3 py-2">{{ $barang->merek ?? '-' }}</td>
                            <td class="px-3 py-2">
                                {{ $barang->tgl_perolehan ? \Carbon\Carbon::parse($barang->tgl_perolehan)->format('d/m/Y') : '-' }}
                            </td>

                            <td class="px-3 py-2 ">{{ $barang->sn ?? '-' }}</td>

                            <td class="px-3 py-2">
                                @php
                                    $warna = match($barang->kondisi) {
                                        'Baik' => 'bg-green-100 text-green-700 hover:bg-green-200 hover:text-green-800',
                                        'Rusak Ringan' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800',
                                        'Rusak Berat' => 'bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800',
                                        'Hilang' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-800',
                                        default => 'bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800',
                                    };
                                @endphp
                                <span class="{{ $warna }} inline-flex items-center justify-center text-xs font-medium px-3 py-1 rounded-full transition-colors duration-200">
                                    {{ $barang->kondisi ?? 'Baik' }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-right">
                                <span class="inline-flex items-center">
                                    <span class="mr-1 text-gray-500">Rp</span>
                                    <span class="font-medium text-gray-900">
                                        {{ number_format($barang->nilai_perolehan ?? 0, 0, ',', '.') }}
                                    </span>
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                <button type="button"
                                    onclick="openQrModal(
                                                '{{ $barang->id }}',
                                                '{{ $barang->nama_barang }}',
                                                '{{ $barang->kode_register }}',
                                                '{{ $barang->alternatif_qr }}',
                                                '{{ $barang->location->name ?? '-' }}'
                                            )"
                                    class="p-1.5 rounded-md border border-gray-300 hover:bg-gray-100 transition"
                                    title="Lihat QR">
                                    <i class="fas fa-qrcode text-gray-600"></i>
                                </button>
                            </td>

                            <td class="px-6 py-2 whitespace-nowrap text-left">
                                <span class="truncate block max-w-[150px]" title="{{ $barang->location->name ?? '-' }}">
                                    {{ $barang->location->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                @include('barang.partials.actions', ['barang' => $barang])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($barangs instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="mt-4">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal QR -->
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
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

        <script>
            $(document).ready(function () {
                let table = $('#dataTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    searching: true,
                    ordering: false,
                    order: [], // default biarkan apa adanya
                    dom: 'lrtip',
                    language: {
                        lengthMenu: "Tampilkan _MENU_ entri",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ entri",
                        paginate: { next: "›", previous: "‹" },
                        zeroRecords: "Tidak ada data yang ditemukan",
                    },
                    columnDefs: [
                        { targets: [0], className: "text-center" },   // No
                        { targets: [10], className: "text-right" },   // Nilai
                        { targets: [11, 14], orderable: false }       // QR & Aksi
                    ]
                });

                // Pencarian custom
                $('#customSearch').on('keyup', function () {
                    table.search(this.value).draw();
                });

                // Filter kondisi (kolom 9)
                $('#filterKondisi').on('change', function () {
                    table.column(9).search(this.value).draw();
                });

                // Urutkan
                $('#shortData').on('change', function () {
                    const val = $(this).val();
                    let columnIndex = 6; // default: Nama Barang
                    let orderDir = 'asc';

                    if (val === 'desc') {
                        orderDir = 'desc';
                    } else if (val === 'nilai_asc') {
                        columnIndex = 10; orderDir = 'asc';
                    } else if (val === 'nilai_desc') {
                        columnIndex = 10; orderDir = 'desc';
                    } else if (val === 'tanggal_asc') {
                        columnIndex = 8; orderDir = 'asc';
                    } else if (val === 'tanggal_desc') {
                        columnIndex = 8; orderDir = 'desc';
                    } else {
                        table.order([]).draw();
                        return;
                    }

                    table.order([columnIndex, orderDir]).draw();
                });

                // Export ke Excel (SheetJS)
                $('#exportExcel').on('click', function () {
                    let wb = XLSX.utils.table_to_book(document.getElementById('dataTable'), { sheet: "Data Barang" });
                    XLSX.writeFile(wb, 'data_barang.xlsx');
                });
            });

            // Modal QR
            function openQrModal(id, nama, kode_register, alternatif_qr, lokasi) {
                const qrData = alternatif_qr;
                const encodedData = encodeURIComponent(qrData);

                document.getElementById('qr-title').innerText = "QR Code - " + nama;
                document.getElementById('qr-image').src = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" + encodedData;
                document.getElementById('qr-kode_register').innerText = "Kode Register: " + kode_register;
                document.getElementById('qr-location').innerText = "Lokasi: " + lokasi;
                let modal = document.getElementById('qr-modal');
                let box = document.getElementById('qr-box');
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { box.classList.remove('scale-95','opacity-0'); box.classList.add('scale-100','opacity-100'); }, 50);
            }
            function closeQrModal() {
                let modal = document.getElementById('qr-modal');
                let box = document.getElementById('qr-box');
                box.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); }, 200);
            }
        </script>
    @endpush
</x-layout>
