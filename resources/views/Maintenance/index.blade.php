<x-layout>
    <x-slot name="title">Pemeliharaan Barang</x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            {{-- Header + Filter --}}
            <div class="px-6 md:px-8 py-5 border-b bg-gray-50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Daftar Pemeliharaan</h2>
                        <p class="text-sm text-gray-500">Total biaya: <span class="font-medium">Rp {{ number_format((int)$totalBiaya,0,',','.') }}</span></p>
                    </div>
                    <a href="{{ route('barang.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">← Kembali</a>
                </div>

                <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                    <input type="hidden" name="barang_id" value="{{ $barangId }}">
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Nama barang / uraian / vendor / kode"
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                            <option value="">Semua</option>
                            @foreach (['Diajukan','Disetujui','Proses','Selesai','Ditolak'] as $s)
                                <option value="{{ $s }}" @selected($status===$s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Dari</label>
                        <input type="date" name="from" value="{{ $from }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Terapkan</button>
                        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Reset</a>
                    </div>
                </form>

                @if (session('ok'))
                  <div class="mt-3 rounded-md bg-green-50 p-3 text-sm text-green-800">{{ session('ok') }}</div>
                @endif
            </div>

            {{-- Tabel --}}
            <div class="px-6 md:px-8 py-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left">
                                <th class="px-3 py-2">Barang</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Vendor</th>
                                <th class="px-3 py-2">Biaya</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Lampiran</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($items as $m)
                                @php
                                    $badge = match($m->status) {
                                        'Diajukan'   => 'bg-amber-50 text-amber-700 ring-amber-200',
                                        'Disetujui'  => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'Proses'     => 'bg-blue-50 text-blue-700 ring-blue-200',
                                        'Selesai'    => 'bg-slate-100 text-slate-700 ring-slate-200',
                                        'Ditolak'    => 'bg-red-50 text-red-700 ring-red-200',
                                        default      => 'bg-gray-100 text-gray-700 ring-gray-200'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="font-medium">{{ $m->barang->nama_barang }}</div>
                                        <div class="text-xs text-gray-500 font-mono">#{{ $m->barang->kode_register }}</div>
                                    </td>
                                    <td class="px-3 py-2">{{ $m->jenis }}</td>
                                    <td class="px-3 py-2">
                                        {{ $m->tanggal_mulai->format('d/m/Y') }}
                                        @if($m->tanggal_selesai) – {{ $m->tanggal_selesai->format('d/m/Y') }} @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $m->vendor ?? '—' }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format((int)$m->biaya,0,',','.') }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                            {{ $m->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($m->lampiran_url)
                                            <a href="{{ $m->lampiran_url }}" class="text-blue-600 hover:underline" target="_blank">Lihat</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('maintenance.edit',$m) }}" class="px-3 py-1.5 rounded-lg bg-white border hover:bg-gray-50">Edit</a>

                                            @if(auth()->user()->role === 'admin')
                                                <form action="{{ route('maintenance.approve',$m) }}" method="POST" onsubmit="return confirm('Setujui pengajuan ini?')">
                                                    @csrf
                                                    <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Setuju</button>
                                                </form>
                                                <form action="{{ route('maintenance.reject',$m) }}" method="POST" onsubmit="return confirm('Tolak pengajuan ini?')">
                                                    @csrf
                                                    <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700">Tolak</button>
                                                </form>
                                                <form action="{{ route('maintenance.destroy',$m) }}" method="POST" onsubmit="return confirm('Hapus data pemeliharaan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-10 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
