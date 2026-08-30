<?php

namespace App\Livewire\Portal\Pos;

use App\Models\InventoryItem;
use App\Models\SaleItem;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Point of Sale'])]
class Index extends Component
{
    public string $search = '';

    /** @var array<int, array{name: string, unit: ?string, unit_price: float, quantity: float, max: float}> */
    public array $cart = [];

    public ?string $lastReceiptTotal = null;

    public function addToCart(int $itemId): void
    {
        $item = InventoryItem::where('merchant_id', Auth::user()->merchant_id)->findOrFail($itemId);

        $inCart = $this->cart[$itemId]['quantity'] ?? 0;

        if ($inCart >= (float) $item->quantity_on_hand) {
            return;
        }

        $this->cart[$itemId] = [
            'name' => $item->name,
            'unit' => $item->unit,
            'unit_price' => (float) $item->unit_price,
            'quantity' => $inCart + 1,
            'max' => (float) $item->quantity_on_hand,
        ];
    }

    public function incrementQty(int $itemId): void
    {
        if (! isset($this->cart[$itemId])) {
            return;
        }

        if ($this->cart[$itemId]['quantity'] < $this->cart[$itemId]['max']) {
            $this->cart[$itemId]['quantity']++;
        }
    }

    public function decrementQty(int $itemId): void
    {
        if (! isset($this->cart[$itemId])) {
            return;
        }

        $this->cart[$itemId]['quantity']--;

        if ($this->cart[$itemId]['quantity'] <= 0) {
            unset($this->cart[$itemId]);
        }
    }

    public function removeFromCart(int $itemId): void
    {
        unset($this->cart[$itemId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function cartTotal(): float
    {
        return array_sum(array_map(fn ($line) => $line['quantity'] * $line['unit_price'], $this->cart));
    }

    public function cartCount(): float
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $merchantId = Auth::user()->merchant_id;
        $total = $this->cartTotal();

        DB::transaction(function () use ($merchantId, $total) {
            $sale = SalesRecord::create([
                'merchant_id' => $merchantId,
                'amount' => $total,
                'items_count' => $this->cartCount(),
                'description' => 'POS sale',
                'sale_date' => now()->toDateString(),
                'recorded_by' => Auth::id(),
            ]);

            foreach ($this->cart as $itemId => $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'inventory_item_id' => $itemId,
                    'item_name' => $line['name'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'subtotal' => $line['quantity'] * $line['unit_price'],
                ]);

                StockMovement::create([
                    'merchant_id' => $merchantId,
                    'inventory_item_id' => $itemId,
                    'type' => StockMovement::TYPE_OUT,
                    'quantity' => $line['quantity'],
                    'reference' => 'sale',
                    'notes' => "POS sale #{$sale->id}",
                    'movement_date' => now()->toDateString(),
                    'recorded_by' => Auth::id(),
                ]);

                InventoryItem::where('id', $itemId)->decrement('quantity_on_hand', $line['quantity']);
            }
        });

        $this->lastReceiptTotal = number_format($total, 0);
        $this->cart = [];
    }

    public function render()
    {
        $items = InventoryItem::where('merchant_id', Auth::user()->merchant_id)
            ->whereNotNull('unit_price')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->get();

        return view('livewire.portal.pos.index', [
            'items' => $items,
        ]);
    }
}
