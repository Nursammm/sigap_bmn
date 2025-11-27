<x-guest-layout>
    <div class="relative flex items-center justify-center">

        <!-- Card Transparan -->
        <div class="relative z-10 w-full max-w-sm backdrop-blur-xl border border-white/30
                    p-8 rounded-3xl shadow-lg">

            <!-- Logo -->
            <div class="flex justify-center mb-5">
                <img src="{{ asset('storage/gap2.png') }}" alt="Logo" class="w-19 h-18 drop-shadow-md">
            </div>

            <!-- Judul -->
            <h1 class="text-2xl font-semibold text-white text-center mb-2">Welcome Back</h1>
            <p class="text-center text-sm text-gray-200 mb-6">Sign in to your account</p>

            <!-- Notifikasi Error -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-400 text-red-200 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <input type="email" name="email" placeholder="Email Address"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl
                           text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                    required>

                <!-- Password -->
                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl
                           text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                    required>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between text-sm text-gray-200">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-400 bg-white/20">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-indigo-300 hover:underline">
                        Forgot password?
                    </a>
                </div>

                <!-- Tombol Login -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
