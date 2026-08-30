<div class="space-y-6">
    <div>
        <button wire:click="$toggle('showSupplierForm')" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">
            {{ $showSupplierForm ? 'Cancel' : '+ Add supplier' }}
        </button>
    </div>

    @if($showSupplierForm)
        <form wire:submit="addSupplier" class="bg-white border rounded-lg p-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Supplier name</label>
                <input type="text" wire:model="name" class="w-full rounded border-slate-300 text-sm">
                @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Contact person</label>
                <input type="text" wire:model="contact_person" class="w-full rounded border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full rounded border-slate-300 text-sm">
            </div>
            <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Save supplier</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Supplier</th>
                    <th class="px-4 py-2">Contact</th>
                    <th class="px-4 py-2">Outstanding balance</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $supplier->name }}</td>
                        <td class="px-4 py-2">{{ $supplier->contact_person }} {{ $supplier->phone }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($supplier->outstandingBalance(), 0) }}</td>
                        <td class="px-4 py-2 text-right">
                            <button wire:click="startTransaction({{ $supplier->id }})" class="text-emerald-700 text-xs hover:underline">Record purchase / payment</button>
                        </td>
                    </tr>

                    @if($transactingSupplierId === $supplier->id)
                        <tr class="bg-slate-50">
                            <td colspan="4" class="px-4 py-3">
                                <form wire:submit="saveTransaction" class="flex flex-wrap gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                                        <select wire:model="transactionType" class="rounded border-slate-300 text-sm">
                                            <option value="purchase">Purchase (increases balance)</option>
                                            <option value="payment">Payment (reduces balance)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Amount (TZS)</label>
                                        <input type="number" step="0.01" wire:model="transactionAmount" class="rounded border-slate-300 text-sm">
                                        @error('transactionAmount') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                                        <input type="date" wire:model="transactionDate" class="rounded border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                                        <input type="text" wire:model="transactionDescription" class="rounded border-slate-300 text-sm">
                                    </div>
                                    <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">Save</button>
                                    <button type="button" wire:click="$set('transactingSupplierId', null)" class="text-slate-500 text-sm">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No suppliers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
