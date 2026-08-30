<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><p class="text-slate-500">Business</p><p class="font-medium">{{ $merchant->business_name }}</p></div>
        <div><p class="text-slate-500">Owner</p><p class="font-medium">{{ $merchant->owner_name }}</p></div>
        <div><p class="text-slate-500">Phone</p><p class="font-medium">{{ $merchant->phone }}</p></div>
        <div><p class="text-slate-500">Email</p><p class="font-medium">{{ $merchant->email ?: '—' }}</p></div>
        <div><p class="text-slate-500">Business type</p><p class="font-medium">{{ $merchant->business_type ?: '—' }}</p></div>
        <div><p class="text-slate-500">Region / City</p><p class="font-medium">{{ $merchant->region }} {{ $merchant->city }}</p></div>
        <div><p class="text-slate-500">TIN</p><p class="font-medium">{{ $merchant->tin_number ?: '—' }}</p></div>
        <div><p class="text-slate-500">Subscription tier</p><p class="font-medium capitalize">{{ $merchant->subscription_tier }}</p></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <p class="text-xs text-slate-500 uppercase">Total recorded sales</p>
            <p class="text-xl font-bold mt-1">TZS {{ number_format($salesTotal, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <p class="text-xs text-slate-500 uppercase">Total recorded expenses</p>
            <p class="text-xl font-bold mt-1">TZS {{ number_format($expensesTotal, 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">KYC Review</h2>
            <span @class([
                'px-2 py-0.5 rounded text-xs font-medium',
                'bg-emerald-100 text-emerald-700' => $merchant->kyc_status === 'approved',
                'bg-amber-100 text-amber-700' => in_array($merchant->kyc_status, ['pending', 'under_review']),
                'bg-rose-100 text-rose-700' => $merchant->kyc_status === 'rejected',
            ])>{{ str_replace('_', ' ', $merchant->kyc_status) }}</span>
        </div>

        <h3 class="text-sm font-medium text-slate-600 mb-2">Submitted documents</h3>
        <div class="space-y-2 mb-4">
            @forelse($documents as $doc)
                <div class="flex items-center justify-between border rounded px-3 py-2 text-sm">
                    <div>
                        <span class="font-medium capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</span>
                        <span class="text-slate-400 ml-2">{{ $doc->status }}</span>
                        @if($doc->file())
                            <a href="{{ $doc->file()->getUrl() }}" target="_blank" class="text-emerald-700 underline ml-2">view file</a>
                        @endif
                    </div>
                    @if($doc->status === 'pending')
                        <div class="flex gap-2">
                            <button wire:click="approveDocument({{ $doc->id }})" class="text-emerald-700 text-xs hover:underline">Approve</button>
                            <button wire:click="rejectDocument({{ $doc->id }})" class="text-rose-600 text-xs hover:underline">Reject</button>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400">No documents submitted yet.</p>
            @endforelse
        </div>

        <label class="block text-sm font-medium text-slate-600 mb-1">Review notes</label>
        <textarea wire:model="reviewNotes" rows="2" class="w-full rounded border-slate-300 text-sm mb-3"></textarea>

        <div class="flex gap-2">
            <button wire:click="markUnderReview" class="border rounded px-3 py-1.5 text-sm hover:bg-slate-50">Mark under review</button>
            <button wire:click="approveKyc" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">Approve merchant</button>
            <button wire:click="rejectKyc" class="bg-rose-600 text-white rounded px-3 py-1.5 text-sm hover:bg-rose-700">Reject merchant</button>
        </div>
    </div>
</div>
