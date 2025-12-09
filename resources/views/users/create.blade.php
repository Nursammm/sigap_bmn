<x-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    @php
        $roles = [
            \App\Models\User::ROLE_ADMIN => 'ADMIN',
            \App\Models\User::ROLE_PENGELOLA => 'PENGELOLA',
        ];
        $selectedRole = old('role', \App\Models\User::ROLE_PENGELOLA);
    @endphp

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Form Tambah Pengguna</h2>
                <p class="text-sm text-gray-500 mt-1">Isi data akun baru dan tentukan role akses.</p>
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

            <form action="{{ route('users.store') }}" method="POST" class="px-6 py-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" class="w-full border rounded-md px-3 py-2"
                               placeholder="Nama lengkap" value="{{ old('name') }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded-md px-3 py-2"
                               value="{{ old('email') }}" required>
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
                        <p class="text-xs text-gray-500 mt-1">Admin memiliki akses penuh, Pengelola untuk operasional.</p>
                    </div>


                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded-md px-3 py-2" required>
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter.</p>
                    </div>

                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300">
                        Kembali
                    </a>

                    <button type="submit"
                            class="px-4 py-2 text-sm rounded-md bg-blue-500 hover:bg-blue-600 text-white">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-layout>
