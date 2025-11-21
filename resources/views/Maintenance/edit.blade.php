<x-layout>
    <x-slot name="title">Edit Pemeliharaan</x-slot>

    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        $photos = is_array($m->photo_path) ? $m->photo_path : (empty($m->photo_path) ? [] : [$m->photo_path]);
    @endphp

    <div class="flex items-start justify-center min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 px-4 pt-10 pb-20">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-3xl border border-gray-200">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Pemeliharaan</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Barang:
                    <span class="font-semibold">{{ $m->barang->nama_barang ?? '-' }}</span>
                    @if(!empty($m->barang->kode_register))
                        <span class="text-xs text-gray-500">
                            ({{ $m->barang->kode_register }})
                        </span>
                    @endif
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Status saat ini:
                    <span class="font-semibold">{{ $m->status }}</span>
                </p>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('maintenance.update', $m->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Tanggal Mulai
                        </label>
                        <input
                            type="date"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $m->tanggal_mulai?->toDateString()) }}"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Tanggal Selesai
                        </label>
                        <input
                            type="date"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', optional($m->tanggal_selesai)->toDateString()) }}"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Biaya (Rp)
                        </label>

                        <input
                            type="text"
                            id="biaya_format"
                            value="{{ number_format(old('biaya', $m->biaya), 0, ',', '.') }}"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            oninput="formatRupiah(this)"
                        >

                        <input type="hidden" name="biaya" id="biaya" value="{{ old('biaya', $m->biaya) }}">

                        <span class="text-xs text-gray-500">
                            Isi 0 jika tanpa biaya atau belum diketahui.
                        </span>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Status
                        </label>

                        @if($isAdmin)
                            <select
                                name="status"
                                class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            >
                                @foreach(['Diajukan','Disetujui','Proses','Selesai','Ditolak'] as $st)
                                    <option value="{{ $st }}" {{ old('status', $m->status) === $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input
                                type="text"
                                class="w-full border border-gray-300 p-2.5 rounded-lg bg-gray-50 text-gray-700"
                                value="{{ $m->status }}"
                                disabled
                            >
                            <input type="hidden" name="status" value="{{ $m->status }}">
                            <span class="text-xs text-gray-500">
                                Status hanya dapat diubah oleh admin.
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Uraian Pekerjaan
                    </label>
                    <textarea
                        name="uraian"
                        rows="4"
                        class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >{{ old('uraian', $m->uraian) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Foto yang Sudah Diunggah
                    </label>

                    @if(count($photos))
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($photos as $p)
                                <div class="border rounded-lg p-2 space-y-2">
                                    <img
                                        src="{{ asset('storage/'.$p) }}"
                                        alt="Foto Pemeliharaan"
                                        class="w-full h-28 object-cover rounded"
                                    >
                                    <div class="flex items-center justify-between text-xs">
                                        <a href="{{ asset('storage/'.$p) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                        <label class="inline-flex items-center gap-1 text-red-600">
                                            <input type="checkbox" name="remove_photos[]" value="{{ $p }}" class="rounded border-gray-300">
                                            Hapus
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500">Belum ada lampiran.</div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Tambah Foto
                    </label>

                    <input
                        type="file"
                        name="photos[]"
                        accept=".jpg,.jpeg,.png,.webp"
                        multiple
                        class="block w-full text-sm text-gray-700
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100">

                    <span class="block text-xs text-gray-500 mt-2">
                        Maksimal 10 foto, masing-masing ≤ 4MB. Format: JPG/PNG/WEBP.
                    </span>

                    @error('photos')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div id="preview-edit" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                </div>

                @if($isAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Catatan Admin untuk Pengelola
                        </label>
                        <textarea
                            name="admin_note"
                            rows="3"
                            class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Tuliskan catatan kepada pengelola (opsional)"
                        >{{ old('admin_note', $m->admin_note) }}</textarea>
                        <span class="text-xs text-gray-500">
                            Setiap perubahan catatan atau status akan mengirim notifikasi ke pengelola.
                        </span>
                    </div>
                @elseif(!empty($m->admin_note))
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                        <div class="text-xs font-semibold text-amber-800 mb-1">
                            Catatan dari Admin:
                        </div>
                        <div class="text-sm text-amber-900">
                            {{ $m->admin_note }}
                        </div>
                    </div>
                @endif

                <div class="pt-6 flex gap-3 justify-end">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition"
                    >
                        Simpan
                    </button>
                    <a
                        href="{{ route('maintenance.index', ['barang_id' => $m->barang_id]) }}"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium shadow hover:bg-gray-300 transition"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('input[name="photos[]"]');
            const box   = document.getElementById('preview-edit');
            if (!input || !box) return;

            input.addEventListener('change', () => {
                box.innerHTML = '';
                [...input.files].forEach(f => {
                    const url = URL.createObjectURL(f);
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'w-full h-28 object-cover rounded-lg border';
                    box.appendChild(img);
                });
            });
        });
    </script>


    <script>
        function formatRupiah(el) {
            let angka = el.value.replace(/\./g, '').replace(/,/g, '');

            document.getElementById('biaya').value = angka;

            el.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>

</x-layout>
