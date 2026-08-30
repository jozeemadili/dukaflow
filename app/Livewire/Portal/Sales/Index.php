<?php

namespace App\Livewire\Portal\Sales;

use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal', ['title' => 'Sales'])]
class Index extends Component
{
    use WithPagination;

    public string $amount = '';

    public string $items_count = '';

    public string $description = '';

    public string $sale_date;

    public ?int $expandedSaleId = null;

    public function mount()
    {
        $this->sale_date = now()->toDateString();
    }

    public function toggleExpand(int $saleId): void
    {
        $this->expandedSaleId = $this->expandedSaleId === $saleId ? null : $saleId;
    }

    public function save()
    {
        $this->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'items_count' => ['nullable', 'integer', 'min:0'],
            'sale_date' => ['required', 'date'],
        ]);

        SalesRecord::create([
            'merchant_id' => Auth::user()->merchant_id,
            'amount' => $this->amount,
            'items_count' => $this->items_count ?: null,
            'description' => $this->description,
            'sale_date' => $this->sale_date,
            'recorded_by' => Auth::id(),
        ]);

        $this->reset(['amount', 'items_count', 'description']);
        $this->sale_date = now()->toDateString();
        session()->flash('status', 'Sale recorded.');
    }

    public function render()
    {
        $sales = SalesRecord::where('merchant_id', Auth::user()->merchant_id)
            ->withCount('items')
            ->latest('sale_date')
            ->latest('id')
            ->paginate(15);

        return view('livewire.portal.sales.index', compact('sales'));
    }
}
