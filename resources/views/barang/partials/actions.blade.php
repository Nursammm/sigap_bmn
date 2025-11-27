@php
    /** @var \App\Models\Barang $barang */
    $isAdmin = auth()->user()?->role === 'admin';
@endphp

<div class="flex items-center justify-center gap-2">

    <a href="{{ route('barang.show', $barang) }}"
       class="text-gray-500 hover:text-gray-700" title="Detail">
        <i class="fas fa-eye"></i>
    </a>

    @if ($isAdmin)
    <a href="{{ route('barang.edit', $barang) }}"
       class="text-blue-600 hover:text-blue-700" title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    @endif

    @if ($isAdmin)
        <a href="{{ route('mutasi.create', $barang) }}"
           class="text-purple-600 hover:text-purple-700" title="Mutasi Barang">
            <i class="fas fa-exchange-alt"></i>
        </a>
    @else
        <a href="{{ route('mutasi.create', $barang) }}"
           class="text-purple-600 hover:text-purple-700" title="Ajukan Mutasi (butuh persetujuan Admin)">
            <i class="fas fa-flag"></i>
        </a>
    @endif

    @if ($isAdmin)
        <!-- TOMBOL HAPUS (SweetAlert) -->
        <button type="button"
            class="text-red-600 hover:text-red-700"
            title="Hapus"
            onclick="deleteBarang('{{ $barang->id }}')">
            <i class="fas fa-trash-alt"></i>
        </button>

        <!-- FORM DELETE -->
        <form id="delete-form-{{ $barang->id }}" action="{{ route('barang.destroy', $barang) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

<!-- SCRIPT SWEETALERT -->
<script>
function deleteBarang(id) {
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
