{{-- resources/views/ruangan/show.blade.php --}}
<x-layout>
    <x-slot name="title">Ruangan: {{ $location->name }}</x-slot>

    <div class="bg-white p-6 rounded-2xl shadow border">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Barang di Ruangan <span class="text-blue-700">{{ $location->name }}</span>
            </h2>
            <a href="{{ route('ruangan.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Kembali</a>
        </div>

        <div class="overflow-x-auto rounded-lg border">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">Kode Register</th>
                        <th class="px-3 py-2 text-left">Nama Barang</th>
                        <th class="px-3 py-2 text-left">Kondisi</th>
                        <th class="px-3 py-2 text-left">Tanggal</th>
                        <th class="px-3 py-2 text-center">Maintenance</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barangs as $i => $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $barangs->firstItem() + $i }}</td>
                        <td class="px-3 py-2 font-mono text-xs">{{ $b->kode_register }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $b->nama_barang }}</td>
                        <td class="px-3 py-2">{{ $b->kondisi ?? '-' }}</td>
                        <td class="px-3 py-2">
                            {{ $b->tgl_perolehan ? \Carbon\Carbon::parse($b->tgl_perolehan)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Riwayat Maintenance (filter by barang) --}}
                                <a href="{{ route('maintenance.index', ['barang_id' => $b->id]) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-white border rounded-lg text-xs hover:bg-gray-50 shadow">
                                    🛠 Riwayat
                                </a>
                                {{-- Ajukan Maintenance untuk barang ini --}}
                                <a href="{{ route('maintenance.create', $b) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 shadow">
                                    + Ajukan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $barangs->links() }}
        </div>
    </div>
</x-layout>
