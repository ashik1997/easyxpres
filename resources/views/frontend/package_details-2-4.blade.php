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


                        <div class="px-5 pb-5">
                            <button
                                onclick="openPaymentModal('{{ $subpackage->title }}', {{ $subpackage->offer_price }})"
                                class="w-full py-2.5 rounded-xl text-sm font-semibold text-white
           bg-gradient-to-r from-indigo-500 to-purple-600
           hover:from-indigo-600 hover:to-purple-700
           shadow-lg hover:shadow-xl transition"
                            >
                                Enter
                            </button>

                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </main>

    <!-- Modal -->
    <!-- Modal -->
    <div
        id="paymentModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
    >
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative">

            <!-- Close Button -->
            <button
                onclick="closePaymentModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold"
            >✕</button>

            <!-- Modal Title -->
            <h2
                id="modalPackageTitle"
                class="text-2xl font-bold text-gray-800 mb-4 text-center"
            ></h2>

            <!-- Form -->
            <form id="paymentForm">
                <!-- Mobile Number -->
                <label class="block text-sm font-medium text-gray-600 mb-1">Mobile Number</label>
                <input
                    type="tel"
                    name="mobile"
                    placeholder="Enter your mobile number"
                    class="w-full border rounded-xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >

                <!-- Amount -->
                <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
                <input
                    type="number"
                    name="amount"
                    placeholder="Enter amount"
                    class="w-full border rounded-xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-semibold text-white
                   bg-gradient-to-r from-indigo-500 to-purple-600
                   hover:from-indigo-600 hover:to-purple-700
                   shadow-lg hover:shadow-xl transition"
                >
                    Submit
                </button>
            </form>
        </div>
    </div>


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

    <script>
        function openPaymentModal(title, amount) {
            // Show modal
            document.getElementById('paymentModal').classList.remove('hidden');

            // Set dynamic package title
            document.getElementById('modalPackageTitle').textContent = title;

            // Pre-fill amount
            document.querySelector('#paymentForm input[name="amount"]').value = amount;
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Handle form submission (optional)
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const mobile = this.mobile.value;
            const amount = this.amount.value;

            if (!mobile || !amount) {
                alert('Please enter both mobile number and amount.');
                return;
            }

            console.log('Package:', document.getElementById('modalPackageTitle').textContent);
            console.log('Mobile:', mobile, 'Amount:', amount);

            closePaymentModal();
            alert('Details submitted successfully!');
        });
    </script>


</body>

</html>
