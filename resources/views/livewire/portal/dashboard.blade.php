<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Sales (30 days)</p>
            <p class="text-2xl font-bold mt-1 text-emerald-700">TZS {{ number_format($salesLast30, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Expenses (30 days)</p>
            <p class="text-2xl font-bold mt-1 text-rose-600">TZS {{ number_format($expensesLast30, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Low stock items</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $lowStockCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Payments awaiting verification</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $unverifiedPayments }}</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3 text-sm flex-wrap">
        <a href="{{ route('portal.sales.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Record a sale &rarr;</a>
        <a href="{{ route('portal.expenses.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Record an expense &rarr;</a>
        <a href="{{ route('portal.payments.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Record a payment &rarr;</a>
        <a href="{{ route('portal.inventory.index') }}" class="bg-white border rounded px-4 py-2 hover:bg-slate-50">Manage stock &rarr;</a>
    </div>
</div>
