<div class="space-y-6">
    <div>
        <button wire:click="$toggle('showItemForm')" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">
            {{ $showItemForm ? 'Cancel' : '+ Add inventory item' }}
        </button>
    </div>

    @if($showItemForm)
        <form wire:submit="addItem" class="bg-white border rounded-lg p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Item name</label>
                <input type="text" wire:model="name" class="w-full rounded border-slate-300 text-sm">
                @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">SKU</label>
                <input type="text" wire:model="sku" class="w-full rounded border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Unit</label>
                <input type="text" wire:model="unit" placeholder="pcs, kg..." class="w-full rounded border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Reorder level</label>
                <input type="number" step="0.01" wire:model="reorder_level" class="w-full rounded border-slate-300 text-sm">
            </div>
            <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Save item</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Item</th>
                    <th class="px-4 py-2">SKU</th>
                    <th class="px-4 py-2">On hand</th>
                    <th class="px-4 py-2">Reorder level</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($items as $item)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                        <td class="px-4 py-2">{{ $item->sku }}</td>
                        <td class="px-4 py-2 {{ $item->isLowStock() ? 'text-rose-600 font-semibold' : '' }}">
                            {{ rtrim(rtrim($item->quantity_on_hand, '0'), '.') }} {{ $item->unit }}
                            @if($item->isLowStock())
                                <span class="text-xs bg-rose-100 text-rose-700 rounded px-1.5 py-0.5 ml-1">low stock</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ rtrim(rtrim($item->reorder_level, '0'), '.') }}</td>
                        <td class="px-4 py-2 text-right">
                            <button wire:click="startMovement({{ $item->id }})" class="text-emerald-700 text-xs hover:underline">Record movement</button>
                        </td>
                    </tr>

                    @if($movingItemId === $item->id)
                        <tr class="bg-slate-50">
                            <td colspan="5" class="px-4 py-3">
                                <form wire:submit="saveMovement" class="flex gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                                        <select wire:model="movementType" class="rounded border-slate-300 text-sm">
                                            <option value="in">Stock in</option>
                                            <option value="out">Stock out</option>
                                            <option value="adjustment">Adjustment</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Quantity</label>
                                        <input type="number" step="0.01" wire:model="movementQuantity" class="rounded border-slate-300 text-sm">
                                        @error('movementQuantity') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">Save</button>
                                    <button type="button" wire:click="$set('movingItemId', null)" class="text-slate-500 text-sm">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No inventory items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
