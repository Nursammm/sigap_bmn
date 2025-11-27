{{-- resources/views/user/create.blade.php --}}
<x-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Form Tambah Pengguna</h2>
            </div>

            {{-- FORM --}}
            <form action="{{ route('users.store') }}" method="POST" class="px-6 py-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="name" class="w-full border rounded-md px-3 py-2"
                               placeholder="Nama lengkap" required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded-md px-3 py-2" required>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" class="w-full border rounded-md px-3 py-2">
                            <option value="ADMIN">ADMIN</option>
                            <option value="OPERATOR">PENGELOLA
                                
                            </option>
                        </select>
                    </div>


                    {{-- Password --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded-md px-3 py-2" required>
                    </div>

                </div>

                {{-- BUTTONS --}}
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
