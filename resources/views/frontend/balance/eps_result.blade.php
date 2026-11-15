<x-guest-layout>
    <x-slot:metaTitle>Payment Result · EasyXpres</x-slot:metaTitle>
    @php
        $isSuccess = $resultType === 'successful';
        $isProcessing = $resultType === 'processing' || $resultType === 'pending';
        $iconClasses = $isSuccess ? 'bg-emerald-50 text-emerald-600' : ($isProcessing ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600');
    @endphp
    <div class="auth-card text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl text-2xl font-extrabold {{ $iconClasses }}">{{ $isSuccess ? '✓' : ($isProcessing ? '…' : '×') }}</div>
        <span class="ux-eyebrow mt-5 block">EPS payment</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">{{ $isSuccess ? 'Payment successful' : ($isProcessing ? 'Verification pending' : 'Payment not completed') }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $message }}</p>
        @if ($payment)
            <dl class="mt-6 space-y-3 rounded-2xl bg-slate-50 p-4 text-left text-sm"><div class="flex justify-between gap-4"><dt class="text-slate-500">Reference</dt><dd class="break-all text-right font-mono text-xs font-bold text-slate-700">{{ $payment->transaction_id }}</dd></div><div class="flex justify-between gap-4"><dt class="text-slate-500">Paid</dt><dd class="font-extrabold text-teal-700">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</dd></div>@if ($payment->status === \App\Enums\PaymentStatus::Successful)<div class="flex justify-between gap-4"><dt class="text-slate-500">Balance credited</dt><dd class="font-extrabold text-teal-700">MVR {{ number_format((float) $payment->balance_amount, 2) }}</dd></div>@endif<div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd class="font-bold text-slate-700">{{ $payment->status->label() }}</dd></div></dl>
        @endif
        <a href="{{ auth()->check() ? route('balance.payment.index') : route('login') }}" class="ux-primary-button mt-6 w-full">{{ auth()->check() ? 'View payment history' : 'Sign in' }} <i class="fas fa-arrow-right text-xs"></i></a>
    </div>
</x-guest-layout>
