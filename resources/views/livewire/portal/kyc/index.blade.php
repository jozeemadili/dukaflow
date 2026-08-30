<div class="space-y-6">
    <div class="bg-white border rounded-lg p-4">
        <p class="text-sm mb-3">
            Verification status:
            <span @class([
                'px-2 py-0.5 rounded text-xs font-medium',
                'bg-emerald-100 text-emerald-700' => $merchant->kyc_status === 'approved',
                'bg-amber-100 text-amber-700' => in_array($merchant->kyc_status, ['pending', 'under_review']),
                'bg-rose-100 text-rose-700' => $merchant->kyc_status === 'rejected',
            ])>{{ str_replace('_', ' ', $merchant->kyc_status) }}</span>
        </p>

        <form wire:submit="upload" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Document type</label>
                <select wire:model="document_type" class="rounded border-slate-300 text-sm">
                    @foreach($documentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">File</label>
                <input type="file" wire:model="file" class="text-sm">
                @error('file') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="file" class="text-xs text-slate-400 mt-1">Uploading…</div>
            </div>
            <button type="submit" class="bg-emerald-700 text-white rounded px-3 py-2 text-sm hover:bg-emerald-800">Upload document</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">Document type</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Submitted</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($documents as $doc)
                    <tr>
                        <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</td>
                        <td class="px-4 py-2 capitalize">{{ $doc->status }}</td>
                        <td class="px-4 py-2">{{ $doc->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-right">
                            @if($doc->file())
                                <a href="{{ $doc->file()->getUrl() }}" target="_blank" class="text-emerald-700 underline">view</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No documents uploaded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
