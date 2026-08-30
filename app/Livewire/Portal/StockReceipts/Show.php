<?php

namespace App\Livewire\Portal\StockReceipts;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\StockReceipt;
use App\Models\StockReceiptItem;
use App\Models\SupplierTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Stock Receipt'])]
class Show extends Component
{
    public StockReceipt $receipt;

    public string $inventory_item_id = '';

    public string $quantity = '';

    public string $unit_cost = '';

    public bool $addingNewProduct = false;

    public string $new_name = '';

    public string $new_sku = '';

    public string $new_unit = '';

    public string $new_unit_price = '';

    public function mount(StockReceipt $receipt)
    {
        abort_unless($receipt->merchant_id === Auth::user()->merchant_id, 403);
        $this->receipt = $receipt;
    }

    public function addExistingItem()
    {
        $this->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $item = InventoryItem::where('merchant_id', Auth::user()->merchant_id)->findOrFail($this->inventory_item_id);

        $this->storeLine($item->id, (float) $this->quantity, (float) $this->unit_cost);

        $this->reset(['inventory_item_id', 'quantity', 'unit_cost']);
    }

    public function addNewProduct()
    {
        $this->validate([
            'new_name' => ['required', 'string', 'max:255'],
            'new_unit' => ['nullable', 'string', 'max:255'],
            'new_unit_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $item = InventoryItem::create([
            'merchant_id' => Auth::user()->merchant_id,
            'name' => $this->new_name,
            'sku' => $this->new_sku ?: null,
            'unit' => $this->new_unit ?: null,
            'unit_cost' => $this->unit_cost,
            'unit_price' => $this->new_unit_price ?: null,
            'quantity_on_hand' => 0,
            'reorder_level' => 0,
        ]);

        $this->storeLine($item->id, (float) $this->quantity, (float) $this->unit_cost);

        $this->reset(['new_name', 'new_sku', 'new_unit', 'new_unit_price', 'quantity', 'unit_cost', 'addingNewProduct']);
    }

    protected function storeLine(int $inventoryItemId, float $quantity, float $unitCost): void
    {
        abort_unless($this->receipt->isPending(), 403);

        StockReceiptItem::create([
            'stock_receipt_id' => $this->receipt->id,
            'inventory_item_id' => $inventoryItemId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'subtotal' => $quantity * $unitCost,
        ]);

        $this->receipt->update(['total_amount' => $this->receipt->items()->sum('subtotal')]);
    }

    public function removeItem(int $lineId)
    {
        abort_unless($this->receipt->isPending(), 403);

        $this->receipt->items()->where('id', $lineId)->delete();
        $this->receipt->update(['total_amount' => $this->receipt->items()->sum('subtotal')]);
    }

    public function approve()
    {
        abort_unless(Auth::user()->can('approve-stock-receipts'), 403);
        abort_unless($this->receipt->isPending(), 403);
        abort_if($this->receipt->items()->count() === 0, 422);

        DB::transaction(function () {
            foreach ($this->receipt->items as $line) {
                InventoryItem::where('id', $line->inventory_item_id)->increment('quantity_on_hand', $line->quantity);

                StockMovement::create([
                    'merchant_id' => $this->receipt->merchant_id,
                    'inventory_item_id' => $line->inventory_item_id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $line->quantity,
                    'reference' => 'purchase_receipt',
                    'notes' => "Stock receipt #{$this->receipt->id}",
                    'movement_date' => now()->toDateString(),
                    'recorded_by' => Auth::id(),
                ]);
            }

            if ($this->receipt->supplier_id) {
                SupplierTransaction::create([
                    'merchant_id' => $this->receipt->merchant_id,
                    'supplier_id' => $this->receipt->supplier_id,
                    'type' => 'purchase',
                    'amount' => $this->receipt->total_amount,
                    'description' => "Stock receipt #{$this->receipt->id}",
                    'transaction_date' => now()->toDateString(),
                    'recorded_by' => Auth::id(),
                ]);
            }

            $this->receipt->update([
                'status' => StockReceipt::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        session()->flash('status', 'Stock receipt approved and posted. Inventory has been updated.');
    }

    public function reject()
    {
        abort_unless(Auth::user()->can('approve-stock-receipts'), 403);
        abort_unless($this->receipt->isPending(), 403);

        $this->receipt->update([
            'status' => StockReceipt::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('status', 'Stock receipt rejected. No inventory changes were made.');
    }

    public function render()
    {
        $this->receipt->refresh();

        return view('livewire.portal.stock-receipts.show', [
            'lines' => $this->receipt->items()->with('inventoryItem')->get(),
            'inventoryItems' => InventoryItem::where('merchant_id', Auth::user()->merchant_id)->orderBy('name')->get(),
        ]);
    }
}
