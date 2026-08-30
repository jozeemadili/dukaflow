<div class="space-y-4">
    <x-ui.card>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <x-ui.input type="number" step="0.01" wire:model="amount" label="Amount (TZS)" id="sale_amount" />
            <x-ui.input type="number" wire:model="items_count" label="Items sold" id="sale_items" />
            <x-ui.input type="date" wire:model="sale_date" label="Date" id="sale_date" />
            <x-ui.input wire:model="description" label="Note" id="sale_note" />
            <x-ui.button type="submit" target="save">Record sale</x-ui.button>
        </form>
        @error('amount') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
        <p class="text-[12px] text-ink-mute mt-3">Selling from your product catalog? Use <a href="{{ route('portal.pos.index') }}" class="text-primary hover:text-primary-deep">Point of Sale</a> instead — it tracks items and updates stock automatically.</p>
    </x-ui.card>

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Date</th>
                    <th class="px-5 py-3 font-normal">Amount</th>
                    <th class="px-5 py-3 font-normal">Items</th>
                    <th class="px-5 py-3 font-normal">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($sales as $sale)
                    <tr class="hover:bg-canvas-soft/60 {{ $sale->items_count > 0 ? 'cursor-pointer' : '' }}" @if($sale->items_count > 0) wire:click="toggleExpand({{ $sale->id }})" @endif>
                        <td class="px-5 py-3 text-ink-secondary">{{ $sale->sale_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 tnum text-ink font-medium">TZS {{ number_format($sale->amount, 0) }}</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">
                            {{ $sale->items_count ?? '—' }}
                            @if($sale->items_count > 0)
                                <x-ui.badge tone="primary" class="ml-1">POS &middot; {{ $expandedSaleId === $sale->id ? 'hide' : 'view' }}</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-mute">{{ $sale->description }}</td>
                    </tr>
                    @if($expandedSaleId === $sale->id)
                        <tr class="bg-canvas-soft/60">
                            <td colspan="4" class="px-5 py-3">
                                <table class="w-full text-[12px]">
                                    <thead class="text-ink-mute text-left">
                                        <tr>
                                            <th class="font-normal pb-1">Product</th>
                                            <th class="font-normal pb-1">Qty</th>
                                            <th class="font-normal pb-1">Unit price</th>
                                            <th class="font-normal pb-1 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-hairline/60">
                                        @foreach($sale->items as $item)
                                            <tr>
                                                <td class="py-1.5 text-ink-secondary">{{ $item->item_name }}</td>
                                                <td class="py-1.5 tnum text-ink-secondary">{{ rtrim(rtrim($item->quantity, '0'), '.') }}</td>
                                                <td class="py-1.5 tnum text-ink-secondary">{{ number_format($item->unit_price, 0) }}</td>
                                                <td class="py-1.5 tnum text-ink text-right">{{ number_format($item->subtotal, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-ink-mute">No sales recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div>{{ $sales->links() }}</div>
</div>
