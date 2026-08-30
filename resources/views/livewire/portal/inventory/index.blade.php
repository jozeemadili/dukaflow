<div class="space-y-4">
    <div class="flex items-center gap-3">
        <x-ui.button wire:click="$toggle('showItemForm')">
            {{ $showItemForm ? 'Cancel' : '+ Add inventory item' }}
        </x-ui.button>
        <p class="text-[12px] text-ink-mute">Receiving stock from a supplier? Use <a href="{{ route('portal.stock-receipts.index') }}" class="text-primary hover:text-primary-deep">Stock Receipts</a> so it goes through approval before quantities update.</p>
    </div>

    @if($showItemForm)
        <x-ui.card>
            <form wire:submit="addItem" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.input wire:model="name" label="Item name" id="item_name" />
                    <x-ui.input wire:model="sku" label="SKU" id="item_sku" />
                    <x-ui.input wire:model="barcode" label="Barcode" id="item_barcode" />
                </div>
                @error('name') <p class="text-ruby text-[12px]">{{ $message }}</p> @enderror

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <x-ui.input wire:model="unit" label="Unit" placeholder="pcs, kg..." id="item_unit" />
                    <x-ui.input type="number" step="0.01" wire:model="reorder_level" label="Reorder level" id="item_reorder" />
                    <x-ui.input type="number" step="0.01" wire:model="unit_cost" label="Unit cost" id="item_cost" />
                    <x-ui.input type="number" step="0.01" wire:model="unit_price" label="Selling price" id="item_price" />
                </div>

                <div>
                    <label class="block text-[13px] text-ink-mute mb-1.5">Product image (optional)</label>
                    <input type="file" wire:model="image" accept="image/*" class="text-[13px] text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-pill file:border-0 file:text-[12px] file:bg-primary-subtle/40 file:text-primary-deep">
                    @error('image') <p class="text-ruby text-[12px] mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="image" class="text-[12px] text-ink-mute mt-1">Uploading…</div>
                </div>

                <x-ui.button type="submit" target="addItem">Save item</x-ui.button>
            </form>
        </x-ui.card>
    @endif

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Item</th>
                    <th class="px-5 py-3 font-normal">SKU / Barcode</th>
                    <th class="px-5 py-3 font-normal">Selling price</th>
                    <th class="px-5 py-3 font-normal">On hand</th>
                    <th class="px-5 py-3 font-normal">Reorder level</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($items as $item)
                    <tr class="hover:bg-canvas-soft/60">
                        <td class="px-5 py-3 text-ink font-medium">
                            <div class="flex items-center gap-2.5">
                                @if($item->image())
                                    <img src="{{ $item->image()->getUrl() }}" alt="" class="h-8 w-8 rounded object-cover border border-hairline">
                                @else
                                    <span class="h-8 w-8 rounded bg-canvas-soft border border-hairline flex items-center justify-center text-ink-mute text-[10px]">—</span>
                                @endif
                                {{ $item->name }}
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-secondary tnum">{{ $item->sku }} @if($item->barcode)<br><span class="text-[11px] text-ink-mute">{{ $item->barcode }}</span>@endif</td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ $item->unit_price ? number_format($item->unit_price, 0) : '—' }}</td>
                        <td class="px-5 py-3 tnum {{ $item->isLowStock() ? 'text-ruby font-semibold' : 'text-ink-secondary' }}">
                            {{ rtrim(rtrim($item->quantity_on_hand, '0'), '.') }} {{ $item->unit }}
                            @if($item->isLowStock())
                                <x-ui.badge tone="danger" class="ml-1">low stock</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ rtrim(rtrim($item->reorder_level, '0'), '.') }}</td>
                        <td class="px-5 py-3 text-right">
                            <x-ui.button size="sm" variant="secondary" wire:click="startMovement({{ $item->id }})" target="startMovement({{ $item->id }})">Adjust</x-ui.button>
                        </td>
                    </tr>

                    @if($movingItemId === $item->id)
                        <tr class="bg-canvas-soft/60">
                            <td colspan="6" class="px-5 py-4">
                                <form wire:submit="saveMovement" class="flex flex-wrap gap-4 items-end">
                                    <x-ui.select wire:model="movementType" label="Type" id="movement_type">
                                        <option value="in">Stock in</option>
                                        <option value="out">Stock out</option>
                                        <option value="adjustment">Adjustment</option>
                                    </x-ui.select>
                                    <x-ui.input type="number" step="0.01" wire:model="movementQuantity" label="Quantity" id="movement_qty" />
                                    <x-ui.button type="submit" variant="primary" size="sm" target="saveMovement">Save</x-ui.button>
                                    <x-ui.button type="button" variant="ghost" size="sm" wire:click="$set('movingItemId', null)">Cancel</x-ui.button>
                                </form>
                                @error('movementQuantity') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-ink-mute">No inventory items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</div>
