<div class="space-y-4">
    <x-ui.card>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <x-ui.select wire:model="category" label="Category" id="expense_category">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.input type="number" step="0.01" wire:model="amount" label="Amount (TZS)" id="expense_amount" />
            <x-ui.input type="date" wire:model="expense_date" label="Date" id="expense_date" />
            <x-ui.input wire:model="description" label="Note" id="expense_note" />
            <x-ui.button type="submit" target="save">Record expense</x-ui.button>
        </form>
        @error('amount') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
    </x-ui.card>

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Date</th>
                    <th class="px-5 py-3 font-normal">Category</th>
                    <th class="px-5 py-3 font-normal">Amount</th>
                    <th class="px-5 py-3 font-normal">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-canvas-soft/60">
                        <td class="px-5 py-3 text-ink-secondary">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-ink-secondary capitalize">{{ $expense->category }}</td>
                        <td class="px-5 py-3 tnum text-ink font-medium">TZS {{ number_format($expense->amount, 0) }}</td>
                        <td class="px-5 py-3 text-ink-mute">{{ $expense->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-ink-mute">No expenses recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div>{{ $expenses->links() }}</div>
</div>
