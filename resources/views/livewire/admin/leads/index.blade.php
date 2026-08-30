<div>
    <div class="mb-4">
        <button wire:click="$toggle('showForm')" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">
            {{ $showForm ? 'Cancel' : '+ Add lead' }}
        </button>
    </div>

    @if($showForm)
        <form wire:submit="create" class="bg-white border rounded-lg p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Business name</label>
                <input type="text" wire:model="business_name" class="w-full rounded border-slate-300 text-sm">
                @error('business_name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Contact name</label>
                <input type="text" wire:model="contact_name" class="w-full rounded border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full rounded border-slate-300 text-sm">
            </div>
            <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Save lead</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Business</th>
                    <th class="px-4 py-2">Contact</th>
                    <th class="px-4 py-2">Agent</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($leads as $lead)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $lead->business_name }}</td>
                        <td class="px-4 py-2">{{ $lead->contact_name }} {{ $lead->phone }}</td>
                        <td class="px-4 py-2">{{ $lead->agent?->name }}</td>
                        <td class="px-4 py-2">
                            <select wire:change="updateStatus({{ $lead->id }}, $event.target.value)" class="text-xs rounded border-slate-300">
                                @foreach(['new','contacted','onboarding','converted','lost'] as $status)
                                    <option value="{{ $status }}" @selected($lead->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No leads yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
