<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-2">Business</th>
                <th class="px-4 py-2">Owner</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Documents submitted</th>
                <th class="px-4 py-2">Registered</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($queue as $merchant)
                <tr>
                    <td class="px-4 py-2 font-medium">{{ $merchant->business_name }}</td>
                    <td class="px-4 py-2">{{ $merchant->owner_name }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">{{ str_replace('_', ' ', $merchant->kyc_status) }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $merchant->kyc_documents_count }}</td>
                    <td class="px-4 py-2">{{ $merchant->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('admin.merchants.show', $merchant) }}" class="text-emerald-700 hover:underline">Review &rarr;</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Nothing waiting for review. 🎉</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
