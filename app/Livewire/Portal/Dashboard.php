<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $merchant = Auth::user()->merchant;

        $thirtyDaysAgo = now()->subDays(30);

        return view('livewire.portal.dashboard', [
            'merchant' => $merchant,
            'salesLast30' => $merchant->salesRecords()->where('sale_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'expensesLast30' => $merchant->expenses()->where('expense_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'lowStockCount' => $merchant->inventoryItems()->whereColumn('quantity_on_hand', '<=', 'reorder_level')->count(),
            'unverifiedPayments' => $merchant->paymentRecords()->where('status', 'recorded')->count(),
        ]);
    }
}
