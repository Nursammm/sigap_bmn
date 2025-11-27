{{-- resources/views/user/edit.blade.php --}}
<x-layout>
    <x-slot name="title">Edit Pengguna</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Form Edit Pengguna</h2>
            </div>

            {{-- FORM --}}
            <form action="{{ route('users.update', $user->id) }}" method="POST" id="editForm" class="px-6 py-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text"
                               name="name"
                               class="w-full border rounded-md px-3 py-2"
                               value="{{ old('name', $user->name) }}"
                               required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email"
                               name="email"
                               class="w-full border rounded-md px-3 py-2"
                               value="{{ old('email', $user->email) }}"
                               required>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" class="w-full border rounded-md px-3 py-2">
                            <option value="ADMIN"    @selected(old('role', $user->role) === 'ADMIN')>ADMIN</option>
                            <option value="OPERATOR" @selected(old('role', $user->role) === 'OPERATOR')>PENGELOLA</option>
                        </select>
                    </div>

                    {{-- Password (opsional) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password"
                               name="password"
                               class="w-full border rounded-md px-3 py-2"
                               placeholder="Password">
                    </div>

                </div>

                {{-- BUTTONS --}}
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300">
                        Kembali
                    </a>

                    {{-- Tombol Simpan diubah menjadi button biasa --}}
                    <button type="button" id="btnSubmit"
                            class="px-4 py-2 text-sm rounded-md bg-blue-500 hover:bg-blue-600 text-white">
                        Simpan
                    </button>
                </div>
            </form> {{-- ← FORM TERTUTUP DI SINI --}}

        </div>
    </div>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('btnSubmit').addEventListener('click', function () {
            Swal.fire({
                title: "Simpan Perubahan?",
                text: "Data pengguna akan diperbarui.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Simpan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('editForm').submit();
                }
            });
        });
    </script>

</x-layout>
