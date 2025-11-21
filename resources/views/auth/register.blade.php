<x-guest-layout>
    <div class="relative flex items-center justify-center">
        <div class="relative z-10 w-full max-w-md backdrop-blur-xl border border-white/30
                    p-10 rounded-3xl shadow-lg">

            <!-- Logo -->
            <div class="flex justify-center mb-5">
                <img src="{{ asset('storage/gap2.png') }}" alt="Logo" class="w-19 h-16 drop-shadow-md">
            </div>

            <!-- Judul -->
            <h1 class="text-2xl font-semibold text-white text-center mb-2">Create an Account</h1>
            <p class="text-center text-sm text-gray-200 mb-6">Register to get started</p>

            <!-- Form Register -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <input id="name" type="text" name="name" placeholder="Full Name"
                        class="w-full px-5 py-3 bg-white/20 border border-white/30 rounded-xl 
                               text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                        value="{{ old('name') }}" required autofocus autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-pink-300" />
                </div>

                <!-- Email -->
                <div>
                    <input id="email" type="email" name="email" placeholder="Email Address"
                        class="w-full px-5 py-3 bg-white/20 border border-white/30 rounded-xl 
                               text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                        value="{{ old('email') }}" required autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-pink-300" />
                </div>

                <!-- Password -->
                <div>
                    <input id="password" type="password" name="password" placeholder="Password"
                        class="w-full px-5 py-3 bg-white/20 border border-white/30 rounded-xl 
                               text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                        required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-pink-300" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password"
                        class="w-full px-5 py-3 bg-white/20 border border-white/30 rounded-xl 
                               text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                        required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-pink-300" />
                </div>

                <!-- Tombol Register -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Register
                </button>
            </form>

            <!-- Link ke Login -->
            <p class="text-center text-sm text-gray-300 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>


