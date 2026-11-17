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
                        <span class="font-mono text-xs text-slate-400">{{ $ticket->ticket_number }}</span>
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
