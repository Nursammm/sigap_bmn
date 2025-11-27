{{-- resources/views/ruangan/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Barang Ruangan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }

        /* --- GLOBAL TABLE STYLES --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #ccc; /* Garis grid abu-abu seperti Excel */
            padding: 4px 6px;
            vertical-align: middle;
        }

        /* --- HEADER SECTION (Grid Style) --- */
        .header-table td {
            font-size: 9pt;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            background-color: #fff;
            border: 1px solid #ccc;
            height: 30px;
        }

        .label-cell {
            font-weight: bold;
            width: 150px;
            background-color: #f9f9f9; /* Sedikit abu agar terlihat seperti label */
        }

        .label-right {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 110px;          /* lebih kecil dari label biasa */
        }

        .colon-tight {
            width: 10px;           /* sempit, jadi dekat dengan teks */
            text-align: center;
            padding-left: 0;
            padding-right: 0;
        }

        .value-cell {
            /* Area isi */
        }

        .qr-cell {
            text-align: center;
            vertical-align: middle;
            width: 100px;
        }

        /* --- DATA TABLE SECTION --- */
        .data-table th {
            background-color: #0047b3; /* Biru tua persis gambar */
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #003380;
        }

        .data-table td {
            font-size: 9pt;
            border: 1px solid #000; /* Garis tabel data lebih hitam tegas */
        }

        /* Alignment Helpers */
        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        
        /* Links style */
        a.link-blue {
            color: #0000EE;
            text-decoration: underline;
            cursor: pointer;
        }

        /* Lebar Kolom (Disesuaikan proporsinya) */
        .col-no { width: 30px; }
        .col-kode { width: 90px; }
        .col-nama { width: auto; }
        .col-merk { width: 100px; }
        .col-nup { width: 40px; }
        .col-tgl { width: 80px; }
        .col-kondisi { width: 70px; }
        .col-ket { width: 80px; }
        .col-foto { width: 70px; }
        .col-qr { width: 80px; }

    </style>
</head>
<body>

    {{-- HEADER: Grid Layout persis Excel --}}
    <table class="header-table">
        {{-- Baris 1: Judul --}}
        <tr>
            <td colspan="6" class="header-title">DAFTAR BARANG RUANGAN</td>
        </tr>

        {{-- Baris 2: Pemilik Ruangan --}}
        <tr>
            <td class="label-cell">PEMILIK RUANGAN</td>
            <td class="text-center" style="width:10px;">:</td>
            <td colspan="4"></td>
        </tr>

        {{-- Baris 3: UAPB --}}
        <tr>
            <td class="label-cell">UAPB</td>
            <td class="text-center">:</td>
            <td colspan="4">LEMBAGA PENYIARAN PUBLIK TVRI</td>
        </tr>

        {{-- Baris 4: UAPB-E1 --}}
        <tr>
            <td class="label-cell">UAPB-E1</td>
            <td class="text-center">:</td>
            <td colspan="4">LPP TVRI</td>
        </tr>

        {{-- Baris 5: Nama UAKPB --}}
        <tr>
            <td class="label-cell">NAMA UAKPB</td>
            <td class="text-center">:</td>
            <td colspan="4">TVRI STASIUN SULAWESI TENGAH</td>
        </tr>

        {{-- Baris 6: Kode UAKPB (kiri) + NAMA RUANGAN (kanan) --}}
        <tr>
            <td class="label-cell">KODE UAKPB</td>
            <td class="text-center">:</td>
            <td>117011800700231000KD</td>

            <td class="label-right">NAMA RUANGAN</td>
            <td class="colon-tight">:</td>
            <td class="value-cell">{{ $location?->name ?? 'RUANGAN UMUM' }}</td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>

            <td class="label-right">TAHUN</td>
            <td class="colon-tight">:</td>
            <td>{{ now()->year }}</td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>

            <td class="label-right" style="text-align:right;">Timestamp</td>
            <td class="colon-tight">:</td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- TABEL DATA BARANG --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-kode">Kode Barang</th>
                    <th class="col-nama">Nama Barang</th>
                    <th class="col-merk">Merk/Type</th>
                    <th class="col-nup">NUP</th>
                    <th class="col-tgl">Tahun Perolehan</th>
                    <th class="col-kondisi">Kondisi Barang</th>
                    <th class="col-ket">Keterangan</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $index => $b)
                    @php
                        $tgl = $b->tgl_perolehan
                            ? \Carbon\Carbon::parse($b->tgl_perolehan)->format('d/m/Y')
                            : '';
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $b->kode_barang }}</td>
                        <td class="text-left">{{ $b->nama_barang }}</td>
                        <td class="text-left">{{ $b->merek }}</td>
                        <td class="text-center">{{ $b->nup }}</td>
                        <td class="text-center">{{ $tgl }}</td>
                        <td class="text-center">{{ $b->kondisi }}</td>
                        <td class="text-left">{{ $b->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

</body>
</html>