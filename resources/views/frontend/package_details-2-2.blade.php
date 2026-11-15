<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastr CSS -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- User Welcome -->
            <h1 class="text-xl font-bold">Welcome, {{ Auth::user()->name }}</h1>

            <!-- Logout -->
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf
                <button
                    type="submit"
                    class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-semibold transition"
                >
                    Logout
                </button>
            </form>
        </div>
    </nav>


    <div class="max-w-7xl mx-auto p-6">
        <a
            href="{{ route('dashboard') }}"
            class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg font-semibold transition mb-4"
        >
            &larr; Back to Dashboard
        </a>
    </div>

    <!-- // card details Package Details data fetch from controller -->
    <main class="max-w-7xl mx-auto p-6">
        <div class="bg-white shadow-md rounded-lg p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($subpackages as $subpackage)
                    <div class="border p-4 rounded-lg shadow-sm">

                        @php
                            $package_id = $subpackage->package_id;
                            $package_image = DB::table('packages')->where('id', $package_id)->value('image_icon');
                        @endphp

                        @if ($package_image)
                            <img
                                src="{{ asset('storage/' . $package_image) }}"
                                alt="Package Image"
                                class="w-16 h-16 object-cover rounded-full mb-4"
                            >
                        @endif

                        <h4 class="text-lg font-bold mb-2">{{ $subpackage->title }}</h4>
                        <p class="mb-2 text-red-600">Amount: {{ $subpackage->amount }}</p>
                        <p class="font-semibold">Commission: {{ $subpackage->commission }}</p>
                        <p class="font-semibold">Offer Price: {{ $subpackage->offer_price }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </main>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Modal functions
        function openBalanceModal() {
            document.getElementById('balanceModal').classList.remove('hidden');
        }

        function closeBalanceModal() {
            document.getElementById('balanceModal').classList.add('hidden');
        }

        // Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": 4000
        };

        // Success / Error flash messages
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        // Validation errors
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
            // Open modal if validation fails
            openBalanceModal();
        @endif
    </script>

    <script>
        function showDepositTable() {
            // Hide main dashboard
            document.querySelector('main').classList.add('hidden');
            // Show deposit table
            document.getElementById('depositTable').classList.remove('hidden');
        }

        function backToDashboard() {
            // Show main dashboard
            document.querySelector('main').classList.remove('hidden');
            // Hide deposit table
            document.getElementById('depositTable').classList.add('hidden');
        }
    </script>

    <script>
        function showPackagesTable() {
            // Hide main dashboard
            document.querySelector('main').classList.add('hidden');
            // Hide deposit table if open
            document.getElementById('depositTable').classList.add('hidden');
            // Show packages table
            document.getElementById('packagesTable').classList.remove('hidden');
        }

        function backToDashboard() {
            // Show main dashboard
            document.querySelector('main').classList.remove('hidden');
            // Hide deposit table
            document.getElementById('depositTable').classList.add('hidden');
            // Hide packages table
            document.getElementById('packagesTable').classList.add('hidden');
        }
    </script>



</body>

</html>
