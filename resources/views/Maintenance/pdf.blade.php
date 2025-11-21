{{-- resources/views/maintenance/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Pemeliharaan</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm 2.5cm 2.5cm 3cm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .meta-table {
            font-size: 10pt;
        }

        .meta-table td:first-child {
            width: 70px;
        }

        .meta-table td:nth-child(2) {
            width: 10px;
        }

        .right-header {
            float: right;
            text-align: right;
            font-size: 10pt;
        }

        .mt-10 { margin-top: 10px; }
        .mt-20 { margin-top: 20px; }
        .mt-30 { margin-top: 30px; }
        .mb-5  { margin-bottom: 5px; }

        .indent {
            text-indent: 1.25cm;
            text-align: justify;
        }

        table.permintaan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10pt;
        }

        table.permintaan th,
        table.permintaan td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        table.permintaan th {
            text-align: center;
        }

        table.permintaan td.number {
            text-align: center;
        }

        table.permintaan td.right {
            text-align: right;
        }

        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-kanan {
            float: right;
            text-align: center;
            width: 45%;
            font-size: 11pt;
        }

        .ttd-kanan .nama {
            text-decoration: underline;
            font-weight: bold;
        }

        .ttd-kanan .nip {
            font-size: 10pt;
        }
    </style>
</head>
<body>

    {{-- Bagian kiri (Sifat, Lampiran, Hal) + kanan (kota, tanggal) --}}
    <div class="clearfix">
        <table class="meta-table" style="float:left;">
            <tr>
                <td>Sifat</td><td>:</td><td>{{ $sifat ?? 'Biasa' }}</td>
            </tr>
            <tr>
                <td>Lampiran</td><td>:</td><td>{{ $lampiran ?? '1 (satu) berkas' }}</td>
            </tr>
            <tr>
                <td>Hal</td><td>:</td><td>{{ $hal ?? 'Permohonan Pemeliharaan Peralatan dan Mesin' }}</td>
            </tr>
        </table>

        <div class="right-header">
            {{ $kotaSurat ?? 'Palu' }},
            {{ $tanggalSurat ?? \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- Alamat tujuan --}}
    <div class="mt-30">
        Kepada Yth.<br>
        Kepala {{ $instansiTujuan ?? 'TVRI Stasiun Sulawesi Tengah' }}<br>
        Di-<br>
        <strong>{{ $kotaTujuan ?? 'Palu' }}</strong>
    </div>

    {{-- Paragraf pembuka --}}
    <div class="mt-20">
        Dengan hormat,
    </div>

    @php
        // Ambil uraian dari setiap baris sebagai keterangan barang + lokasi
        $deskripsiBarang = collect($rows ?? [])
            ->map(function ($row) {
                // dukung array dan objek
                return is_array($row)
                    ? ($row['uraian'] ?? null)
                    : ($row->uraian ?? null);
            })
            ->filter()      // buang null/kosong
            ->unique()      // hilangkan duplikat
            ->values();
    @endphp

    <p class="indent mt-10">
        Bersama ini kami mengajukan permohonan Pemeliharaan Peralatan dan Mesin
        berupa
        @if($deskripsiBarang->isNotEmpty())
            {{ $deskripsiBarang->implode('; ') }}
        @else
            barang sebagaimana tercantum pada rincian di bawah
        @endif
        yang sudah tidak berfungsi dengan baik, dengan rincian sebagai berikut.
    </p>

    {{-- Tabel rincian permintaan --}}
    @php
        // diasumsikan $rows adalah collection/array dari data maintenance yang akan dicetak
        // dan sudah disiapkan di controller.
        // Contoh field yang digunakan: uraian, jumlah, satuan, pagu, total.
        $totalKeseluruhan = collect($rows ?? [])->sum('total');
    @endphp

    <table class="permintaan">
        <thead>
        <tr>
            <th style="width:35px;">No</th>
            <th>Uraian</th>
            <th style="width:70px;">Jumlah</th>
            <th style="width:90px;">Satuan Pagu</th>
            <th style="width:90px;">Total</th>
            <th style="width:70px;">Ket</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows ?? [] as $i => $row)
            <tr>
                <td class="number">{{ $i + 1 }}</td>
                <td>{{ $row['uraian'] ?? $row->uraian ?? '-' }}</td>
                <td class="number">
                    {{ $row['jumlah'] ?? $row->jumlah ?? '-' }}
                    {{ $row['satuan'] ?? $row->satuan ?? '' }}
                </td>
                <td class="right">
                    Rp {{ number_format((int)($row['pagu'] ?? $row->pagu ?? 0), 0, ',', '.') }}
                </td>
                <td class="right">
                    Rp {{ number_format((int)($row['total'] ?? $row->total ?? 0), 0, ',', '.') }}
                </td>
                <td>&nbsp;</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;">Tidak ada data.</td>
            </tr>
        @endforelse
        {{-- Baris jumlah --}}
        <tr>
            <td colspan="4" class="right"><strong>Jumlah</strong></td>
            <td class="right">
                <strong>Rp {{ number_format((int)$totalKeseluruhan, 0, ',', '.') }}</strong>
            </td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>

    <p class="mt-20">
        Demikian disampaikan atas perhatian dan kerjasama diucapkan terima kasih.
    </p>

    {{-- TTD kanan --}}
    <div class="ttd-wrapper">
        <div class="ttd-kanan">
            ttd,<br>
            Pejabat Pengadaan pada lembaga Penyiaran Publik Tekevisi Republik Indonesia<br>
        </div>
    </div>

</body>
</html>
