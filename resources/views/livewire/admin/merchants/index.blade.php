<div>
    <div class="flex gap-3 mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by business or owner name" class="rounded border-slate-300 flex-1 text-sm">
        <select wire:model.live="kycFilter" class="rounded border-slate-300 text-sm">
            <option value="">All KYC statuses</option>
            <option value="pending">Pending</option>
            <option value="under_review">Under review</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Business</th>
                    <th class="px-4 py-2">Owner</th>
                    <th class="px-4 py-2">Region</th>
                    <th class="px-4 py-2">KYC status</th>
                    <th class="px-4 py-2">Tier</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($merchants as $merchant)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $merchant->business_name }}</td>
                        <td class="px-4 py-2">{{ $merchant->owner_name }}</td>
                        <td class="px-4 py-2">{{ $merchant->region }}</td>
                        <td class="px-4 py-2">
                            <span @class([
                                'px-2 py-0.5 rounded text-xs font-medium',
                                'bg-emerald-100 text-emerald-700' => $merchant->kyc_status === 'approved',
                                'bg-amber-100 text-amber-700' => in_array($merchant->kyc_status, ['pending', 'under_review']),
                                'bg-rose-100 text-rose-700' => $merchant->kyc_status === 'rejected',
                            ])>
                                {{ str_replace('_', ' ', $merchant->kyc_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 capitalize">{{ $merchant->subscription_tier }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('admin.merchants.show', $merchant) }}" class="text-emerald-700 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No merchants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $merchants->links() }}</div>
</div>
