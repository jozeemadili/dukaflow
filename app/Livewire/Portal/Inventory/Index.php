<?php

namespace App\Livewire\Portal\Inventory;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Inventory'])]
class Index extends Component
{
    public bool $showItemForm = false;

    public string $name = '';

    public string $sku = '';

    public string $unit = '';

    public string $reorder_level = '0';

    public string $unit_cost = '';

    public string $unit_price = '';

    public ?int $movingItemId = null;

    public string $movementType = 'in';

    public string $movementQuantity = '';

    public function addItem()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        InventoryItem::create([
            'merchant_id' => Auth::user()->merchant_id,
            'name' => $this->name,
            'sku' => $this->sku ?: null,
            'unit' => $this->unit ?: null,
            'reorder_level' => $this->reorder_level,
            'unit_cost' => $this->unit_cost ?: null,
            'unit_price' => $this->unit_price ?: null,
        ]);

        $this->reset(['name', 'sku', 'unit', 'reorder_level', 'unit_cost', 'unit_price', 'showItemForm']);
        $this->reorder_level = '0';
        session()->flash('status', 'Inventory item added.');
    }

    public function startMovement(int $itemId)
    {
        $this->movingItemId = $itemId;
        $this->movementType = 'in';
        $this->movementQuantity = '';
    }

    public function saveMovement()
    {
        $this->validate([
            'movementQuantity' => ['required', 'numeric', 'min:0.01'],
            'movementType' => ['required', 'in:in,out,adjustment'],
        ]);

        $item = InventoryItem::where('merchant_id', Auth::user()->merchant_id)->findOrFail($this->movingItemId);

        DB::transaction(function () use ($item) {
            StockMovement::create([
                'merchant_id' => Auth::user()->merchant_id,
                'inventory_item_id' => $item->id,
                'type' => $this->movementType,
                'quantity' => $this->movementQuantity,
                'movement_date' => now()->toDateString(),
                'recorded_by' => Auth::id(),
            ]);

            $delta = $this->movementType === 'out' ? -$this->movementQuantity : $this->movementQuantity;
            $item->increment('quantity_on_hand', $delta);
        });

        $this->reset(['movingItemId', 'movementQuantity']);
        session()->flash('status', 'Stock movement recorded.');
    }

    public function render()
    {
        return view('livewire.portal.inventory.index', [
            'items' => InventoryItem::where('merchant_id', Auth::user()->merchant_id)->orderBy('name')->get(),
        ]);
    }
}
