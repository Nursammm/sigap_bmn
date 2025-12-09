<x-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>

    <div class="max-w-7xl mx-auto space-y-4">

        @if (session('success'))
            <div class="px-4 py-3 rounded-xl bg-green-50 text-green-700 border border-green-200 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200 shadow-sm">
                {{ session('error') }}
            </div>
        @endif



        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Pengguna Terdaftar</h2>

                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-md
                          bg-blue-500 hover:bg-blue-600 text-white shadow-sm">
                    Tambah
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 border text-left w-16">ID</th>
                            <th class="px-3 py-2 border text-left">Nama</th>
                            <th class="px-3 py-2 border text-left">Email</th>
                            <th class="px-3 py-2 border text-left">Role</th>
                            <th class="px-3 py-2 border text-center w-40">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border align-middle">
                                    {{ $user->id }}
                                </td>
                                <td class="px-3 py-2 border align-middle">
                                    {{ $user->name }}
                                </td>
                                <td class="px-3 py-2 border align-middle">
                                    {{ $user->email }}
                                </td>
                                <td class="px-3 py-2 border align-middle">
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset
                                        {{ ($user->role === 'admin') ? 'bg-blue-50 text-blue-700 ring-blue-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200' }}">
                                        {{ strtoupper($user->role ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 border text-center align-middle">
                                    <div class="inline-flex gap-1">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="px-3 py-1 text-xs font-semibold rounded-md
                                                  bg-yellow-400 hover:bg-yellow-500 text-gray-800">
                                            Edit
                                        </a>

                                        <button onclick="showDeleteModal({{ $user->id }})"
                                            class="px-3 py-1 text-xs font-semibold rounded-md
                                                   bg-red-500 hover:bg-red-600 text-white">
                                            Hapus
                                        </button>

                                        <form id="delete-form-{{ $user->id }}"
                                              action="{{ route('users.destroy', $user->id) }}"
                                              method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 border text-center text-gray-500">
                                    Belum ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="delete-modal"
         class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50">
        <div class="bg-white w-full max-w-sm rounded-xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-600">Apakah Anda yakin ingin menghapus pengguna ini?</p>

            <div class="mt-5 flex justify-end gap-2">
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 rounded-md border bg-gray-100 hover:bg-gray-200">
                    Batal
                </button>

                <button id="confirm-delete-btn"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedId = null;

        function showDeleteModal(id) {
            selectedId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.getElementById('delete-modal').classList.add('flex');
        }

        function closeDeleteModal() {
            selectedId = null;
            document.getElementById('delete-modal').classList.add('hidden');
            document.getElementById('delete-modal').classList.remove('flex');
        }

        document.getElementById('confirm-delete-btn').onclick = function () {
            if (selectedId) {
                document.getElementById('delete-form-' + selectedId).submit();
            }
        }
    </script>

</x-layout>
