<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-blue-500 to-purple-600">

    <div class="w-full max-w-md bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl p-8">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-gray-500 text-sm mt-1">Create a new secure password</p>
        </div>

        <!-- Error Message -->
        @if($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('admin.reset.password') }}" class="space-y-5">
            @csrf

            <!-- New Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    New Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 
                           focus:ring-2 focus:ring-indigo-500 
                           focus:outline-none transition duration-200"
                >
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 
                           focus:ring-2 focus:ring-indigo-500 
                           focus:outline-none transition duration-200"
                >
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full py-3 rounded-xl bg-indigo-600 
                       hover:bg-indigo-700 text-white 
                       font-semibold transition duration-300 shadow-lg">
                Reset Password
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}"
               class="text-sm text-gray-500 hover:text-gray-700 hover:underline transition">
                Back to Login
            </a>
        </div>

    </div>

</body>
</html>
