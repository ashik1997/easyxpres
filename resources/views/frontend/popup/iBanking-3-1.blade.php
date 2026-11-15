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

        <form method="POST" action="{{ route('ibanking.add') }}">
            @csrf

             @php
              $bankNames = DB::table('bank_names')->get();
                @endphp

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

            <!-- Account Number -->
            <label class="block text-sm font-medium mb-1">
                Account Number
            </label>
            <input type="text"
                name="account_no"
                required
                class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter account number">

                

            

            <!-- Amount -->
            <label class="block text-sm font-medium mb-1">
                Amount (MVR)
            </label>
            <input type="number"
                name="amount"
                required
                min="1"
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






