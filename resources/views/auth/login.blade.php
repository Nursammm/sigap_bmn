<x-guest-layout>
    <div class="relative flex items-center justify-center">

        <!-- Card Transparan -->
        <div class="relative z-10 w-full max-w-sm backdrop-blur-xl border border-white/30
                    p-8 rounded-3xl shadow-lg">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img
                    src="{{ asset('storage/gap2.png') }}"
                    alt="Logo"
                    class="w-28 h-24 md:w-32 md:h-28 drop-shadow-md"
                >
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

                <!-- Password with toggle -->
                <div class="relative">
                    <input type="password" name="password" placeholder="Password"
                        class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl
                               text-white placeholder-gray-200 focus:ring-2 focus:ring-indigo-400 focus:outline-none pr-11"
                        required id="passwordInput">
                    <button type="button"
                            class="absolute inset-y-0 right-0 px-3 text-gray-200 hover:text-white"
                            aria-label="Toggle password visibility"
                            onclick="togglePassword()">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3 12s3.5-7 9-7 9 7 9 7-3.5 7-9 7-9-7-9-7z"/>
                            <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                        </svg>
                    </button>
                </div>

                <div>
                    
                </div>


            <!-- Tombol Login -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                Sign In
            </button>
        </form>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.5 0-9-7-9-7a18.705 18.705 0 014.5-5.5M9.88 9.88a3 3 0 104.24 4.24M10.6 5.1A9.965 9.965 0 0112 5c5.5 0 9 7 9 7a18.72 18.72 0 01-2.81 3.59"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20 20 4"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12s3.5-7 9-7 9 7 9 7-3.5 7-9 7-9-7-9-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>';
    }
</script>
</x-guest-layout>
