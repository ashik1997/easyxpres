<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">

    <!-- Center Content -->
    <div class="flex-grow flex items-center justify-center px-4">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 mt-10">

            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
                <p class="text-sm text-gray-500">Register to get started</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Name
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Your full name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300
                        focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        placeholder="Enter your phone number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300
                        focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address (Optional)
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300
                        focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300
                        focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" required
                        placeholder="••••••••"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300
                        focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Already registered?
                    </a>

                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700
                        text-white rounded-lg font-semibold transition">
                        Register
                    </button>
                </div>

            </form>

        </div>

    </div>

    <!-- Footer -->
    <footer class="py-6 text-center">
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
            © {{ date('Y') }} Your Easyxpres. All rights reserved.
        </p>
    </footer>

</body>

</html>
