<div class="space-y-4">
    <x-ui.card>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 text-[13px]">
            <div><p class="text-ink-mute mb-1">Business</p><p class="text-ink font-medium">{{ $merchant->business_name }}</p></div>
            <div><p class="text-ink-mute mb-1">Owner</p><p class="text-ink font-medium">{{ $merchant->owner_name }}</p></div>
            <div><p class="text-ink-mute mb-1">Phone</p><p class="text-ink font-medium tnum">{{ $merchant->phone }}</p></div>
            <div><p class="text-ink-mute mb-1">Email</p><p class="text-ink font-medium">{{ $merchant->email ?: '—' }}</p></div>
            <div><p class="text-ink-mute mb-1">Business type</p><p class="text-ink font-medium">{{ $merchant->business_type ?: '—' }}</p></div>
            <div><p class="text-ink-mute mb-1">Region / City</p><p class="text-ink font-medium">{{ $merchant->region }} {{ $merchant->city }}</p></div>
            <div><p class="text-ink-mute mb-1">TIN</p><p class="text-ink font-medium tnum">{{ $merchant->tin_number ?: '—' }}</p></div>
            <div><p class="text-ink-mute mb-1">Subscription tier</p><p class="text-ink font-medium capitalize">{{ $merchant->subscription_tier }}</p></div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-ui.card padding="p-5">
            <x-ui.stat label="Total recorded sales" value="TZS {{ number_format($salesTotal, 0) }}" tone="primary" />
        </x-ui.card>
        <x-ui.card padding="p-5">
            <x-ui.stat label="Total recorded expenses" value="TZS {{ number_format($expensesTotal, 0) }}" tone="ruby" />
        </x-ui.card>
    </div>

    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-[15px] text-ink-secondary">KYC Review</h2>
            <x-ui.badge :tone="$merchant->kyc_status === 'approved' ? 'success' : ($merchant->kyc_status === 'rejected' ? 'danger' : 'warning')">
                {{ str_replace('_', ' ', $merchant->kyc_status) }}
            </x-ui.badge>
        </div>

        <h3 class="text-[13px] text-ink-mute mb-2">Submitted documents</h3>
        <div class="space-y-2 mb-5">
            @forelse($documents as $doc)
                <div class="flex items-center justify-between border border-hairline rounded-md px-3.5 py-2.5 text-[13px]">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-ink capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</span>
                        <x-ui.badge :tone="$doc->status === 'approved' ? 'success' : ($doc->status === 'rejected' ? 'danger' : 'neutral')">{{ $doc->status }}</x-ui.badge>
                        @if($doc->file())
                            <a href="{{ $doc->file()->getUrl() }}" target="_blank" class="text-primary hover:text-primary-deep">view file</a>
                        @endif
                    </div>
                    @if($doc->status === 'pending')
                        <div class="flex gap-2">
                            <x-ui.button size="sm" variant="secondary" wire:click="approveDocument({{ $doc->id }})" target="approveDocument({{ $doc->id }})">Approve</x-ui.button>
                            <x-ui.button size="sm" variant="danger" wire:click="rejectDocument({{ $doc->id }})" target="rejectDocument({{ $doc->id }})">Reject</x-ui.button>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-[13px] text-ink-mute">No documents submitted yet.</p>
            @endforelse
        </div>

        <x-ui.textarea wire:model="reviewNotes" label="Review notes" rows="2" class="mb-4"></x-ui.textarea>

        <div class="flex gap-2">
            <x-ui.button variant="secondary" wire:click="markUnderReview" target="markUnderReview">Mark under review</x-ui.button>
            <x-ui.button variant="primary" wire:click="approveKyc" target="approveKyc">Approve merchant</x-ui.button>
            <x-ui.button variant="danger" wire:click="rejectKyc" target="rejectKyc">Reject merchant</x-ui.button>
        </div>
    </x-ui.card>
</div>
