<x-ui.card>
    <p class="text-[13px] text-lemon bg-canvas-cream border border-lemon/20 rounded-md px-3.5 py-2.5 mb-4">
        The credit engine is feature-flagged. This screen previews the loan-facility workflow structure;
        scoring and disbursement logic ship once a partner bank/MFI agreement is confirmed.
    </p>

    <table class="w-full text-[13px]">
        <thead class="text-left text-[11px] uppercase tracking-wide text-ink-mute border-b border-hairline">
            <tr>
                <th class="pb-2 font-normal">Merchant</th>
                <th class="pb-2 font-normal">Requested</th>
                <th class="pb-2 font-normal">Approved</th>
                <th class="pb-2 font-normal">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-hairline">
            @forelse($facilities as $facility)
                <tr>
                    <td class="py-2 text-ink">{{ $facility->merchant->business_name }}</td>
                    <td class="py-2 tnum text-ink-secondary">{{ $facility->requested_amount }}</td>
                    <td class="py-2 tnum text-ink-secondary">{{ $facility->approved_amount }}</td>
                    <td class="py-2 text-ink-secondary">{{ $facility->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-6 text-center text-ink-mute">No facilities yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-ui.card>
