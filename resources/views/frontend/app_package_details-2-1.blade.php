<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Package Details</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastr CSS -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >
</head>

<body class="bg-blue-50 min-h-screen font-sans">

    <!-- ================= NAVBAR ================= -->
    <nav class="bg-indigo-600 text-white shadow-lg sticky top-0 z-10">
        <div class="max-w-md mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-lg font-bold">Package Details</h1>
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf
                <button class="bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg font-semibold transition shadow">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- ================= BACK BUTTON ================= -->
    <div class="max-w-md mx-auto px-4 mt-4">
        <a
            href="{{ route('app_dashboard') }}"
            class="inline-flex items-center gap-2 bg-white hover:bg-gray-100 px-3 py-2 rounded-xl font-semibold shadow transition"
        >
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="max-w-md mx-auto mt-6 space-y-4 pb-8">

        @foreach ($subpackages as $subpackage)
            <div
                class="bg-white shadow-xl rounded-3xl border border-gray-100 transition transform hover:-translate-y-1 hover:shadow-2xl">

                <!-- Header -->
                <div class="flex items-center gap-4 p-5 border-b">
                    @if ($subpackage->package?->image_icon)
                        <div class="w-14 h-14 bg-indigo-50 rounded-xl flex items-center justify-center shadow-inner">
                            <img
                                src="{{ asset('storage/' . $subpackage->package->image_icon) }}"
                                class="w-10 h-10 object-contain"
                                alt="Package Icon"
                            >
                        </div>
                    @endif
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">
                            {{ $subpackage->title }}
                        </h4>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Amount</span>
                        <span class="font-semibold text-gray-800">MVR {{ number_format($subpackage->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Commission</span>
                        <span class="font-semibold text-indigo-600">MVR
                            {{ number_format($subpackage->commission, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Offer Price</span>
                        <span class="font-bold text-green-600">MVR
                            {{ number_format($subpackage->offer_price, 2) }}</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="px-5 pb-5">
                    <button
                        type="button"
                        onclick="openPaymentModal('{{ $subpackage->title }}', {{ $subpackage->offer_price }}, {{ $subpackage->id }})"
                        class="w-full py-2.5 rounded-2xl text-sm font-semibold text-white
                           bg-gradient-to-r from-indigo-500 to-purple-600
                           hover:from-indigo-600 hover:to-purple-700
                           shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-arrow-right"></i> Enter
                    </button>
                </div>

            </div>
        @endforeach

    </main>

    <!-- ================= PAYMENT MODAL ================= -->
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
                id="modalPackageSubtitle"
                class="text-2xl font-bold text-gray-800 mb-2 text-center"
            ></h2>

            <!-- Form -->
            <form
                id="paymentForm"
                method="POST"
                action="{{ route('payment.store') }}"
                class="space-y-4"
            >
                @csrf
                <input
                    type="hidden"
                    name="package_id"
                    id="modalPackageId"
                >
                <input
                    type="hidden"
                    name="package_title"
                    id="modalPackageTitle"
                >

                <label class="block text-sm font-medium text-gray-600 mb-1">Mobile Number</label>
                <input
                    type="tel"
                    name="mobile"
                    placeholder="Enter your mobile number"
                    class="w-full border rounded-2xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required
                >

                <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
                <input
                    type="number"
                    name="amount"
                    id="modalAmount"
                    readonly
                    class="w-full border rounded-2xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required
                >

                <button
                    type="submit"
                    class="w-full py-2.5 rounded-2xl text-sm font-semibold text-white
                           bg-gradient-to-r from-indigo-500 to-purple-600
                           hover:from-indigo-600 hover:to-purple-700
                           shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-check-circle"></i> Submit Payment
                </button>
            </form>
        </div>
    </div>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script
        src="https://kit.fontawesome.com/a076d05399.js"
        crossorigin="anonymous"
    ></script>

    <script>
        // Toastr setup
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

        // Open modal with dynamic data
        function openPaymentModal(title, amount, packageId) {
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('modalPackageSubtitle').textContent = title;
            document.getElementById('modalPackageTitle').value = title;
            document.getElementById('modalAmount').value = amount;
            document.getElementById('modalPackageId').value = packageId;
        }

        // Close modal
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>

</body>

</html>
