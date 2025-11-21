<x-layout>
    <x-slot name="title">Ajukan Pemeliharaan</x-slot>

    <div class="max-w-xl mx-auto mt-8 bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-lg font-semibold mb-4">
            Ajukan Pemeliharaan
        </h1>

        <div class="mb-4 p-3 rounded-lg bg-gray-50 text-sm text-gray-700">
            <div><span class="font-semibold">Barang:</span> {{ $barang->nama_barang }}</div>
            @if($barang->kode_register ?? false)
                <div><span class="font-semibold">Kode Register:</span> {{ $barang->kode_register }}</div>
            @endif
        </div>

        <form action="{{ route('maintenance.store', $barang) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai"
                           value="{{ old('tanggal_mulai') }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    @error('tanggal_mulai')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai"
                           value="{{ old('tanggal_selesai') }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    @error('tanggal_selesai')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Biaya</label>

                <input
                    type="text"
                    id="biaya_format"
                    value="{{ old('biaya') ? number_format(old('biaya'), 0, ',', '.') : (isset($m->biaya) && $m->biaya !== null ? number_format((int)$m->biaya, 0, ',', '.') : '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40 px-3 py-2"
                    oninput="formatRupiah(this, 'biaya')"
                    onblur="formatRupiah(this, 'biaya')"
                    onfocus="unformatForEdit(this)"
                    autocomplete="off"
                >

                <input type="hidden" name="biaya" id="biaya" value="{{ old('biaya', isset($m->biaya) ? (int)$m->biaya : '') }}">

                @error('biaya')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Uraian</label>
                <textarea name="uraian" rows="4"
                          class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40"
                          placeholder="Detail pekerjaan pemeliharaan…">{{ old('uraian') }}</textarea>
                @error('uraian')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Foto Kondisi Barang (opsional)
                </label>

                <input
                    type="file"
                    name="photos[]"
                    accept="image/*"
                    multiple
                    class="block w-full text-sm text-gray-700
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:text-sm file:font-semibold
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">
                @error('photos')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div id="preview-create" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ url()->previous() }}"
                   class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:text-gray-800">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    Simpan Pengajuan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('input[name="photos[]"]');
            const box   = document.getElementById('preview-create');
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
        function formatRupiah(el, hiddenId) {
            let onlyNums = (el.value || '').toString().replace(/[^0-9]/g, '');
            const hidden = document.getElementById(hiddenId);
            if (hidden) hidden.value = onlyNums;

            if (!onlyNums) {
                el.value = '';
                return;
            }

            el.value = onlyNums.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function unformatForEdit(el) {
            el.value = (el.value || '').toString().replace(/[^0-9]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const display = document.getElementById('biaya_format');
            const hidden  = document.getElementById('biaya');
            if (display && hidden && (display.value === '' && hidden.value)) {
                display.value = (hidden.value || '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });
    </script>
</x-layout>
