<div class="space-y-6">
    <form wire:submit="save" class="bg-white border rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Amount (TZS)</label>
            <input type="number" step="0.01" wire:model="amount" class="w-full rounded border-slate-300 text-sm">
            @error('amount') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Payer name</label>
            <input type="text" wire:model="payer_name" class="w-full rounded border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
            <input type="date" wire:model="payment_date" class="w-full rounded border-slate-300 text-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1">Proof of payment (photo/receipt)</label>
            <input type="file" wire:model="proof" class="w-full text-sm">
            @error('proof') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            <div wire:loading wire:target="proof" class="text-xs text-slate-400 mt-1">Uploading…</div>
        </div>
        <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Record payment</button>
    </form>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Payer</th>
                    <th class="px-4 py-2">Proof</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-2">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($payment->amount, 0) }}</td>
                        <td class="px-4 py-2">{{ $payment->payer_name ?: '—' }}</td>
                        <td class="px-4 py-2">
                            @if($payment->proofOfPayment())
                                <a href="{{ $payment->proofOfPayment()->getUrl() }}" target="_blank" class="text-emerald-700 underline">view</a>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span @class([
                                'px-2 py-0.5 rounded text-xs font-medium',
                                'bg-emerald-100 text-emerald-700' => $payment->status === 'verified',
                                'bg-amber-100 text-amber-700' => $payment->status === 'recorded',
                                'bg-rose-100 text-rose-700' => $payment->status === 'flagged',
                            ])>{{ $payment->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>
</div>
