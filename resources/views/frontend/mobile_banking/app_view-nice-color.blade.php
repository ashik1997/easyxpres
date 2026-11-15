<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mobile Banking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen font-sans">

<!-- ================= APP HEADER ================= -->
<div class="bg-gradient-to-r from-orange-500 to-amber-500 text-white px-4 py-5 shadow-md">
    <div class="flex items-center justify-between max-w-md mx-auto">
        <div class="flex items-center gap-2">
            <i class="fas fa-wallet text-lg"></i>
            <h2 class="text-lg font-bold">Mobile Banking</h2>
        </div>

        <button onclick="backToDashboard()"
            class="bg-white/20 backdrop-blur px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-white/30 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </button>
    </div>
</div>


<!-- ================= CONTENT ================= -->
<div class="max-w-md mx-auto px-4 py-6">

 @php
        $mobileBankings = \App\Models\MobileBanking::where('status', 'active')->get();
        @endphp

    @forelse($mobileBankings as $banking)

    <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 flex items-center justify-between hover:shadow-md transition">

        <!-- Left Side -->
        <div class="flex items-center gap-4">

            @if($banking->image_icon)
            <img src="{{ asset('storage/'.$banking->image_icon) }}"
                 alt="{{ $banking->name }}"
                 class="w-14 h-14 object-cover rounded-xl shadow-sm">
            @else
            <div class="w-14 h-14 bg-gray-200 flex items-center justify-center rounded-xl text-gray-500 text-xs">
                Logo
            </div>
            @endif

            <div>
                <h3 class="text-sm font-semibold text-gray-800">
                    {{ $banking->name }}
                </h3>
                <p class="text-xs text-gray-500">
                    Service Rate: {{ $banking->rate }}%
                </p>
            </div>
        </div>

        <!-- Right Side -->
        <button onclick="openMobileBankingModal({{ $banking->id }}, {{ $banking->rate }})"
            class="px-4 py-2 bg-orange-500 text-white text-xs font-semibold rounded-lg hover:bg-orange-600 transition">
            Use
        </button>

    </div>

    @empty
    <div class="bg-white rounded-2xl shadow-sm p-6 text-center text-gray-500">
        No mobile banking services available.
    </div>
    @endforelse

</div>


<!-- ================= SIMPLE MODAL ================= -->
<div id="mobileBankingModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">

        <h3 class="text-lg font-bold mb-4 text-gray-800">
            Mobile Banking Service
        </h3>

        <p id="modalRateText" class="text-sm text-gray-600 mb-4"></p>

        <div class="flex justify-end gap-3">
            <button onclick="closeMobileBankingModal()"
                class="px-4 py-2 bg-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                Cancel
            </button>

            <button class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-semibold hover:bg-orange-600 transition">
                Confirm
            </button>
        </div>

    </div>
</div>


<!-- ================= SCRIPT ================= -->
<script>

function backToDashboard() {
    window.location.href = "{{ route('app_dashboard') }}";
}

function openMobileBankingModal(id, rate) {
    document.getElementById('mobileBankingModal').classList.remove('hidden');
    document.getElementById('mobileBankingModal').classList.add('flex');
    document.getElementById('modalRateText').innerText = "Service Rate: " + rate + "%";
}

function closeMobileBankingModal() {
    document.getElementById('mobileBankingModal').classList.add('hidden');
    document.getElementById('mobileBankingModal').classList.remove('flex');
}

</script>

</body>
</html>