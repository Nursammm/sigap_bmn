<x-layout>
    <x-slot name="title">Notifikasi</x-slot>

    @php
        $isAdmin = $isAdmin ?? (auth()->user()?->role === 'admin');
    @endphp

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="max-w-4xl mx-auto mt-8 px-4"
         x-data="{ deleteMode:false, selected:[] }">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
                <p class="text-sm text-gray-600">
                    Kelola semua notifikasi mutasi dan pemeliharaan barang di satu halaman.
                </p>
            </div>

            <div class="flex flex-col items-end gap-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">
                        Belum dibaca: {{ $badgeUnread }}
                    </span>

                    {{-- TANDAI SEMUA DIBACA --}}
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="text-xs px-3 py-1.5 rounded-full border border-gray-200 bg-white hover:bg-gray-50"
                        >
                            Tandai semua dibaca
                        </button>
                    </form>

                    {{-- TOMBOL MASUK / KELUAR MODE HAPUS --}}
                    <button type="button"
                            class="text-xs px-3 py-1.5 rounded-full border border-rose-300 text-rose-600 bg-white hover:bg-rose-50"
                            @click="deleteMode = !deleteMode; if(!deleteMode) selected = [];">
                        <span x-show="!deleteMode">Hapus</span>
                        <span x-show="deleteMode" x-cloak>Batal</span>
                    </button>
                </div>

                {{-- FORM HAPUS TERPILIH --}}
                <form x-show="deleteMode" x-cloak
                      action="{{ route('notifications.destroySelected') }}"
                      method="POST"
                      class="flex items-center gap-2 text-[11px] mt-1">
                    @csrf
                    @method('DELETE')

                    <span class="text-gray-500" x-text="selected.length
                        ? (selected.length + ' notifikasi dipilih')
                        : 'Centang notifikasi yang ingin dihapus.'"></span>

                    {{-- hidden input untuk id terpilih --}}
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>

                    <button type="submit"
                            class="px-3 py-1.5 rounded-full bg-rose-600 text-white hover:bg-rose-700"
                            :disabled="selected.length === 0"
                            :class="{ 'opacity-50 cursor-not-allowed': selected.length === 0 }">
                        Hapus terpilih
                    </button>
                </form>
            </div>
        </div>

        {{-- Filter tab --}}
        <div class="mb-4 flex items-center gap-2 text-sm">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               class="px-4 py-1.5 rounded-full font-medium
               {{ $filter === 'all'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                Semua
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="px-4 py-1.5 rounded-full font-medium
               {{ $filter === 'unread'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                Belum dibaca
            </a>
        </div>

        {{-- Daftar notifikasi --}}
        <div class="space-y-3">
            @forelse($notifications as $n)
                @php
                    $t = $n->type;
                    $d = $n->data ?? [];
                    $unread = is_null($n->read_at);
                @endphp

                {{-- ================= PERMINTAAN MUTASI (untuk ADMIN) ================= --}}
                @if($t === \App\Notifications\MutasiRequestedNotification::class)
                    <div class="rounded-2xl border
                        {{ $unread ? 'border-blue-200 bg-blue-50/70' : 'border-gray-200 bg-white' }}
                        p-4 shadow-sm">
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">
                                    Permintaan Mutasi
                                    @if($unread)
                                        <span class="ml-2 text-[10px] inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 uppercase tracking-wide">
                                            Baru
                                        </span>
                                    @else
                                        <span class="ml-2 text-[10px] inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 uppercase tracking-wide">
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-700">
                                    Barang:
                                    <span class="font-semibold">{{ $d['barang_nama'] ?? '-' }}</span>
                                    <span class="ml-1 text-[10px] text-gray-500">
                                        ({{ $d['kode_register'] ?? '-' }})
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700">
                                    Dari: {{ $d['from_name'] ?? '-' }} → Ke: {{ $d['to_name'] ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Tanggal: {{ $d['tanggal'] ?? '-' }}
                                </div>
                                @if(!empty($d['catatan']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Catatan: {{ $d['catatan'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                {{-- checkbox pilih untuk hapus --}}
                                <label x-show="deleteMode" x-cloak
                                       class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                    <input type="checkbox"
                                           class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                           value="{{ $n->id }}"
                                           @change="
                                            if ($event.target.checked) {
                                                if (!selected.includes('{{ $n->id }}')) selected.push('{{ $n->id }}')
                                            } else {
                                                selected = selected.filter(x => x !== '{{ $n->id }}')
                                            }">
                                    <span>Pilih</span>
                                </label>

                                {{-- ADMIN: tidak ada tombol tandai dibaca --}}
                                @if($unread && !$isAdmin)
                                    <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="text-[11px] text-gray-500 hover:text-gray-700">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol approve / reject hanya untuk ADMIN & satu kali (saat UNREAD) --}}
                        @if($isAdmin && $unread)
                            <div class="mt-3 grid gap-2 md:grid-cols-2">
                                <form action="{{ route('mutasi.request.approve', $n->id) }}"
                                      method="POST"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <input type="text"
                                           name="note"
                                           class="flex-1 border rounded-full px-3 py-2 text-xs"
                                           placeholder="Catatan (opsional)">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-full bg-green-600 text-white text-xs font-semibold hover:bg-green-700"
                                    >
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('mutasi.request.reject', $n->id) }}"
                                      method="POST"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <input type="text"
                                           name="note"
                                           class="flex-1 border rounded-full px-3 py-2 text-xs"
                                           placeholder="Catatan (opsional)">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-full bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700"
                                    >
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($isAdmin && !$unread)
                            <div class="mt-3 text-xs text-gray-500 italic">
                                Permintaan ini sudah diproses
                            </div>
                        @endif
                    </div>

                {{-- ============ HASIL PERMINTAAN MUTASI (untuk PENGAJU) ============ --}}
                @elseif($t === \App\Notifications\MutasiRequestResolvedNotification::class)
                    <div class="rounded-2xl border
                        {{ $unread ? 'border-blue-200 bg-blue-50/70' : 'border-gray-200 bg-white' }}
                        p-4 shadow-sm">
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">
                                    Permintaan Mutasi
                                    <span class="ml-1
                                        {{ ($d['status'] ?? '') === 'Approved' ? 'text-green-600' : 'text-rose-600' }}">
                                        {{ $d['status'] ?? '' }}
                                    </span>
                                    @if($unread)
                                        <span class="ml-2 text-[10px] inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 uppercase tracking-wide">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-700">
                                    Barang:
                                    <span class="font-semibold">{{ $d['barang_nama'] ?? '-' }}</span>
                                    <span class="ml-1 text-[10px] text-gray-500">
                                        ({{ $d['kode_register'] ?? '-' }})
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700">
                                    Ke: {{ $d['to_name'] ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Diputus oleh: {{ $d['decided_by'] ?? '-' }}
                                </div>
                                @if(!empty($d['note']))
                                    <div class="mt-1 text-xs text-gray-500">
                                        Catatan: {{ $d['note'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                {{-- checkbox pilih --}}
                                <label x-show="deleteMode" x-cloak
                                       class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                    <input type="checkbox"
                                           class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                           value="{{ $n->id }}"
                                           @change="
                                            if ($event.target.checked) {
                                                if (!selected.includes('{{ $n->id }}')) selected.push('{{ $n->id }}')
                                            } else {
                                                selected = selected.filter(x => x !== '{{ $n->id }}')
                                            }">
                                    <span>Pilih</span>
                                </label>

                                {{-- tandai dibaca hanya untuk bukan admin --}}
                                @if($unread && !$isAdmin)
                                    <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="text-[11px] text-gray-500 hover:text-gray-700">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                {{-- ============ CATATAN PEMELIHARAAN (MaintenanceNoteNotification) ============ --}}
                @elseif($t === \App\Notifications\MaintenanceNoteNotification::class)
                    <div class="rounded-2xl border
                        {{ $unread ? 'border-amber-200 bg-amber-50/70' : 'border-gray-200 bg-white' }}
                        p-4 shadow-sm">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1">
                                    <div class="text-sm font-semibold text-gray-900">
                                        Catatan Pemeliharaan
                                        @if($unread)
                                            <span class="ml-2 text-[10px] inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 uppercase tracking-wide">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-xs text-gray-700">
                                        Barang:
                                        <span class="font-semibold">{{ $d['barang_nama'] ?? '-' }}</span>
                                        <span class="ml-1 text-[10px] text-gray-500">
                                        ({{ $d['kode_register'] ?? '-' }})
                                    </span>
                                    </div>
                                    <div class="text-xs text-gray-700">
                                        Status: <span class="font-medium">{{ $d['status'] ?? '-' }}</span>
                                    </div>
                                    @if(!empty($d['message']))
                                        <div class="mt-1 text-xs text-gray-600">
                                            {{ $d['message'] }}
                                        </div>
                                    @endif
                                    @if(!empty($d['admin_note']))
                                        <div class="mt-1 text-xs text-gray-500">
                                            Catatan:
                                            <span class="italic">"{{ Str::limit($d['admin_note'], 120) }}"</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                {{-- checkbox pilih --}}
                                <label x-show="deleteMode" x-cloak
                                       class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                    <input type="checkbox"
                                           class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                           value="{{ $n->id }}"
                                           @change="
                                            if ($event.target.checked) {
                                                if (!selected.includes('{{ $n->id }}')) selected.push('{{ $n->id }}')
                                            } else {
                                                selected = selected.filter(x => x !== '{{ $n->id }}')
                                            }">
                                    <span>Pilih</span>
                                </label>

                                @if($unread)
                                    <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="text-[11px] text-gray-500 hover:text-gray-700">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 py-10 text-center text-sm text-gray-500">
                    Tidak ada notifikasi.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->withQueryString()->links() }}
        </div>
    </div>
</x-layout>
