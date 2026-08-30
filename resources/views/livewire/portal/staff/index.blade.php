<div>
    <div class="mb-4">
        <button wire:click="$toggle('showForm')" class="bg-emerald-700 text-white rounded px-3 py-1.5 text-sm hover:bg-emerald-800">
            {{ $showForm ? 'Cancel' : '+ Add staff member' }}
        </button>
    </div>

    @if($showForm)
        <form wire:submit="create" class="bg-white border rounded-lg p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                <input type="text" wire:model="name" class="w-full rounded border-slate-300 text-sm">
                @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full rounded border-slate-300 text-sm">
                @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full rounded border-slate-300 text-sm">
            </div>
            <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Create</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Phone</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($staff as $user)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->phone }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No staff accounts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
