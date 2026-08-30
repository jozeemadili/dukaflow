<div class="space-y-4">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card padding="p-5">
            <x-ui.stat label="Sales (30 days)" value="TZS {{ number_format($salesLast30, 0) }}" tone="primary" />
        </x-ui.card>
        <x-ui.card padding="p-5">
            <x-ui.stat label="Expenses (30 days)" value="TZS {{ number_format($expensesLast30, 0) }}" tone="ruby" />
        </x-ui.card>
        <x-ui.card padding="p-5">
            <x-ui.stat label="Low stock items" :value="$lowStockCount" tone="lemon" />
        </x-ui.card>
        <x-ui.card padding="p-5">
            <x-ui.stat label="Payments awaiting verification" :value="$unverifiedPayments" tone="lemon" />
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[15px] text-ink-secondary">Sales vs expenses (14 days)</h2>
            </div>
            <x-ui.chart
                type="line"
                :labels="$trendLabels"
                :height="220"
                :datasets="[
                    [
                        'label' => 'Sales',
                        'data' => $salesTrend,
                        'borderColor' => '#c89a44',
                        'backgroundColor' => 'rgba(200,154,68,0.12)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 0,
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => 'Expenses',
                        'data' => $expensesTrend,
                        'borderColor' => '#ea2261',
                        'backgroundColor' => 'rgba(234,34,97,0.06)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 0,
                        'borderWidth' => 2,
                    ],
                ]"
                :options="[
                    'scales' => [
                        'y' => ['ticks' => ['display' => false], 'grid' => ['display' => false], 'border' => ['display' => false]],
                        'x' => ['grid' => ['display' => false], 'border' => ['display' => false], 'ticks' => ['color' => '#64748d', 'font' => ['size' => 11]]],
                    ],
                ]"
            />
            <div class="flex items-center gap-4 mt-3 text-[12px] text-ink-mute">
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-primary"></span> Sales</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-ruby"></span> Expenses</span>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-[15px] text-ink-secondary mb-4">Low stock items</h2>
            @forelse($lowStockItems as $item)
                <div class="flex items-center justify-between py-2 border-b border-hairline last:border-0 text-[13px]">
                    <span class="text-ink-secondary">{{ $item->name }}</span>
                    <span class="tnum text-ruby font-medium">{{ rtrim(rtrim($item->quantity_on_hand, '0'), '.') }} {{ $item->unit }} left</span>
                </div>
            @empty
                <p class="text-[13px] text-ink-mute">Nothing low on stock right now.</p>
            @endforelse
            <a href="{{ route('portal.inventory.index') }}" class="inline-block mt-3 text-[13px] text-primary hover:text-primary-deep">Manage inventory &rarr;</a>
        </x-ui.card>

        <x-ui.card class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[15px] text-ink-secondary">Recent payments</h2>
                <a href="{{ route('portal.payments.index') }}" class="text-[13px] text-primary hover:text-primary-deep">View all &rarr;</a>
            </div>
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="text-left text-ink-mute border-b border-hairline">
                        <th class="pb-2 font-normal">Date</th>
                        <th class="pb-2 font-normal">Amount</th>
                        <th class="pb-2 font-normal">Payer</th>
                        <th class="pb-2 font-normal">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                        <tr class="border-b border-hairline last:border-0">
                            <td class="py-2 text-ink-secondary">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="py-2 tnum text-ink">TZS {{ number_format($payment->amount, 0) }}</td>
                            <td class="py-2 text-ink-secondary">{{ $payment->payer_name ?: '—' }}</td>
                            <td class="py-2">
                                <x-ui.badge :tone="$payment->status === 'verified' ? 'success' : ($payment->status === 'flagged' ? 'danger' : 'warning')">
                                    {{ $payment->status }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-ink-mute">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>
    </div>

    <div class="flex gap-3 text-[13px] flex-wrap">
        <a href="{{ route('portal.sales.index') }}" class="bg-canvas border border-hairline rounded-pill px-4 py-2 hover:border-primary/40 text-ink-secondary">Record a sale &rarr;</a>
        <a href="{{ route('portal.expenses.index') }}" class="bg-canvas border border-hairline rounded-pill px-4 py-2 hover:border-primary/40 text-ink-secondary">Record an expense &rarr;</a>
        <a href="{{ route('portal.payments.index') }}" class="bg-canvas border border-hairline rounded-pill px-4 py-2 hover:border-primary/40 text-ink-secondary">Record a payment &rarr;</a>
        <a href="{{ route('portal.inventory.index') }}" class="bg-canvas border border-hairline rounded-pill px-4 py-2 hover:border-primary/40 text-ink-secondary">Manage stock &rarr;</a>
    </div>
</div>
