<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">

    <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-md text-center">
        
        <!-- Icon -->
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
        </div>

        <!-- // need username  -->
        <p class="text-lg font-semibold text-gray-800 mb-2">{{ Auth::user()->name }}</p>

        <!-- Heading -->
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Your account is pending approval</h1>
        <p class="text-gray-600 mb-6">Please wait for an administrator to approve your account before you can access the system.</p>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition duration-300">
                Logout
            </button>
        </form>

    </div>

</body>
</html>
