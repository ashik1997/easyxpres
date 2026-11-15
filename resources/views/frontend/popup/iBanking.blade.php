<!-- iBanking Modal -->
<div id="iBankingModal"
    class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl relative">

        <!-- Close -->
        <button onclick="closeIBankingModal()"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">
            ✕
        </button>

        <h2 class="text-xl font-bold text-gray-800 mb-4">
            iBanking Service
        </h2>

        <form method="POST" action="{{ route('ibanking.add') }}" enctype="multipart/form-data">
            @csrf

            @php
            $rateValue = DB::table('ibanking_rates')
                            ->where('status', 'active')
                            ->value('rate') ?? 0;

            $bankNames = DB::table('bank_names')->get();
            @endphp

            <!-- Converted Amount Show -->
            <p class="text-gray-700 mb-4 flex items-center justify-between bg-orange-50 border border-orange-200 rounded-lg px-4 py-2 shadow-sm">
                <!-- <span class="font-medium">BDT Converted (Rate: {{ $rateValue }}):</span> -->
                <span class="font-medium">BDT Converted :</span>
                <span id="convertAmount" class="font-bold text-orange-600 text-lg">0</span>
            </p>

            <!-- Hidden BDT Field -->
            <input type="hidden" name="bdt_amount" id="bdt_amount">

            <!-- Bank Name -->
            <label class="block text-sm font-medium mb-1">
                Select Bank
            </label>
            <select name="bank_name_id"
                required
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500">
                <option value="">Select Bank</option>
                @foreach($bankNames as $bank)
                    <option value="{{ $bank->id }}">
                        {{ $bank->bank_name }}
                    </option>
                @endforeach
            </select>

            <!-- Account Holder Name -->
            <label class="block text-sm font-medium mb-1">
                Account Holder Name
            </label>
            <input type="text"
                name="account_holder_name"
                required
                maxlength="120"
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter account holder name">

            <!-- Account Number -->
            <label class="block text-sm font-medium mb-1">
                Account Number
            </label>
            <input type="text"
                name="account_no"
                required
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter account number">

            <!-- Branch -->
            <label class="block text-sm font-medium mb-1">
                Bank Branch
            </label>
            <input type="text"
                name="branch"
                required
                maxlength="120"
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter branch name">

            <!-- Supporting Document -->
            <label class="block text-sm font-medium mb-1">
                Supporting Document <span class="text-gray-400">(optional)</span>
            </label>
            <input type="file"
                name="document_attachment"
                accept=".jpg,.jpeg,.png,.pdf"
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500">

            <!-- Amount -->
            <label class="block text-sm font-medium mb-1">
                Amount (MVR)
            </label>
            <input type="number"
                name="amount"
                id="amount"
                required
                min="1"
                step="0.01"
                class="w-full border rounded-lg px-3 py-2 mb-6 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter amount">

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="closeIBankingModal()"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>

                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Confirm
                </button>
            </div>

        </form>

    </div>
</div>


<script>
    const rateValue = {{ $rateValue }};

    document.getElementById('amount').addEventListener('input', function () {

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
