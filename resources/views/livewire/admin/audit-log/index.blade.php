<div>
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-2">When</th>
                    <th class="px-4 py-2">Actor</th>
                    <th class="px-4 py-2">Event</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($activities as $activity)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $activity->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $activity->causer?->name ?? 'System' }}</td>
                        <td class="px-4 py-2">{{ $activity->event }}</td>
                        <td class="px-4 py-2">{{ class_basename($activity->subject_type ?? '') }} #{{ $activity->subject_id }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $activity->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $activities->links() }}</div>
</div>
