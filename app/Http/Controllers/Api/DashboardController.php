<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\PaymentRecordResource;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const PERIOD_LABELS = [
        'week' => 'Last 7 days',
        'month' => 'Last 30 days',
        'year' => 'This year',
    ];

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        $period = $request->query('period', 'month');
        if (! array_key_exists($period, self::PERIOD_LABELS)) {
            $period = 'month';
        }

        $trend = match ($period) {
            'week' => $this->dailyTrend($merchant, 7),
            'year' => $this->monthlyTrend($merchant, 12),
            default => $this->dailyTrend($merchant, 30),
        };

        return response()->json([
            'period' => $period,
            'period_label' => self::PERIOD_LABELS[$period],
            'sales_total' => (float) $merchant->salesRecords()->where('sale_date', '>=', $trend['start'])->sum('amount'),
            'expenses_total' => (float) $merchant->expenses()->where('expense_date', '>=', $trend['start'])->sum('amount'),
            'low_stock_count' => $merchant->lowStockItems()->count(),
            'expiring_soon_count' => $merchant->expiringSoonItems()->count(),
            'unverified_payments_count' => $merchant->paymentRecords()->where('status', 'recorded')->count(),
            'trend' => [
                'labels' => $trend['labels'],
                'sales' => $trend['sales'],
                'expenses' => $trend['expenses'],
            ],
            'low_stock_items' => InventoryItemResource::collection($merchant->lowStockItems()->limit(5)->get()),
            'expiring_soon_items' => InventoryItemResource::collection($merchant->expiringSoonItems()->orderBy('expiry_date')->limit(5)->get()),
            'recent_payments' => PaymentRecordResource::collection($merchant->paymentRecords()->latest('payment_date')->limit(5)->get()),
        ]);
    }

    /**
     * Day-by-day sales/expenses for the trailing $days days (inclusive of
     * today). Used for the "week" (7) and "month" (30) periods.
     */
    private function dailyTrend(Merchant $merchant, int $days): array
    {
        $dates = collect(range($days - 1, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $start = $dates->first();

        $salesByDay = $merchant->salesRecords()
            ->selectRaw('sale_date, SUM(amount) as total')
            ->where('sale_date', '>=', $start)
            ->groupBy('sale_date')
            ->pluck('total', 'sale_date');

        $expensesByDay = $merchant->expenses()
            ->selectRaw('expense_date, SUM(amount) as total')
            ->where('expense_date', '>=', $start)
            ->groupBy('expense_date')
            ->pluck('total', 'expense_date');

        return [
            'start' => $start,
            'labels' => $dates->map(fn ($d) => Carbon::parse($d)->format('d M'))->all(),
            'sales' => $dates->map(fn ($d) => (float) ($salesByDay[$d] ?? 0))->all(),
            'expenses' => $dates->map(fn ($d) => (float) ($expensesByDay[$d] ?? 0))->all(),
        ];
    }

    /**
     * Month-by-month sales/expenses for the trailing $months calendar
     * months (inclusive of the current month). Used for the "year" period.
     * Grouped in PHP rather than a DB-specific date-format function, so it
     * works the same regardless of the underlying database driver.
     */
    private function monthlyTrend(Merchant $merchant, int $months): array
    {
        $periods = collect(range($months - 1, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());
        $start = $periods->first();

        $sales = $merchant->salesRecords()
            ->where('sale_date', '>=', $start)
            ->get(['sale_date', 'amount'])
            ->groupBy(fn ($row) => Carbon::parse($row->sale_date)->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount'));

        $expenses = $merchant->expenses()
            ->where('expense_date', '>=', $start)
            ->get(['expense_date', 'amount'])
            ->groupBy(fn ($row) => Carbon::parse($row->expense_date)->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount'));

        return [
            'start' => $start->toDateString(),
            'labels' => $periods->map(fn ($p) => $p->format('M Y'))->all(),
            'sales' => $periods->map(fn ($p) => $sales[$p->format('Y-m')] ?? 0.0)->all(),
            'expenses' => $periods->map(fn ($p) => $expenses[$p->format('Y-m')] ?? 0.0)->all(),
        ];
    }
}
