<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mobile Banking</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.animate-slideUp { animation: slideUp 0.3s ease-out forwards; }

/* Orange Toastr */
.toast-success { background-color: #f97316 !important; }
.toast-error { background-color: #dc2626 !important; }
</style>
</head>

<body class="bg-orange-50 min-h-screen font-sans">

<!-- HEADER -->
<header class="bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg">
    <div class="max-w-md mx-auto flex justify-between items-center px-4 py-4">
        <h1 class="text-lg font-bold">Mobile Banking</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow">
                Logout
            </button>
        </form>
    </div>
</header>

<!-- Back Button -->
<div class="max-w-md mx-auto px-4 mt-4">
    <a href="{{ route('app_dashboard') }}"
       class="inline-flex items-center gap-2 bg-white hover:bg-orange-100 px-3 py-2 rounded-xl font-semibold shadow border border-orange-200 transition">
        ← Back
    </a>
</div>

<!-- MOBILE BANKING LIST -->
<div class="max-w-md mx-auto px-4 py-6">

 @php
    $mobileBankings = \App\Models\MobileBanking::where('status', 'active')->get();
 @endphp

 @forelse($mobileBankings as $banking)
    <div class="bg-white rounded-2xl shadow-xl border border-orange-100 p-4 mb-4 flex items-center justify-between hover:shadow-2xl transition">

        <div class="flex items-center gap-4">
            @if($banking->image_icon)
                <img src="{{ asset('storage/'.$banking->image_icon) }}"
                     class="w-14 h-14 object-cover rounded-xl shadow-sm border border-orange-100">
            @else
                <div class="w-14 h-14 bg-orange-100 flex items-center justify-center rounded-xl text-orange-500 text-xs">
                    Logo
                </div>
            @endif

            <div>
                <h3 class="text-sm font-semibold text-gray-800">{{ $banking->name }}</h3>
                <p class="text-xs text-orange-500">Service Rate: {{ $banking->rate }}%</p>
            </div>
        </div>

        <button type="button"
            onclick="openMobileBankingModal({{ $banking->id }}, {{ $banking->rate }})"
            class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition shadow">
            Use
        </button>
    </div>

 @empty
    <div class="bg-white rounded-2xl shadow-xl border border-orange-100 p-6 text-center text-orange-400">
        No mobile banking services available.
    </div>
 @endforelse
</div>

<!-- MODAL -->
<div id="mobileBankingModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-end justify-center z-50 p-4">

    <div class="bg-white w-full max-w-md rounded-t-3xl shadow-2xl overflow-hidden animate-slideUp">

        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 text-white flex justify-between items-center">
            <h2 class="text-lg font-bold">Mobile Banking Request</h2>
            <button type="button" onclick="closeMobileBankingModal()" class="hover:text-orange-200 text-lg">✕</button>
        </div>

        <div class="p-6">

            <form id="mobileBankingForm" method="POST" action="{{ route('mobile.banking.store') }}">
                @csrf

                <p class="text-gray-700 mb-4 flex justify-between bg-orange-50 border border-orange-200 rounded-lg px-4 py-2 shadow-sm">
                    <span class="font-medium">BDT Converted:</span>
                    <span id="totalAmount" class="font-bold text-orange-600 text-lg">0</span>
                </p>

                <input type="hidden" name="mobile_banking_id" id="mobile_banking_id">
                <input type="hidden" name="rate_calculation" id="rateCalculationInput">

                <label class="block text-sm font-medium mb-1">Mobile Number</label>
                <input type="text" name="number" required
                    class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <label class="block text-sm font-medium mb-1">Amount (MVR)</label>
                <input type="number" name="amount" id="amountInput" required min="1"
                    class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <label class="block text-sm font-medium mb-2">Select Account Type</label>
                <div class="flex gap-6 mb-6">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="money_status" value="personal" checked
                               class="text-orange-600 focus:ring-orange-500">
                        <span>Personal</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="money_status" value="agent"
                               class="text-orange-600 focus:ring-orange-500">
                        <span>Agent</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button"
                        onclick="closeMobileBankingModal()"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>

                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition shadow">
                        Confirm
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
let currentRate = 0;

function openMobileBankingModal(id, rate) {
    currentRate = parseFloat(rate);
    const modal = document.getElementById('mobileBankingModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('mobile_banking_id').value = id;
    document.getElementById('totalAmount').innerText = '0';
}

function closeMobileBankingModal() {
    const modal = document.getElementById('mobileBankingModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('mobileBankingForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerText = 'Processing...';
    btn.disabled = true;
});

document.getElementById('amountInput').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    const total = amount * currentRate;
    document.getElementById('totalAmount').innerText = total.toFixed(2);
    document.getElementById('rateCalculationInput').value = total.toFixed(2);
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
@if(session('success'))
    toastr.success("{{ session('success') }}");
@endif
@if(session('error'))
    toastr.error("{{ session('error') }}");
@endif
</script>

</body>
</html>