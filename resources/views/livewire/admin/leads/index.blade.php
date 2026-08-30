<div class="space-y-4">
    <div>
        <x-ui.button wire:click="$toggle('showForm')" target="$toggle('showForm')">
            {{ $showForm ? 'Cancel' : '+ Add lead' }}
        </x-ui.button>
    </div>

    @if($showForm)
        <x-ui.card>
            <form wire:submit="create" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <x-ui.input wire:model="business_name" label="Business name" id="business_name" />
                <x-ui.input wire:model="contact_name" label="Contact name" id="contact_name" />
                <x-ui.input wire:model="phone" label="Phone" id="phone" />
                <x-ui.button type="submit" target="create">Save lead</x-ui.button>
            </form>
            @error('business_name') <p class="text-ruby text-[12px] mt-2">{{ $message }}</p> @enderror
        </x-ui.card>
    @endif

    <x-ui.card padding="p-0">
        <table class="w-full text-[13px]">
            <thead class="bg-canvas-soft text-left text-[11px] uppercase tracking-wide text-ink-mute">
                <tr>
                    <th class="px-5 py-3 font-normal">Business</th>
                    <th class="px-5 py-3 font-normal">Contact</th>
                    <th class="px-5 py-3 font-normal">Agent</th>
                    <th class="px-5 py-3 font-normal">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($leads as $lead)
                    <tr class="hover:bg-canvas-soft/60">
                        <td class="px-5 py-3 text-ink font-medium">{{ $lead->business_name }}</td>
                        <td class="px-5 py-3 text-ink-secondary">{{ $lead->contact_name }} {{ $lead->phone }}</td>
                        <td class="px-5 py-3 text-ink-secondary">{{ $lead->agent?->name }}</td>
                        <td class="px-5 py-3">
                            <select wire:change="updateStatus({{ $lead->id }}, $event.target.value)" class="text-[12px] rounded-sm border border-hairline-input px-2 py-1">
                                @foreach(['new','contacted','onboarding','converted','lost'] as $status)
                                    <option value="{{ $status }}" @selected($lead->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-ink-mute">No leads yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</div>
