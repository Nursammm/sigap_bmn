<x-layout>
    <x-slot name="title">Edit Pengguna</x-slot>

    @php
        $roles = [
            \App\Models\User::ROLE_ADMIN => 'Admin',
            \App\Models\User::ROLE_PENGELOLA => 'Pengelola',
        ];
        $selectedRole = old('role', $user->role);
    @endphp

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Form Edit Pengguna</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui data akun dan role pengguna.</p>
            </div>

            @if ($errors->any())
                <div class="px-6 pt-4">
                    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST" id="editForm" class="px-6 py-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text"
                               name="name"
                               class="w-full border rounded-md px-3 py-2"
                               value="{{ old('name', $user->name) }}"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email"
                               name="email"
                               class="w-full border rounded-md px-3 py-2"
                               value="{{ old('email', $user->email) }}"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" class="w-full border rounded-md px-3 py-2">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected($selectedRole === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ubah role untuk mengatur tingkat akses.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password"
                               name="password"
                               class="w-full border rounded-md px-3 py-2"
                               placeholder="Kosongkan jika tidak diubah">
                    </div>

                </div>
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300">
                        Kembali
                    </a>
                    <button type="button" id="btnSubmit"
                            class="px-4 py-2 text-sm rounded-md bg-blue-500 hover:bg-blue-600 text-white">
                        Simpan
                    </button>
                </div>
            </form> 

        </div>
    </div>

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
