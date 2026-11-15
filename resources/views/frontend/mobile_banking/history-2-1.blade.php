<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

<!-- ================= NAVBAR ================= -->
<nav class="bg-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">
            Welcome, {{ Auth::check() ? Auth::user()->name : 'Guest' }}
        </h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-xl font-semibold transition shadow">
                Logout
            </button>
        </form>
    </div>
</nav>

<!-- ================= BACK BUTTON ================= -->
<div class="max-w-7xl mx-auto px-6 pt-6">
    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300
              px-4 py-2 rounded-xl font-semibold transition shadow">
        ← Back to Dashboard
    </a>
</div>
<!-- ================= MAIN CONTENT ================= -->
<main class="max-w-7xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Mobile Banking History</h2>

    <div class="bg-white shadow-md rounded-2xl border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image Icon</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
               
@foreach($packages as $key => $package)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $key + 1 }}</td>
                    @php
                                        $packageId = $package->package_id;
                                        $packageDetail = DB::table('package_details')->where('id', $packageId)->first();
                                        $pack = DB::table('packages')->where('id', $packageDetail->package_id)->first();
                                        
                                        @endphp
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <img src="{{ asset('storage/' . $pack->image_icon) }}"
                                                alt="{{ $pack->name }}"
                                                width="50"
                                                height="50">
                                        </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $package->number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $package->amount }}</td>
                     <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($package->status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($package->status === 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $package->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
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
