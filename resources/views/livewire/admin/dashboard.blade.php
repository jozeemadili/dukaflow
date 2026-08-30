<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Total merchants</p>
            <p class="text-2xl font-bold mt-1">{{ $totalMerchants }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Approved merchants</p>
            <p class="text-2xl font-bold mt-1 text-emerald-700">{{ $approvedMerchants }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Pending KYC</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $pendingKyc }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Payments to verify</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $pendingPayments }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Flagged payments</p>
            <p class="text-2xl font-bold mt-1 text-rose-600">{{ $flaggedPayments }}</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3 text-sm">
        <a href="{{ route('admin.kyc.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Review KYC queue &rarr;</a>
        <a href="{{ route('admin.payments.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Verify payments &rarr;</a>
        <a href="{{ route('admin.merchants.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">All merchants &rarr;</a>
    </div>
</div>
