<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
            
            <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">
                Login
            </h1>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <input type="email" name="email" placeholder="E-mail"
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300" required>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password"
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300" required>
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                        Forgot?
                    </a>
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                    Log in
                </button>
            </form>

            <!-- Register -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Register</a>
            </p>
        </div>
    </div>
</x-guest-layout>
