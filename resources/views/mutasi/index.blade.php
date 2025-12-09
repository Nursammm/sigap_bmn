<x-layout>
    <x-slot name="title">Riwayat Mutasi Barang</x-slot>

    <div class="max-w-6xl mx-auto mt-8 p-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Riwayat Mutasi Barang</h1>
                    <p class="text-gray-600 text-sm">Semua perpindahan lokasi barang tercatat di sini.</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl shadow-md border border-gray-100">
                <table class="min-w-full text-sm bg-white">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Barang</th>
                            <th class="px-4 py-3 text-left font-semibold">Dari Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold">Ke Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold">Oleh</th>
                            <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($items as $it)
                            <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                                {{-- Tanggal --}}
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $it->tanggal?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Barang --}}
                                <td class="px-4 py-3">
                                    @if ($it->barang)
                                        <a href="{{ route('barang.show', $it->barang) }}"
                                           class="font-medium text-blue-700 hover:text-blue-900 hover:underline">
                                            {{ $it->barang->nama_barang }}
                                        </a>
                                        <div class="font-mono text-xs text-gray-500">
                                            Kode: {{ $it->barang->kode_register ?? '-' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Barang tidak ditemukan</span>
                                    @endif
                                </td>

                                {{-- Dari lokasi --}}
                                <td class="px-4 py-3 text-gray-700">
                                    {{ optional($it->fromLocation)->name ?: '—' }}
                                </td>

                                {{-- Ke lokasi --}}
                                <td class="px-4 py-3 text-gray-700">
                                    {{ optional($it->toLocation)->name ?: '—' }}
                                </td>

                                {{-- User pemindah --}}
                                <td class="px-4 py-3 text-gray-700">
                                    {{ optional($it->user)->name ?: '—' }}
                                </td>

                                {{-- Catatan --}}
                                <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
                                    {{ $it->catatan ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500 text-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="mt-2">Belum ada riwayat mutasi barang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="mt-6 p-4 bg-gray-50 rounded-lg flex justify-center">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
