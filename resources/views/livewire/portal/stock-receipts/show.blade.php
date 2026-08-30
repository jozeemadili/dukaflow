<div class="space-y-4">
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <h2 class="text-[15px] text-ink-secondary">Receipt #{{ $receipt->id }}</h2>
                <x-ui.badge :tone="$receipt->status === 'approved' ? 'success' : ($receipt->status === 'rejected' ? 'danger' : 'warning')">{{ $receipt->status }}</x-ui.badge>
            </div>
            @can('approve-stock-receipts')
                @if($receipt->isPending())
                    <div class="flex gap-2">
                        <x-ui.button variant="danger" size="sm" wire:click="reject" target="reject" wire:confirm="Reject this stock receipt? No inventory changes will be made.">Reject</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="approve" target="approve" wire:confirm="Approve and post this receipt? Inventory quantities will increase immediately.">Approve &amp; post stock</x-ui.button>
                    </div>
                @endif
            @endcan
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 text-[13px]">
            <div><p class="text-ink-mute mb-1">Supplier</p><p class="text-ink font-medium">{{ $receipt->supplier?->name ?: '—' }}</p></div>
            <div><p class="text-ink-mute mb-1">Reference</p><p class="text-ink font-medium">{{ $receipt->reference_no ?: '—' }}</p></div>
            <div><p class="text-ink-mute mb-1">Date</p><p class="text-ink font-medium">{{ $receipt->receipt_date->format('d M Y') }}</p></div>
            <div><p class="text-ink-mute mb-1">Total</p><p class="text-ink font-medium tnum">TZS {{ number_format($receipt->total_amount, 0) }}</p></div>
        </div>
    </x-ui.card>

    @if($receipt->isPending())
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[15px] text-ink-secondary">Add product</h2>
                <button wire:click="$toggle('addingNewProduct')" class="text-[13px] text-primary-deep hover:underline">
                    {{ $addingNewProduct ? 'Choose existing product instead' : '+ This is a new product' }}
                </button>
            </div>

            @if($addingNewProduct)
                <form wire:submit="addNewProduct" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <x-ui.input wire:model="new_name" label="Product name" id="new_name" />
                    <x-ui.input wire:model="new_sku" label="SKU" id="new_sku" />
                    <x-ui.input wire:model="new_unit" label="Unit" placeholder="pcs, kg..." id="new_unit" />
                    <x-ui.input type="number" step="0.01" wire:model="quantity" label="Qty received" id="new_qty" />
                    <x-ui.input type="number" step="0.01" wire:model="unit_cost" label="Unit cost" id="new_cost" />
                    <x-ui.input type="number" step="0.01" wire:model="new_unit_price" label="Selling price" id="new_price" />
                    <div class="md:col-span-6">
                        <x-ui.button type="submit" target="addNewProduct">Add product to receipt</x-ui.button>
                    </div>
                </form>
                @error('new_name') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
            @else
                <form wire:submit="addExistingItem" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <x-ui.select wire:model="inventory_item_id" label="Product" id="existing_item">
                        <option value="">Select product</option>
                        @foreach($inventoryItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} @if($item->sku)({{ $item->sku }})@endif</option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.input type="number" step="0.01" wire:model="quantity" label="Qty received" id="existing_qty" />
                    <x-ui.input type="number" step="0.01" wire:model="unit_cost" label="Unit cost" id="existing_cost" />
                    <x-ui.button type="submit" target="addExistingItem">Add to receipt</x-ui.button>
                </form>
                @error('inventory_item_id') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
            @endif
        </x-ui.card>
    @endif

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Product</th>
                    <th class="px-5 py-3 font-normal">Qty</th>
                    <th class="px-5 py-3 font-normal">Unit cost</th>
                    <th class="px-5 py-3 font-normal">Subtotal</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($lines as $line)
                    <tr>
                        <td class="px-5 py-3 text-ink font-medium">{{ $line->inventoryItem->name }}</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ rtrim(rtrim(number_format($line->quantity, 2, '.', ''), '0'), '.') }}</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ number_format($line->unit_cost, 0) }}</td>
                        <td class="px-5 py-3 tnum text-ink">{{ number_format($line->subtotal, 0) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($receipt->isPending())
                                <button wire:click="removeItem({{ $line->id }})" class="text-ruby text-[12px] hover:underline">Remove</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-ink-mute">No products added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</div>
