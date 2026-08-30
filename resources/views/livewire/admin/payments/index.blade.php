<div>
    <div class="mb-4">
        <select wire:model.live="statusFilter" class="rounded border-slate-300 text-sm">
            <option value="">All statuses</option>
            <option value="recorded">Awaiting verification</option>
            <option value="verified">Verified</option>
            <option value="flagged">Flagged</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Merchant</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Payer</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Proof</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $payment->merchant->business_name }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($payment->amount, 0) }}</td>
                        <td class="px-4 py-2">{{ $payment->payer_name ?: '—' }}</td>
                        <td class="px-4 py-2">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-2">
                            @if($payment->proofOfPayment())
                                <a href="{{ $payment->proofOfPayment()->getUrl() }}" target="_blank" class="text-emerald-700 underline">view</a>
                            @else
                                <span class="text-rose-500 text-xs">missing</span>
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
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            @if($payment->status !== 'verified')
                                <button wire:click="verify({{ $payment->id }})" class="text-emerald-700 text-xs hover:underline mr-2">Verify</button>
                            @endif
                            @if($payment->status !== 'flagged')
                                <button wire:click="flag({{ $payment->id }})" class="text-rose-600 text-xs hover:underline">Flag</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No payments in this view.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
</div>
