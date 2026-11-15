<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>easyxpres</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        /* Success Toast → Orange */
        #toast-container>.toast-success {
            background: linear-gradient(to right, #f97316, #ea580c) !important;
            /* orange gradient */
            color: #ffffff !important;
            border-left: 5px solid #c2410c !important;
            /* darker orange */
        }

        /* Error Toast → Red */
        #toast-container>.toast-error {
            background: linear-gradient(to right, #dc2626, #b91c1c) !important;
            color: #ffffff !important;
            border-left: 5px solid #7f1d1d !important;
        }

        /* Info Toast → Blue */
        #toast-container>.toast-info {
            background: linear-gradient(to right, #3b82f6, #2563eb) !important;
            color: #ffffff !important;
            border-left: 5px solid #1e40af !important;
        }

        /* Warning Toast → Amber */
        #toast-container>.toast-warning {
            background: linear-gradient(to right, #f59e0b, #d97706) !important;
            color: #ffffff !important;
            border-left: 5px solid #b45309 !important;
        }

        /* Common Toast styling */
        #toast-container .toast {
            border-radius: 12px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            font-weight: 600 !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
        }
    </style>
</head>

<body class="bg-blue-50">

    <main class="max-w-md mx-auto min-h-screen bg-white shadow-2xl flex flex-col">

        <!-- Header -->
        <header class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="bg-gray-300 rounded-full h-10 w-10 flex items-center justify-center">
                    <i class="fas fa-user text-gray-700 text-xl"></i>
                </div>

                <div class="flex flex-col">
                    <p class="text-xs text-indigo-100 font-medium tracking-wide">
                        USER ID
                    </p>

                    <div class="flex items-center gap-2 mt-1">
                        <div
                            class="bg-white/20 backdrop-blur-md border border-white/30 px-3 py-1.5 rounded-lg shadow-sm">
                            <span class="text-white font-semibold tracking-wider">
                                {{ Auth::user()->account->account_number ?? 'N/A' }}
                            </span>
                        </div>

                        <button
                            onclick="copyUserId()"
                            class="bg-white/20 hover:bg-white/30 transition px-2 py-1 rounded-md text-xs"
                        >
                            <i class="fas fa-copy text-white"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Top-right dropdown menu -->
            <div class="relative">
                <!-- Dropdown Button -->
                <button
                    id="dropdownButton"
                    onclick="toggleDropdown()"
                    class="bg-white text-orange-600 font-bold px-4 py-2 rounded-lg shadow hover:bg-orange-50 flex items-center gap-2 transition"
                >
                    Menu <i class="fas fa-caret-down"></i>
                </button>

                <!-- Dropdown Menu -->
                <div
                    id="dropdownMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-gradient-to-b from-orange-50 to-orange-100 border border-orange-300 rounded-lg shadow-lg z-50"
                >

                    <!-- My Account Button -->
                    <button
                        onclick="openPasswordModal()"
                        class="w-full text-left px-4 py-2 text-orange-800 font-semibold hover:bg-orange-200 rounded-t-lg transition"
                    >
                        <i class="fas fa-user-circle mr-2"></i> My Account
                    </button>

                    <!-- Logout Button -->
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-2 text-red-700 font-semibold hover:bg-red-200 rounded-b-lg transition"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Marquee under header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden relative">
            @php
                $appNote = DB::table('app_setups')->where('id', 1)->first();
            @endphp
            <div class="marquee whitespace-nowrap text-white font-semibold py-2 px-4 text-sm md:text-base">
                {!! $appNote->marquee !!}
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 py-6 flex-1 space-y-6">

            <!-- My Account Section -->
            <!-- My Account Section -->
            <section class="border-2 border-orange-300 rounded-3xl p-5 shadow-sm">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-orange-900 font-bold text-lg">My Name</p>
                        <p class="text-orange-400 font-medium">{{ Auth::user()->name ?? 'No Name' }}</p>
                    </div>

                    @php
                        $authID = auth()->id();
                        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
                        $totalDeposit = DB::table('transactions')
                            ->where('account_id', $accountID)
                            ->where('type', 'deposit')
                            ->where('status', 'approved')
                            ->sum('amount');
                    @endphp


                    <!-- Balance Button with Dropdown -->
                    <div class="relative">
                        <button
                            class="flex items-center gap-2 border-2 border-orange-600 rounded-full px-3 py-1 text-orange-900 font-semibold text-xs"
                        >

                            Balance : (MVR) {{ Auth::user()->account->balance ?? 0 }}
                        </button>


                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-4 gap-4 text-center">
                    <!-- Add Balance -->
                    <div
                        class="group cursor-pointer"
                        onclick="openBalanceModal()"
                    >
                        <div
                            class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                            <i class="fas fa-file-invoice-dollar text-3xl"></i>
                        </div>
                        <p class="text-[11px] font-bold text-orange-900 mt-2">Add Balance</p>
                    </div>

                    <!-- Deposit History -->
                    <div class="group cursor-pointer">
                        <a
                            href="{{ route('app.balance.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                                <i class="fas fa-history text-2xl"></i>
                            </div>
                            <p class="text-[11px] font-bold text-orange-900 mt-2">Deposit History</p>
                        </a>
                    </div>

                    <!-- View Packages -->
                    <div class="group cursor-pointer">
                        <a
                            href="{{ route('app.package.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                                <i class="fas fa-box-open text-2xl"></i>
                            </div>
                            <p class="text-[11px] font-bold text-orange-900 mt-2">View Packages</p>
                        </a>
                    </div>

                    <!-- Package History -->
                    <div class="group cursor-pointer">
                        <a
                            href="{{ route('app.recharge.packages.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <p class="text-[11px] font-bold text-orange-900 mt-2">Package History</p>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Recharge & Bill Payments Section -->
            <section class="border-2 border-orange-300 rounded-3xl p-5 shadow-sm mt-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-orange-900 font-bold text-lg">Recharge & Banking</h2>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-4 gap-4 text-center">
                    <!-- BD Recharge Now -->
                    <div
                        class="group cursor-pointer"
                        onclick="openRechargeModal()"
                    >
                        <div
                            class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                            <i class="fas fa-bolt text-3xl"></i>
                        </div>
                        <p class="text-xs font-bold text-orange-900 mt-2">BD Recharge Now</p>
                    </div>

                    <!-- BD Recharge History -->
                    <div class="group">
                        <a
                            href="{{ route('app.recharge.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                                <i class="fas fa-history text-2xl"></i>
                            </div>
                            <p class="text-xs font-bold text-orange-900 mt-2">BD Recharge History</p>
                        </a>
                    </div>

                    <!-- Male Recharge Now -->
                    <div
                        class="group cursor-pointer"
                        onclick="openMaleRechargeModal()"
                    >
                        <div
                            class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow hover:from-orange-500 hover:to-orange-700 transition">
                            <i class="fas fa-wallet text-3xl"></i>
                        </div>
                        <p class="text-xs font-bold text-orange-900 mt-2">Male Recharge Now</p>
                    </div>

                    <!-- Male Recharge History -->
                    <div class="group">
                        <a
                            href="{{ route('app.male.recharge.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 text-white rounded-full shadow hover:from-orange-600 hover:via-orange-700 hover:to-orange-800 transition">
                                <i class="fas fa-file-invoice text-2xl"></i>
                            </div>
                            <p class="text-xs font-bold text-orange-800 mt-2">Male Recharge History</p>
                        </a>
                    </div>

                    <!-- View Mobile Banking -->
                    <div class="group">
                        <a
                            href="{{ route('app.mobile.banking.view') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 text-white rounded-full shadow hover:from-orange-600 hover:via-orange-700 hover:to-orange-800 transition">
                                <i class="fas fa-mobile-screen-button text-2xl"></i>
                            </div>
                            <p class="text-xs font-bold text-orange-800 mt-2">View Mobile Banking</p>
                        </a>
                    </div>

                    <!-- Mobile Banking History -->
                    <div class="group">
                        <a
                            href="{{ route('app.mobile.banking.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 text-white rounded-full shadow hover:from-orange-600 hover:via-orange-700 hover:to-orange-800 transition">
                                <i class="fas fa-clock-rotate-left text-2xl"></i>
                            </div>
                            <p class="text-xs font-bold text-orange-800 mt-2">Mobile Banking History</p>
                        </a>
                    </div>

                    <!-- View iBanking -->
                    <div
                        class="group cursor-pointer"
                        onclick="openIBankingModal()"
                    >
                        <div
                            class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 text-white rounded-full shadow hover:from-orange-600 hover:via-orange-700 hover:to-orange-800 transition">
                            <i class="fas fa-money-bill text-3xl"></i>
                        </div>
                        <p class="text-xs font-bold text-orange-800 mt-2">View iBanking</p>
                    </div>

                    <!-- iBanking History -->
                    <div class="group">
                        <a
                            href="{{ route('app.ibanking.history') }}"
                            class="flex flex-col items-center justify-center text-center"
                        >
                            <div
                                class="mx-auto w-12 h-12 flex items-center justify-center bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 text-white rounded-full shadow hover:from-orange-600 hover:via-orange-700 hover:to-orange-800 transition">
                                <i class="fas fa-list-alt text-2xl"></i>
                            </div>
                            <p class="text-xs font-bold text-orange-800 mt-2">iBanking History</p>
                        </a>
                    </div>
                </div>
            </section>

            <div
                id="rechargeModal"
                class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
            >

                <!-- Modal Card -->
                <div
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative overflow-hidden max-h-[90vh] overflow-y-auto">

                    <!-- Gradient Header -->
                    <div
                        class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white">BD Mobile Recharge</h2>
                        <button
                            onclick="closeRechargeModal()"
                            class="text-white text-xl hover:text-orange-200 transition"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Form Body -->
                    <div class="p-6">
                        <form
                            method="POST"
                            action="{{ route('app.recharge.submit') }}"
                        >
                            @csrf

                            <!-- Mobile Number -->
                            <label class="block text-sm font-semibold text-orange-600 mb-1 text-left">
                                Mobile Number
                            </label>
                            <input
                                type="text"
                                name="mobile"
                                required
                                placeholder="01XXXXXXXXX"
                                class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 text-left
                           focus:outline-none focus:ring-2 focus:ring-orange-400
                           focus:border-orange-400"
                            >

                            <!-- Recharge Amount -->
                            <label class="block text-sm font-semibold text-orange-600 mb-1 text-left">
                                Amount
                            </label>

                            <div class="flex items-stretch border border-orange-200 rounded-lg overflow-hidden mb-5">
                                <span class="flex items-center px-3 bg-orange-50 text-orange-600 font-semibold">
                                    MVR
                                </span>
                                <input
                                    type="number"
                                    name="amount"
                                    required
                                    min="1"
                                    placeholder="Enter amount"
                                    class="flex-1 px-3 py-2 text-left focus:outline-none
                               focus:ring-2 focus:ring-orange-400"
                                >
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 mt-3">
                                <button
                                    type="button"
                                    onclick="closeRechargeModal()"
                                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold transition"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 rounded-lg text-white font-semibold
                               bg-gradient-to-r from-orange-500 to-orange-600
                               hover:from-orange-600 hover:to-orange-700
                               shadow-md transition"
                                >
                                    Confirm Recharge
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>


            <div
                id="maleRechargeModal"
                class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
            >

                <!-- Modal Card -->
                <div
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative overflow-hidden max-h-[90vh] overflow-y-auto">

                    <!-- Gradient Header -->
                    <div
                        class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white">Male Mobile Recharge</h2>
                        <button
                            onclick="closeMaleRechargeModal()"
                            class="text-white text-xl hover:text-orange-200 transition"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Form Body -->
                    <div class="p-6">
                        <form
                            method="POST"
                            action="{{ route('app.male.recharge.submit') }}"
                        >
                            @csrf

                            <!-- Mobile Number -->
                            <label class="block text-sm font-semibold text-orange-600 mb-1 text-left">
                                Mobile Number
                            </label>
                            <input
                                type="text"
                                name="mobile"
                                required
                                placeholder="+960XXXXXXXXX"
                                class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4
                           focus:outline-none focus:ring-2 focus:ring-orange-400
                           focus:border-orange-400"
                            >

                            <!-- Recharge Amount -->
                            <label class="block text-sm font-semibold text-orange-600 mb-1 text-left">
                                Amount
                            </label>

                            <div class="flex items-center border border-orange-200 rounded-lg overflow-hidden mb-5">
                                <span class="px-3 bg-orange-50 text-orange-600 font-semibold">
                                    MVR
                                </span>
                                <input
                                    type="number"
                                    name="amount"
                                    required
                                    min="1"
                                    placeholder="Enter amount"
                                    class="w-full px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-orange-400"
                                >
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onclick="closeMaleRechargeModal()"
                                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold transition"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 rounded-lg text-white font-semibold
                               bg-gradient-to-r from-orange-500 to-orange-600
                               hover:from-orange-600 hover:to-orange-700
                               shadow-md transition"
                                >
                                    Confirm Recharge
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>


            <!-- iBanking Modal -->
            <div
                id="iBankingModal"
                class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
            >

                <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl relative border border-orange-100">

                    <!-- Close -->
                    <button
                        onclick="closeIBankingModal()"
                        class="absolute top-3 right-3 text-orange-400 hover:text-orange-600 text-xl"
                    >
                        ✕
                    </button>

                    <h2 class="text-xl font-bold text-orange-600 mb-4">
                        iBanking Service
                    </h2>

                    <form
                        method="POST"
                        action="{{ route('app.ibanking.add') }}"
                    >
                        @csrf

                        @php
                            $rateValue = DB::table('ibanking_rates')->where('status', 'active')->value('rate') ?? 0;

                            $bankNames = DB::table('bank_names')->get();
                        @endphp

                        <!-- Converted Amount Show -->
                        <p
                            class="text-gray-700 mb-4 flex items-center justify-between bg-orange-50 border border-orange-200 rounded-lg px-4 py-2 shadow-sm">
                            <span class="font-medium text-orange-600">
                                BDT Converted :
                            </span>
                            <span
                                id="convertAmount"
                                class="font-bold text-orange-600 text-lg"
                            >
                                0
                            </span>
                        </p>

                        <!-- Hidden BDT Field -->
                        <input
                            type="hidden"
                            name="bdt_amount"
                            id="bdt_amount"
                        >

                        <!-- Bank Name -->
                        <label class="block text-sm font-medium mb-1 text-left text-orange-600">
                            Select Bank
                        </label>
                        <select
                            name="bank_name_id"
                            required
                            class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                            <option value="">Select Bank</option>
                            @foreach ($bankNames as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->bank_name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Account Number -->
                        <label class="block text-sm font-medium mb-1 text-left text-orange-600">
                            Account Number
                        </label>
                        <input
                            type="text"
                            name="account_no"
                            required
                            class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Enter account number"
                        >

                        <!-- Amount -->
                        <label class="block text-sm font-medium mb-1 text-left text-orange-600">
                            Amount (MVR)
                        </label>
                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            required
                            min="1"
                            step="0.01"
                            class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-6 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Enter amount"
                        >

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                onclick="closeIBankingModal()"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                            >
                                Confirm
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="p-4 flex justify-center gap-12 bg-gray-50 border-t">

        </footer>
    </main>

    <!-- Password Modal -->
    <div
        id="passwordModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
    >

        <!-- Modal Card -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden">

            <!-- Header Gradient -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white">Change Account</h2>
                <button
                    onclick="closePasswordModal()"
                    class="text-white text-xl hover:text-orange-200 transition"
                >
                    ✕
                </button>
            </div>

            <!-- Form Body -->
            <div class="p-6">
                <form
                    method="POST"
                    action="{{ route('password.change') }}"
                >
                    @csrf

                    <!-- Name -->
                    <label class="block text-sm font-semibold text-orange-600 mb-1">Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ Auth::user()->name ?? '' }}"
                        class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 
                           focus:outline-none focus:ring-2 focus:ring-orange-400 
                           focus:border-orange-400"
                    >

                    <!-- Phone -->
                    <label class="block text-sm font-semibold text-orange-600 mb-1">Phone</label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ Auth::user()->phone ?? '' }}"
                        class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 
                           focus:outline-none focus:ring-2 focus:ring-orange-400 
                           focus:border-orange-400"
                    >

                    <!-- Email -->
                    <label class="block text-sm font-semibold text-orange-600 mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ Auth::user()->email ?? '' }}"
                        class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-4 
                           focus:outline-none focus:ring-2 focus:ring-orange-400 
                           focus:border-orange-400"
                    >

                    <!-- Password -->
                    <label class="block text-sm font-semibold text-orange-600 mb-1">Change Password</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full border border-orange-200 rounded-lg px-3 py-2 mb-5 
                           focus:outline-none focus:ring-2 focus:ring-orange-400 
                           focus:border-orange-400"
                    >

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="closePasswordModal()"
                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold transition"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg text-white font-semibold 
                               bg-gradient-to-r from-orange-500 to-orange-600 
                               hover:from-orange-600 hover:to-orange-700 
                               shadow-md transition"
                        >
                            Update Account
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!-- Add Balance Modal -->
    <div
        id="balanceModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-0 relative max-h-[90vh] overflow-y-auto">

            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">Add Balance</h2>
                <button
                    onclick="closeBalanceModal()"
                    class="text-white hover:text-gray-200 text-2xl font-bold"
                >✕</button>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-6">
                @php
                    $appSetup = DB::table('app_setups')->first();
                @endphp
                {!! $appSetup->add_balance_content !!}

                <form
                    method="POST"
                    action="{{ route('app.balance.add') }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <!-- Amount Field -->
                    <label class="block text-sm font-medium text-orange-700 mb-1">Amount (MVR)</label>
                    <div
                        class="flex items-center border-2 border-orange-300 rounded-lg overflow-hidden mb-4 shadow-sm">
                        <span class="px-3 bg-orange-100 text-orange-700 font-semibold">MVR</span>
                        <input
                            type="number"
                            name="amount"
                            required
                            min="1"
                            placeholder="Enter amount"
                            class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 rounded-r-lg"
                        >
                    </div>

                    <!-- Transaction ID Field -->
                    <!-- <label class="block text-sm font-medium text-orange-700 mb-1">Transaction ID</label>
                <input type="text" name="transaction_id" required placeholder="Enter transaction ID"
                    class="w-full border-2 border-orange-300 rounded-lg px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-orange-400"> -->

                    <!-- Payment Proof Field -->
                    <label class="block text-sm font-medium text-orange-700 mb-1">Payment Proof (Screenshot)</label>
                    <input
                        type="file"
                        name="file_upload"
                        required
                        accept="image/*,.pdf"
                        class="w-full border-2 border-orange-300 rounded-lg px-3 py-2 mb-5
                           file:bg-orange-600 file:text-white file:px-4 file:py-2 file:rounded-lg file:border-0 file:cursor-pointer"
                    >

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="closeBalanceModal()"
                            class="px-4 py-2 rounded-lg bg-orange-200 text-orange-800 hover:bg-orange-300 font-semibold transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700 font-semibold transition"
                        >
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }

        .marquee {
            display: inline-block;
            padding-left: 100%;
            animation: scroll-marquee 15s linear infinite;
        }

        @keyframes scroll-marquee {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @media (max-width:768px) {
            .marquee {
                font-size: 12px;
                padding-left: 100%;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Dropdown
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const button = document.getElementById('dropdownButton'),
                menu = document.getElementById('dropdownMenu');
            if (!button.contains(e.target) && !menu.contains(e.target)) menu.classList.add('hidden');
        });

        // Password Modal
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        // Balance Dropdown
        function toggleBalance() {
            document.getElementById('balanceDropdown').classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const button = document.querySelector('[onclick="toggleBalance()"]'),
                dropdown = document.getElementById('balanceDropdown');
            if (!button.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden');
        });

        // Add Balance Modal
        function openBalanceModal() {
            document.getElementById('balanceModal').classList.remove('hidden');
        }

        function closeBalanceModal() {
            document.getElementById('balanceModal').classList.add('hidden');
        }

        // Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": 4000
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
            openBalanceModal();
        @endif
    </script>



    <!-- user ID Design JS  -->
    <script>
        function copyUserId() {
            const userId = "{{ Auth::user()->account->account_number ?? '' }}";
            navigator.clipboard.writeText(userId);

            toastr.success("User ID copied!"); // This will now show orange
        }
    </script>

    <!-- BD Recharge JS  -->
    <script>
        function openRechargeModal() {
            document.getElementById('rechargeModal').classList.remove('hidden');
        }

        function closeRechargeModal() {
            document.getElementById('rechargeModal').classList.add('hidden');
        }
    </script>


    <!-- Male Recharge JS  -->
    <script>
        function openMaleRechargeModal() {
            document.getElementById('maleRechargeModal').classList.remove('hidden');
        }

        function closeMaleRechargeModal() {
            document.getElementById('maleRechargeModal').classList.add('hidden');
        }
    </script>

    <!-- iBanking JS  -->
    <script>
        const rateValue = {{ $rateValue }};

        document.getElementById('amount').addEventListener('input', function() {

            let amount = parseFloat(this.value) || 0;

            let converted = amount * rateValue;

            document.getElementById('convertAmount').innerText = converted.toFixed(2);

            document.getElementById('bdt_amount').value = converted.toFixed(2);
        });

        function openIBankingModal() {
            document.getElementById('iBankingModal').classList.remove('hidden');
            document.getElementById('iBankingModal').classList.add('flex');
        }

        function closeIBankingModal() {
            let modal = document.getElementById('iBankingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>




</body>

</html>
