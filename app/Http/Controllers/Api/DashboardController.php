<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\PaymentRecordResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
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

        return response()->json([
            'sales_last_30_days' => (float) $merchant->salesRecords()->where('sale_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'expenses_last_30_days' => (float) $merchant->expenses()->where('expense_date', '>=', $thirtyDaysAgo)->sum('amount'),
            'low_stock_count' => $merchant->lowStockItems()->count(),
            'expiring_soon_count' => $merchant->expiringSoonItems()->count(),
            'unverified_payments_count' => $merchant->paymentRecords()->where('status', 'recorded')->count(),
            'trend' => [
                'labels' => $days->map(fn ($d) => Carbon::parse($d)->format('d M'))->all(),
                'sales' => $days->map(fn ($d) => (float) ($salesByDay[$d] ?? 0))->all(),
                'expenses' => $days->map(fn ($d) => (float) ($expensesByDay[$d] ?? 0))->all(),
            ],
            'low_stock_items' => InventoryItemResource::collection($merchant->lowStockItems()->limit(5)->get()),
            'expiring_soon_items' => InventoryItemResource::collection($merchant->expiringSoonItems()->orderBy('expiry_date')->limit(5)->get()),
            'recent_payments' => PaymentRecordResource::collection($merchant->paymentRecords()->latest('payment_date')->limit(5)->get()),
        ]);
    }
}
