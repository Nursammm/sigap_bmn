{{-- resources/views/maintenance/index.blade.php --}}
<x-layout>
    <x-slot name="title">Pemeliharaan Barang</x-slot>

    <div class="max-w-7xl mx-auto" x-data>
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            {{-- HEADER + FILTER --}}
            <div class="px-6 md:px-8 py-5 border-b bg-gray-50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Data Pemeliharaan</h2>
                        @isset($totalBiaya)
                            <p class="text-sm text-gray-500">
                                Total biaya (hasil filter):
                                <span class="font-medium">Rp {{ number_format((int)$totalBiaya,0,',','.') }}</span>
                            </p>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2">
                        @admin
                        <button type="button"
                                class="px-4 py-2 rounded-lg border border-blue-500 text-blue-600 text-sm hover:bg-blue-50"
                                @click="$dispatch('pdf-open')">
                            Cetak PDF
                        </button>
                        @endadmin

                        <a href="{{ route('ruangan.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">← Kembali</a>
                    </div>
                </div>

                <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Cari</label>
                        <input type="text" name="q" value="{{ request('q','') }}"
                               placeholder="Nama barang / kode / uraian"
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        @php $st = request('status'); @endphp
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                            <option value="">Semua</option>
                            @foreach (['Diajukan','Disetujui','Proses','Selesai','Ditolak'] as $s)
                                <option value="{{ $s }}" @selected($st===$s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Dari</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Terapkan</button>
                        <a href="{{ route('maintenance.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Reset</a>
                    </div>
                </form>

                @if (session('ok'))
                    <div class="mt-3 rounded-md bg-green-50 p-3 text-sm text-green-800">{{ session('ok') }}</div>
                @endif
            </div>

            <div class="px-6 md:px-8 py-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                        <tr class="text-left text-slate-700">
                            <th class="px-3 py-2 w-14">No</th>
                            <th class="px-3 py-2">Barang</th>
                            <th class="px-3 py-2">Biaya</th>
                            <th class="px-3 py-2">Pengaju</th>
                            <th class="px-3 py-2">Catatan</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @forelse ($items as $m)
                            @php
                                [$text, $cls] = match($m->status) {
                                    'Diajukan'  => ['Pending',   'bg-amber-50 text-amber-700 ring-amber-200'],
                                    'Disetujui' => ['Approved',  'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                                    'Proses'    => ['In Progress','bg-blue-50 text-blue-700 ring-blue-200'],
                                    'Selesai'   => ['Completed', 'bg-green-50 text-green-700 ring-green-200'],
                                    'Ditolak'   => ['Rejected',  'bg-red-50 text-red-700 ring-red-200'],
                                    default     => [$m->status,  'bg-gray-100 text-gray-700 ring-gray-200'],
                                };
                                $canApprove  = auth()->user()?->role === 'admin' && $m->status === 'Diajukan';
                                $canReject   = $canApprove;
                                $canComplete = auth()->user()?->role === 'admin' && in_array($m->status, ['Disetujui','Proses'], true);
                                $rowNumber   = method_exists($items,'firstItem') ? $items->firstItem() + $loop->index : $loop->iteration;

                                // === Multi-foto: normalisasi ke array URL ===
                                // Bisa datang dari kolom JSON "photos" (array) atau kolom lama "photo_path" (string).
                                $rawPhotos = [];
                                if (!empty($m->photos)) {
                                    $rawPhotos = is_array($m->photos) ? $m->photos : (array) $m->photos;
                                } elseif (!empty($m->photo_path)) {
                                    $rawPhotos = is_array($m->photo_path) ? $m->photo_path : [$m->photo_path];
                                }
                                $photoUrls = null;
                                    if (is_array($m->photo_path)) {
                                        $photoUrls = array_map(fn($p) => \Illuminate\Support\Facades\Storage::url($p), $m->photo_path);
                                    } elseif (!empty($m->photo_path)) {
                                        $photoUrls = \Illuminate\Support\Facades\Storage::url($m->photo_path);
                                    }

                                $detailPayload = [
                                    'id'          => $m->id,
                                    'status'      => $m->status,
                                    'barang'      => $m->barang->nama_barang,
                                    'merek'       => $m->barang->merek,
                                    'kode'        => $m->barang->kode_register,
                                    'biaya'       => $m->biaya ? 'Rp '.number_format((int)$m->biaya,0,',','.') : '—',
                                    'pemohon'     => $m->requester?->name,
                                    'tgl_mulai'   => optional($m->tanggal_mulai)->format('j/n/Y'),
                                    'tgl_selesai' => optional($m->tanggal_selesai)->format('j/n/Y'),
                                    'uraian'      => $m->uraian,
                                    'photo_urls'  => $photoUrls,   
                                    'admin_note'  => $m->admin_note,
                                    'edit_url'    => route('maintenance.edit',$m),
                                ];
                            @endphp

                            <tr class="hover:bg-gray-50" x-data="{ item: @js($detailPayload) }">
                                <td class="px-3 py-3 text-slate-500">{{ $rowNumber }}</td>

                                {{-- Info barang --}}
                                <td class="px-3 py-3">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold">{{ $m->barang->nama_barang }}</div>
                                        @if(!empty($m->barang->merek))
                                            <div class="text-slate-600">{{ $m->barang->merek }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 font-mono">#{{ $m->barang->kode_register }}</div>
                                    </div>
                                </td>

                                <td class="px-3 py-3">{{ $m->biaya ? 'Rp '.number_format((int)$m->biaya,0,',','.') : '—' }}</td>
                                <td class="px-3 py-3">{{ $m->requester?->name ?? '—' }}</td>

                                {{-- CATATAN ADMIN --}}
                                <td class="px-3 py-3">
                                    <div class="truncate max-w-[34ch]" title="{{ $m->admin_note }}">
                                        {{ $m->admin_note ?? '—' }}
                                    </div>
                                </td>

                                {{-- STATUS badge --}}
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $cls }}">
                                        @switch($m->status)
                                            @case('Diajukan')
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                @break
                                            @case('Disetujui')
                                            @case('Selesai')
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="m4.5 12.75 6 6 9-13.5"/>
                                                </svg>
                                                @break
                                            @case('Proses')
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M3 13a9 9 0 1 0-1-3M2 11h6V5"/>
                                                </svg>
                                                @break
                                            @case('Ditolak')
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M6 18 18 6m0 12L6 6"/>
                                                </svg>
                                                @break
                                        @endswitch
                                        {{ $text }}
                                    </span>
                                </td>

                                <td class="px-3 py-3 whitespace-nowrap">
                                    {{ optional($m->tanggal_mulai)->format('d/m/Y') }}
                                </td>

                                {{-- AKSI --}}
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-4">
                                        {{-- Detail (eye) -> modal --}}
                                        <button type="button" title="Detail"
                                                class="text-slate-700 hover:text-slate-900"
                                                @click="$dispatch('m-show', item)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M2.036 12.322a1 1 0 0 1 0-.644C3.423 7.51 7.36 5 12 5s8.577 2.51 9.964 6.678a1 1 0 0 1 0 .644C20.577 16.49 16.64 19 12 19s-8.577-2.51-9.964-6.678Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </button>

                                        {{-- Approve --}}
                                        @if($canApprove)
                                            <form action="{{ route('maintenance.approve',$m) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" class="btn-approve text-emerald-600 hover:text-emerald-700" title="Approve">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                              d="m4.5 12.75 6 6 9-13.5"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reject --}}
                                        @if($canReject)
                                            <form action="{{ route('maintenance.reject',$m) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" class="btn-reject text-red-600 hover:text-red-700" title="Reject">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                              d="M6 18 18 6m0 12L6 6"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Complete --}}
                                        @if($canComplete)
                                            <form action="{{ route('maintenance.complete', $m) }}" method="POST" class="inline">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    class="px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition"
                                                    title="Complete">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif


                                        @admin
                                        <form action="{{ route('maintenance.destroy',$m) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-delete text-red-600 hover:text-red-700" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                
                                            </button>
                                        </form>

                                            <a href="{{ route('maintenance.edit',$m) }}" @click.stop
                                               class="hidden md:inline px-3 py-1.5 rounded-lg bg-white border hover:bg-gray-50" title="Edit">
                                               Edit
                                            </a>
                                        @endadmin
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

    {{-- MODAL PILIH BARANG UNTUK CETAK PDF --}}
    <div x-data="pdfModal()" x-on:pdf-open.window="open = true">
        <template x-if="open">
            <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 relative">
                    <button type="button"
                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                            @click="close">
                        ✕
                    </button>

                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        Pilih Barang untuk Cetak PDF
                    </h2>
                    <p class="text-xs text-gray-500 mb-4">
                        Pilih salah satu barang yang memiliki data pemeliharaan.
                    </p>

                    <form action="{{ route('maintenance.pdf') }}" method="GET" target="_blank">
                        <div class="max-h-60 overflow-y-auto space-y-1 border rounded-lg p-3 bg-gray-50">
                            @if(isset($barangList) && count($barangList))
                                @foreach($barangList as $b)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer py-1">
                                        <input type="radio"
                                               name="barang_id"
                                               value="{{ $b->id }}"
                                               class="text-blue-600 border-gray-300 focus:ring-blue-500"
                                               required>
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $b->nama_barang ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $b->kode_register ?? $b->kode_barang ?? '' }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-500">
                                    Belum ada daftar barang untuk dicetak.
                                </p>
                            @endif
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button"
                                    @click="close"
                                    class="px-4 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-1.5 text-xs rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal Detail (Alpine) --}}
    <div x-data="detailModal()" x-on:m-show.window="open($event.detail)">
        <template x-if="opened">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/40" @click.self="close"></div>

                <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between p-5 border-b">
                        <h3 class="text-xl font-semibold">Detail Permintaan Pemeliharaan</h3>
                        <button class="size-9 rounded-lg border text-slate-700 hover:bg-slate-50" @click="close" title="Tutup">
                            <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6m0 12L6 6"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="text-sm text-gray-500">ID Permintaan</div>
                                <div class="mt-1 font-medium" x-text="rec.id"></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                                          :class="badgeClass(rec.status)">
                                        <template x-if="rec.status === 'Diajukan'">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                        </template>
                                        <template x-if="rec.status === 'Disetujui' || rec.status === 'Selesai'">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </template>
                                        <template x-if="rec.status === 'Proses'">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M3 13a9 9 0 1 0-1-3M2 11h6V5"/>
                                                </svg>
                                        </template>
                                        <template x-if="rec.status === 'Ditolak'">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M6 18 18 6m0 12L6 6"/>
                                                </svg>
                                        </template>
                                        <span x-text="statusText(rec.status)"></span>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500">Nama Barang</div>
                                <div class="mt-1 font-medium">
                                    <span x-text="rec.barang"></span>
                                    <template x-if="rec.merek">
                                        <span class="font-medium"> <span class="text-gray-500">•</span> <span x-text="rec.merek"></span></span>
                                    </template>
                                    <div class="text-xs text-gray-500 font-mono">#<span x-text="rec.kode"></span></div>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500">Pemohon</div>
                                <div class="mt-1 font-medium" x-text="rec.pemohon || '—'"></div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500">Biaya</div>
                                <div class="mt-1 font-medium" x-text="rec.biaya || '—'"></div>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500">Tanggal Permintaan</div>
                                <div class="mt-1 font-medium" x-text="rec.tgl_mulai || '—'"></div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Tanggal Selesai</div>
                                <div class="mt-1 font-medium" x-text="rec.tgl_selesai || '—'"></div>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">Deskripsi</div>
                            <div class="mt-1 rounded-lg bg-gray-50 border px-4 py-3 text-[13px]" x-text="rec.uraian || '—'"></div>
                        </div>

                        <template x-if="rec.admin_note">
                            <div>
                                <div class="text-sm text-gray-500">Catatan Admin</div>
                                <div class="mt-1 rounded-lg bg-amber-50 border px-4 py-3 text-[13px]" x-text="rec.admin_note"></div>
                            </div>
                        </template>

                        {{-- GALERI MULTI-FOTO --}}
                        <template x-if="rec.photo_urls && rec.photo_urls.length">
                            <div x-data="{ open:false, activeSrc:'' }" class="mt-4">

                                <div class="text-sm text-gray-500 mb-2">Lampiran Foto</div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <template x-for="(url, i) in rec.photo_urls" :key="i">
                                        <button type="button"
                                                class="focus:outline-none"
                                                @click="open = true; activeSrc = url">
                                            <img :src="url"
                                                alt="Lampiran"
                                                class="w-full h-36 object-cover rounded-lg border hover:opacity-80 transition">
                                        </button>
                                    </template>
                                </div>

                        {{-- MODAL --}}
                        <template x-if="open">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
                                @click.self="open = false">

                                <div class="bg-white p-4 rounded-xl max-w-3xl w-full mx-4">

                                    <div class="flex justify-end mb-3">
                                        <button class="text-gray-500 hover:text-gray-700 text-xl leading-none"
                                                @click="open = false">&times;</button>
                                    </div>

                                    <div class="flex justify-center">
                                        <img :src="activeSrc"
                                            class="max-h-[80vh] w-auto rounded-lg">
                                    </div>

                                </div>

                            </div>
                        </template>

                    </div>
                </template>

                    <div class="flex items-center justify-end gap-2 p-5 border-t">
                        @if(auth()->user()?->role === 'admin')
                            <a :href="rec.edit_url"
                               class="px-4 py-2 rounded-lg bg-white border hover:bg-gray-50">Edit</a>
                        @endif
                        <button class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200" @click="close">Tutup</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Alpine helpers --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('detailModal', () => ({
                opened: false,
                rec: {},
                open(payload){
                    this.rec = payload || {};
                    this.opened = true;
                },
                close(){ this.opened = false; },
                statusText(s){
                    switch (s) {
                        case 'Diajukan':  return 'Pending';
                        case 'Disetujui': return 'Approved';
                        case 'Proses':    return 'In Progress';
                        case 'Selesai':   return 'Completed';
                        case 'Ditolak':   return 'Rejected';
                        default:          return s || '-';
                    }
                },
                badgeClass(s){
                    switch (s) {
                        case 'Diajukan':  return 'bg-amber-50 text-amber-700 ring-amber-200';
                        case 'Disetujui': return 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                        case 'Proses':    return 'bg-blue-50 text-blue-700 ring-blue-200';
                        case 'Selesai':   return 'bg-green-50 text-green-700 ring-green-200';
                        case 'Ditolak':   return 'bg-red-50 text-red-700 ring-red-200';
                        default:          return 'bg-gray-100 text-gray-700 ring-gray-200';
                    }
                }
            }));

            Alpine.data('pdfModal', () => ({
                open: false,
                close() { this.open = false; }
            }));
        });
    </script>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SweetAlert handlers --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const bindConfirm = (selector, options) => {
        document.querySelectorAll(selector).forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.preventDefault();
            const form = btn.closest('form');
            const opt  = (typeof options === 'function') ? options(btn) : options;

            Swal.fire({
              title: opt.title || 'Yakin?',
              text:  opt.text  || 'Tindakan ini akan diproses.',
              icon:  opt.icon  || 'question',
              showCancelButton: true,
              confirmButtonText: opt.confirmText || 'Ya',
              cancelButtonText:  opt.cancelText  || 'Batal',
              confirmButtonColor: opt.confirmColor || '#16a34a',
              cancelButtonColor:  opt.cancelColor  || '#6b7280',
              reverseButtons: true,
            }).then((res) => {
              if (res.isConfirmed && form) form.submit();
            });
          }, {passive: false});
        });
      };

      bindConfirm('.btn-approve', () => ({
        title: 'Setujui pengajuan?',
        text:  'Status akan menjadi Approved.',
        icon:  'question',
        confirmText: 'Setujui',
        confirmColor: '#16a34a',
      }));

      bindConfirm('.btn-reject', () => ({
        title: 'Tolak pengajuan?',
        text:  'Status akan menjadi Rejected.',
        icon:  'warning',
        confirmText: 'Tolak',
        confirmColor: '#dc2626',
      }));

      bindConfirm('.btn-complete', () => ({
        title: 'Tandai selesai?',
        text:  'Status akan menjadi Completed.',
        icon:  'question',
        confirmText: 'Complete',
        confirmColor: '#0ea5e9',
      }));

      bindConfirm('.btn-delete', () => ({
        title: 'Hapus data ini?',
        text:  'Tindakan ini tidak bisa dibatalkan.',
        icon:  'error',
        confirmText: 'Hapus',
        confirmColor: '#dc2626',
      }));

      @if (session('ok'))
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: @json(session('ok')),
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
        });
      @endif

      @if ($errors->any())
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: @json($errors->first()),
        });
      @endif
    });
    </script>
</x-layout>
