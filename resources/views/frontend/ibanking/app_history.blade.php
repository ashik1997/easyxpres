<x-frontend-layout>
    <x-slot:metaTitle>iBanking History · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Bank transfer</p><h1 class="truncate text-lg font-extrabold text-white">iBanking History</h1></x-slot:header>

    <div class="mb-6">
        <span class="ux-eyebrow">Transfer activity</span>
        <h2 class="ux-title mt-1">Your bank transfers</h2>
        <p class="ux-muted mt-2 text-sm">Review account, branch, attachments and current status.</p>
    </div>

    <div class="space-y-4">
        @forelse ($packages as $package)
            <article class="ux-card p-4 sm:p-5">
                <div class="flex items-start gap-4">
                    <span class="ux-icon-tile ux-icon-tile--violet"><i class="fas fa-building-columns"></i></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="text-xs text-slate-400">{{ $package->created_at->format('d M Y, h:i A') }}</p>
                                <h3 class="mt-1 font-extrabold text-slate-900">{{ $package->bank->bank_name ?? 'Bank transfer' }}</h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $package->account_holder_name ?: (auth()->user()->name ?? 'Account holder') }}
                                    · {{ $package->branch ? 'Branch: ' . $package->branch : 'Branch not provided' }}
                                </p>
                            </div>
                            <x-user-status :status="$package->status" />
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-3 text-sm">
                            <div class="col-span-2">
                                <dt class="text-xs text-slate-500">Account number</dt>
                                <dd class="mt-1 break-all font-bold text-slate-800">{{ $package->account_no }}</dd>
                            </div>
                            <div><dt class="text-xs text-slate-500">MVR</dt><dd class="mt-1 font-extrabold text-teal-700">{{ number_format($package->amount, 2) }}</dd></div>
                            <div><dt class="text-xs text-slate-500">BDT</dt><dd class="mt-1 font-extrabold text-emerald-700">{{ number_format($package->bdt_amount ?? 0, 2) }}</dd></div>
                        </dl>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if ($package->document_attachment)
                                <a href="{{ route('app.ibanking.attachment', [$package->id, 'document']) }}" target="_blank" rel="noopener noreferrer" class="ux-secondary-button">
                                    <i class="fas fa-file-lines"></i> View document
                                </a>
                                <a href="{{ route('app.ibanking.attachment.download', [$package->id, 'document']) }}" class="ux-secondary-button">
                                    <i class="fas fa-download"></i> Download document
                                </a>
                            @endif
                            @if ($package->upload_slip)
                                <a href="{{ route('app.ibanking.attachment', [$package->id, 'slip']) }}" target="_blank" rel="noopener noreferrer" class="ux-secondary-button">
                                    <i class="fas fa-file-arrow-down"></i> View slip
                                </a>
                                <a href="{{ route('app.ibanking.attachment.download', [$package->id, 'slip']) }}" class="ux-secondary-button">
                                    <i class="fas fa-download"></i> Download slip
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="ux-card p-10 text-center"><i class="fas fa-building-columns text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No bank transfers</h3></div>
        @endforelse
    </div>
</x-frontend-layout>
