<div class="space-y-6">
    <form wire:submit="save" class="bg-white border rounded-lg p-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
            <select wire:model="category" class="w-full rounded border-slate-300 text-sm">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Amount (TZS)</label>
            <input type="number" step="0.01" wire:model="amount" class="w-full rounded border-slate-300 text-sm">
            @error('amount') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
            <input type="date" wire:model="expense_date" class="w-full rounded border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
            <input type="text" wire:model="description" class="w-full rounded border-slate-300 text-sm">
        </div>
        <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Record expense</button>
    </form>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 py-2">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-4 py-2 capitalize">{{ $expense->category }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($expense->amount, 0) }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $expense->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No expenses recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $expenses->links() }}</div>
</div>
