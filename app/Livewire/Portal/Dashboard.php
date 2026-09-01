<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Carbon;
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
        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->toDateString());

        $salesByDay = $merchant->salesRecords()
            ->selectRaw('sale_date, SUM(amount) as total')
            ->where('sale_date', '>=', now()->subDays(13)->toDateString())
            ->groupBy('sale_date')
            ->pluck('total', 'sale_date');

        $expensesByDay = $merchant->expenses()
            ->selectRaw('expense_date, SUM(amount) as total')
            ->where('expense_date', '>=', now()->subDays(13)->toDateString())
            ->groupBy('expense_date')
            ->pluck('total', 'expense_date');

        return view('livewire.portal.dashboard', [
            'merchant' => $merchant,
            'salesLast30' => $merchant->salesRecords()->where('sale_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'expensesLast30' => $merchant->expenses()->where('expense_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'lowStockCount' => $merchant->lowStockItems()->count(),
            'unverifiedPayments' => $merchant->paymentRecords()->where('status', 'recorded')->count(),
            'trendLabels' => $days->map(fn ($d) => Carbon::parse($d)->format('d M'))->all(),
            'salesTrend' => $days->map(fn ($d) => (float) ($salesByDay[$d] ?? 0))->all(),
            'expensesTrend' => $days->map(fn ($d) => (float) ($expensesByDay[$d] ?? 0))->all(),
            'lowStockItems' => $merchant->lowStockItems()->limit(5)->get(),
            'expiringSoonCount' => $merchant->expiringSoonItems()->count(),
            'expiringSoonItems' => $merchant->expiringSoonItems()->orderBy('expiry_date')->limit(5)->get(),
            'recentPayments' => $merchant->paymentRecords()->latest('payment_date')->limit(5)->get(),
        ]);
    }
}
