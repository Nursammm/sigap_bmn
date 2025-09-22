<x-layout>
    <x-slot name="title">Ajukan Pemeliharaan</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            <div class="px-6 md:px-8 py-5 border-b bg-gray-50 flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Pemeliharaan • {{ $barang->nama_barang }}</h2>
                    <p class="text-sm text-gray-500">Kode: <span class="font-mono">{{ $barang->kode_register }}</span></p>
                </div>
                <a href="{{ route('maintenance.index',['barang_id'=>$barang->id]) }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">← Kembali</a>
            </div>

            <div class="px-6 md:px-8 py-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('maintenance.store',$barang) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                            <select name="jenis" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40" required>
                                <option value="">Pilih jenis</option>
                                @foreach ($jenisList as $j)
                                    <option value="{{ $j }}" @selected(old('jenis')===$j)>{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                            <input type="text" name="vendor" value="{{ old('vendor') }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40" placeholder="Opsional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Biaya (Rp)</label>
                            <input type="number" name="biaya" value="{{ old('biaya',0) }}" min="0" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                        </div>

                        @if(auth()->user()->role === 'admin')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                                @foreach (['Diajukan','Disetujui','Proses','Selesai','Ditolak'] as $s)
                                    <option value="{{ $s }}" @selected(old('status')===$s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Admin dapat langsung menyetujui.</p>
                        </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian</label>
                        <textarea name="uraian" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40" placeholder="Detail pekerjaan…">{{ old('uraian') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran (PDF/JPG/PNG, max 5MB)</label>
                        <input type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500/40">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layout>
