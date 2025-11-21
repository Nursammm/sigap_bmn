<x-layout>
   <x-slot name="title">Profile</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <!-- Header Kartu -->
                <div class="border-b px-6 py-4">
                    <h3 class="text-sm font-medium text-gray-700">Profile</h3>
                </div>

                <!-- Body Kartu -->
                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="px-6 py-6"
                >
                    @csrf
                    @method('PATCH')   {{-- Breeze/Jetstream pakai PATCH --}}

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Kolom Kiri: Foto -->
                        <div class="md:col-span-1">
                            <div class="flex flex-col items-center">
                                <!-- Preview Foto -->
                                <img
                                    id="avatarPreview"
                                    class="w-40 h-40 rounded-full object-cover border border-gray-200"
                                    src="{{ auth()->user()->profile_photo_url ?? asset('images/avatar-placeholder.png') }}"
                                    alt="Avatar"
                                />

                                <!-- Input File -->
                                <label class="mt-5 block text-sm font-medium text-gray-700">
                                    Change Profile Photo
                                </label>
                                <input
                                    type="file"
                                    name="photo"
                                    id="photo"
                                    accept="image/*"
                                    class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4
                                           file:rounded-md file:border-0 file:text-sm file:font-semibold
                                           file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                    onchange="previewAvatar(event)"
                                >

                                @error('photo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan: Form -->
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 gap-4">

                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name', auth()->user()->name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', auth()->user()->email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Old Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Old Password</label>
                                    <input
                                        type="password"
                                        name="current_password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        autocomplete="current-password"
                                    >
                                    @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input
                                        type="password"
                                        name="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        autocomplete="new-password"
                                    >
                                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        autocomplete="new-password"
                                    >
                                </div>

                                <!-- Tombol -->
                                <div class="pt-2">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        Update Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Script Preview -->
                <script>
                    function previewAvatar(e){
                        const file = e.target.files?.[0];
                        if(!file) return;
                        const img = document.getElementById('avatarPreview');
                        img.src = URL.createObjectURL(file);
                    }
                </script>
            </div>
        </div>
    </div>
</x-layout>
