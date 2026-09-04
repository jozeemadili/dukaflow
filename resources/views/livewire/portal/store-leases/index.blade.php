<div class="space-y-4">
    <div class="flex justify-end">
        <x-ui.button wire:click="$toggle('showLeaseForm')">
            {{ $showLeaseForm ? 'Cancel' : '+ New lease' }}
        </x-ui.button>
    </div>

    @if($showLeaseForm)
        <x-ui.card>
            <form wire:submit="saveLease" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.select wire:model="branch_id" label="Store / branch" id="lease_branch">
                        <option value="">Select a store…</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </x-ui.select>
                    @error('branch_id') <p class="text-ruby text-[12px] mt-1">{{ $message }}</p> @enderror
                    <x-ui.input type="number" step="0.01" wire:model="monthly_rent_amount" label="Monthly rent" id="lease_rent" />
                    <x-ui.input type="date" wire:model="lease_start_date" label="Lease start date" id="lease_start" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input type="date" wire:model="lease_end_date" label="Lease end date (optional)" id="lease_end" />
                    <x-ui.input wire:model="notes" label="Notes (optional)" id="lease_notes" />
                </div>
                <x-ui.button type="submit" target="saveLease">Create lease</x-ui.button>
            </form>
        </x-ui.card>
    @endif

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Store</th>
                    <th class="px-5 py-3 font-normal">Monthly rent</th>
                    <th class="px-5 py-3 font-normal">Months due / paid</th>
                    <th class="px-5 py-3 font-normal">Balance</th>
                    <th class="px-5 py-3 font-normal">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($leases as $lease)
                    <tr class="hover:bg-canvas-soft/60 cursor-pointer" wire:click="toggleExpand({{ $lease->id }})">
                        <td class="px-5 py-3 text-ink font-medium">{{ $lease->branch?->name ?? '—' }}</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">TZS {{ number_format($lease->monthly_rent_amount, 0) }}</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ $lease->monthsDue() }} / {{ $lease->monthsPaid() }}</td>
                        <td class="px-5 py-3 tnum {{ $lease->remainingBalance() > 0 ? 'text-ruby font-semibold' : 'text-ink-secondary' }}">TZS {{ number_format($lease->remainingBalance(), 0) }}</td>
                        <td class="px-5 py-3">
                            <x-ui.badge :tone="$lease->status === 'active' ? 'success' : ($lease->status === 'terminated' ? 'danger' : 'neutral')">
                                {{ $lease->status }}
                            </x-ui.badge>
                            @if($lease->isExpired())
                                <x-ui.badge tone="warning" class="ml-1">expired</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right text-primary">{{ $expandedLeaseId === $lease->id ? 'hide' : 'view' }}</td>
                    </tr>

                    @if($expandedLeaseId === $lease->id)
                        <tr class="bg-canvas-soft/60">
                            <td colspan="6" class="px-5 py-4" wire:click.stop>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="text-[13px] font-medium text-ink mb-2">Record a payment</h3>
                                        <form wire:submit="recordPayment({{ $lease->id }})" class="flex flex-wrap gap-3 items-end">
                                            <x-ui.input type="number" step="0.01" wire:model="paymentAmount" label="Amount" id="pay_amount_{{ $lease->id }}" />
                                            <x-ui.input type="date" wire:model="paymentDate" label="Date" id="pay_date_{{ $lease->id }}" />
                                            <x-ui.input wire:model="paymentNotes" label="Notes" id="pay_notes_{{ $lease->id }}" />
                                            <x-ui.button type="submit" size="sm" target="recordPayment({{ $lease->id }})">Add payment</x-ui.button>
                                        </form>
                                        @error('paymentAmount') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror

                                        <div class="mt-4 divide-y divide-hairline/60">
                                            @forelse($lease->payments as $payment)
                                                <div class="flex items-center justify-between py-1.5 text-[12.5px]">
                                                    <span class="text-ink-secondary">{{ $payment->payment_date->format('d M Y') }} @if($payment->notes) &middot; {{ $payment->notes }} @endif</span>
                                                    <span class="tnum text-ink font-medium">TZS {{ number_format($payment->amount, 0) }}</span>
                                                </div>
                                            @empty
                                                <p class="text-[12.5px] text-ink-mute py-1.5">No payments recorded yet.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div>
                                        <h3 class="text-[13px] font-medium text-ink mb-2">Contract</h3>
                                        @if($lease->contract())
                                            <a href="{{ $lease->contract()->getUrl() }}" target="_blank" class="inline-flex items-center gap-1.5 text-[13px] text-primary hover:text-primary-deep mb-3">
                                                <x-icon.receipt class="h-4 w-4" /> View uploaded contract
                                            </a>
                                        @else
                                            <p class="text-[12.5px] text-ink-mute mb-3">No contract uploaded yet.</p>
                                        @endif

                                        <form wire:submit="uploadContract({{ $lease->id }})" class="space-y-2">
                                            <input type="file" wire:model="contractFile" accept="image/*,.pdf" class="text-[13px] text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-pill file:border-0 file:text-[12px] file:bg-primary-subtle/40 file:text-primary-deep">
                                            @error('contractFile') <p class="text-ruby text-[12px] mt-1">{{ $message }}</p> @enderror
                                            <div wire:loading wire:target="contractFile" class="text-[12px] text-ink-mute">Uploading…</div>
                                            <div>
                                                <x-ui.button type="submit" size="sm" target="uploadContract({{ $lease->id }})">Upload from computer</x-ui.button>
                                            </div>
                                        </form>

                                        <h3 class="text-[13px] font-medium text-ink mt-5 mb-2">Status</h3>
                                        <div class="flex gap-2">
                                            @foreach(['active', 'expired', 'terminated'] as $statusOption)
                                                <button
                                                    wire:click="updateStatus({{ $lease->id }}, '{{ $statusOption }}')"
                                                    class="text-[12px] px-3 py-1 rounded-pill border {{ $lease->status === $statusOption ? 'bg-brand-dark text-white border-brand-dark' : 'border-hairline text-ink-secondary hover:border-primary/40' }}"
                                                >{{ ucfirst($statusOption) }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-ink-mute">No store leases yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</div>
