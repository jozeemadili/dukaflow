<div class="bg-white rounded-lg shadow-sm border p-5">
    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-2 mb-4">
        The credit engine is feature-flagged. This screen previews the loan-facility workflow structure;
        scoring and disbursement logic ship once a partner bank/MFI agreement is confirmed.
    </p>

    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-2">Merchant</th>
                <th class="px-4 py-2">Requested</th>
                <th class="px-4 py-2">Approved</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($facilities as $facility)
                <tr>
                    <td class="px-4 py-2">{{ $facility->merchant->business_name }}</td>
                    <td class="px-4 py-2">{{ $facility->requested_amount }}</td>
                    <td class="px-4 py-2">{{ $facility->approved_amount }}</td>
                    <td class="px-4 py-2">{{ $facility->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No facilities yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
