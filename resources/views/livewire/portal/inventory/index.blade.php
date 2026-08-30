<div class="space-y-4">
    <div>
        <x-ui.button wire:click="$toggle('showItemForm')">
            {{ $showItemForm ? 'Cancel' : '+ Add inventory item' }}
        </x-ui.button>
    </div>

    @if($showItemForm)
        <x-ui.card>
            <form wire:submit="addItem" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div class="md:col-span-2">
                    <x-ui.input wire:model="name" label="Item name" id="item_name" />
                </div>
                <x-ui.input wire:model="sku" label="SKU" id="item_sku" />
                <x-ui.input wire:model="unit" label="Unit" placeholder="pcs, kg..." id="item_unit" />
                <x-ui.input type="number" step="0.01" wire:model="reorder_level" label="Reorder level" id="item_reorder" />
                <x-ui.button type="submit" target="addItem">Save item</x-ui.button>
            </form>
            @error('name') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
        </x-ui.card>
    @endif

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Item</th>
                    <th class="px-5 py-3 font-normal">SKU</th>
                    <th class="px-5 py-3 font-normal">On hand</th>
                    <th class="px-5 py-3 font-normal">Reorder level</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($items as $item)
                    <tr class="hover:bg-canvas-soft/60">
                        <td class="px-5 py-3 text-ink font-medium">{{ $item->name }}</td>
                        <td class="px-5 py-3 text-ink-secondary">{{ $item->sku }}</td>
                        <td class="px-5 py-3 tnum {{ $item->isLowStock() ? 'text-ruby font-semibold' : 'text-ink-secondary' }}">
                            {{ rtrim(rtrim($item->quantity_on_hand, '0'), '.') }} {{ $item->unit }}
                            @if($item->isLowStock())
                                <x-ui.badge tone="danger" class="ml-1">low stock</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 tnum text-ink-secondary">{{ rtrim(rtrim($item->reorder_level, '0'), '.') }}</td>
                        <td class="px-5 py-3 text-right">
                            <x-ui.button size="sm" variant="secondary" wire:click="startMovement({{ $item->id }})" target="startMovement({{ $item->id }})">Record movement</x-ui.button>
                        </td>
                    </tr>

                    @if($movingItemId === $item->id)
                        <tr class="bg-canvas-soft/60">
                            <td colspan="5" class="px-5 py-4">
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
                    <tr><td colspan="5" class="px-5 py-8 text-center text-ink-mute">No inventory items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</div>
