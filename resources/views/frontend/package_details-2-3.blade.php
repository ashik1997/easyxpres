<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>User Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastr -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- ================= NAVBAR ================= -->
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">
                Welcome, {{ Auth::check() ? Auth::user()->name : 'Guest' }}
            </h1>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf
                <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-xl font-semibold transition shadow">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- ================= BACK BUTTON ================= -->
    <div class="max-w-7xl mx-auto px-6 pt-6">
        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300
              px-4 py-2 rounded-xl font-semibold transition shadow"
        >
            ← Back to Dashboard
        </a>
    </div>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="max-w-7xl mx-auto p-6">

        <div class="bg-white rounded-2xl shadow-lg p-6">


            <!-- ===== CARD GRID ===== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach ($subpackages as $subpackage)
                    <div
                        class="group bg-white rounded-2xl border border-gray-100
                           shadow-md hover:shadow-2xl
                           hover:-translate-y-1 transition-all duration-300">

                        <!-- Header -->
                        <div class="flex items-center gap-4 p-5 border-b">
                            @if ($subpackage->package?->image_icon)
                                <div
                                    class="w-14 h-14 bg-indigo-50 rounded-xl
                                        flex items-center justify-center shadow-inner">
                                    <img
                                        src="{{ asset('storage/' . $subpackage->package->image_icon) }}"
                                        class="w-10 h-10 object-contain"
                                        alt="Package Icon"
                                    >
                                </div>
                            @endif

                            <div>
                                <h4
                                    class="text-lg font-bold text-gray-800
                                       group-hover:text-indigo-600 transition">
                                    {{ $subpackage->title }}
                                </h4>

                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-5 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Amount</span>
                                <span class="font-semibold text-gray-800">
                                    ৳ {{ number_format($subpackage->amount, 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Commission</span>
                                <span class="font-semibold text-indigo-600">
                                    ৳ {{ number_format($subpackage->commission, 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Offer Price</span>
                                <span class="font-bold text-green-600">
                                    ৳ {{ number_format($subpackage->offer_price, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-5 pb-5">



                            <a href="#">
                                <button
                                    class="w-full py-2.5 rounded-xl text-sm font-semibold text-white
                                       bg-gradient-to-r from-indigo-500 to-purple-600
                                       hover:from-indigo-600 hover:to-purple-700
                                       shadow-lg hover:shadow-xl transition"
                                >
                                    Enter
                                </button>
                            </a>

                            <!-- <button
                            class="w-full py-2.5 rounded-xl text-sm font-semibold text-white
                                   bg-gradient-to-r from-indigo-500 to-purple-600
                                   hover:from-indigo-600 hover:to-purple-700
                                   shadow-lg hover:shadow-xl transition">
                            Enter
                        </button> -->
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </main>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-center",
            timeOut: 4000
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

</body>

</html>
