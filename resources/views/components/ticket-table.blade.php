@props(['tickets', 'showRequester' => true, 'showTechnician' => true, 'empty' => 'Belum ada tiket.'])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">Tiket</th>
                <th class="hidden px-4 py-3 md:table-cell">Kategori</th>
                <th class="px-4 py-3">Prioritas</th>
                <th class="px-4 py-3">Status</th>
                @if ($showRequester)
                    <th class="hidden px-4 py-3 lg:table-cell">Pelapor</th>
                @endif
                @if ($showTechnician)
                    <th class="hidden px-4 py-3 lg:table-cell">Teknisi</th>
                @endif
                <th class="hidden px-4 py-3 sm:table-cell">Diperbarui</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($tickets as $ticket)
                <tr class="hover:bg-slate-50">
                    <td class="max-w-xs px-4 py-3">
                        <a href="{{ route('tickets.show', $ticket) }}" class="block font-medium text-slate-900 hover:text-indigo-600">
                            <span class="block truncate">{{ $ticket->subject }}</span>
                        </a>
                        <span class="flex items-center gap-1.5 font-mono text-xs text-slate-400">
                            {{ $ticket->ticket_number }}
                            @if ($ticket->hasAttachment())
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-label="Ada lampiran"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg>
                            @endif
                        </span>
                    </td>
                    <td class="hidden px-4 py-3 text-slate-600 md:table-cell">{{ $ticket->category->name }}</td>
                    <td class="px-4 py-3"><x-priority-badge :priority="$ticket->priority" /></td>
                    <td class="px-4 py-3"><x-status-badge :status="$ticket->status" /></td>
                    @if ($showRequester)
                        <td class="hidden px-4 py-3 text-slate-600 lg:table-cell">{{ $ticket->requester->name }}</td>
                    @endif
                    @if ($showTechnician)
                        <td class="hidden px-4 py-3 lg:table-cell">
                            @if ($ticket->technician)
                                <span class="text-slate-600">{{ $ticket->technician->name }}</span>
                            @else
                                <span class="text-xs italic text-slate-400">Belum ditugaskan</span>
                            @endif
                        </td>
                    @endif
                    <td class="hidden whitespace-nowrap px-4 py-3 text-slate-500 sm:table-cell" title="{{ $ticket->updated_at->format('d M Y H:i') }}">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-slate-500">{{ $empty }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
