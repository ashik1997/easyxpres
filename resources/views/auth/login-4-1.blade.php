<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 relative">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
            <p class="text-sm text-gray-500">Sign in to your account</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('phone')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold transition duration-300">
                Log in
            </button>
        </form>

        <!-- Register -->
        @if (Route::has('register'))
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Don’t have an account?
                <a href="{{ route('register') }}"
                    class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Register
                </a>
            </p>
        </div>
        @endif

    </div>

    <!-- Footer Social Media -->
    <footer class="absolute bottom-6 w-full text-center">
        <div class="flex justify-center space-x-5">

        @php
        $appSetup = DB::table('app_setups')->where('id', 1)->first();
        @endphp

            <!-- Facebook -->
            <a href="{{ $appSetup->facebook }}" target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-blue-500 transition duration-300">
                <i class="fab fa-facebook-f"></i>
            </a>

            <!-- YouTube -->
            <a href="{{ $appSetup->youtube }}" target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-red-500 transition duration-300">
                <i class="fab fa-youtube"></i>
            </a>

            <!-- Telegram -->
            <a href="{{ $appSetup->telegram }}" target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-sky-500 transition duration-300">
                <i class="fab fa-telegram"></i>
            </a>

        </div>

        <p class="text-white text-xs mt-3 opacity-80">
            © {{ date('Y') }} Easyxpres. All rights reserved.
        </p>
    </footer>

</body>

</html>
