<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Deposit History</title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="bg-blue-50 min-h-screen font-sans">

<!-- ================= NAVBAR ================= -->
<header class="bg-blue-600 text-white shadow-md">
    <div class="max-w-md mx-auto flex justify-between items-center px-4 py-4">
        <h1 class="text-lg font-bold">Deposit History</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow">
                Logout
            </button>
        </form>
    </div>
</header>

<!-- ================= BACK BUTTON ================= -->
<div class="max-w-md mx-auto px-4 mt-4">
    <a href="{{ route('app_dashboard') }}"
       class="inline-flex items-center gap-2 bg-white hover:bg-gray-100 px-3 py-2 rounded-xl font-semibold shadow transition">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- ================= MAIN CONTENT ================= -->
<main class="max-w-md mx-auto mt-6 space-y-4">

    @foreach(Auth::user()->account->transactions as $key => $transaction)
    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-4 flex flex-col space-y-2">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500 font-medium">SL: {{ $key + 1 }}</span>
            <span class="text-sm text-gray-400">{{ $transaction->created_at->format('d M Y') }}</span>
        </div>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-900 font-semibold text-sm">Transaction ID:</p>
                <p class="text-gray-700 text-sm">{{ $transaction->transaction_id }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-900 font-semibold text-sm">Amount</p>
                <p class="text-green-600 font-bold text-sm">MVR {{ $transaction->amount }}</p>
            </div>
        </div>
        <div class="flex justify-start items-center gap-2 mt-2">
            @if($transaction->status === 'pending')
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
            @elseif($transaction->status === 'approved')
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Approved</span>
            @else
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rejected</span>
            @endif
        </div>
    </div>
    @endforeach

    @if(Auth::user()->account->transactions->isEmpty())
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6 text-center text-gray-500">
            No deposit history found.
        </div>
    @endif

</main>

<!-- ================= TOASTR JS ================= -->
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