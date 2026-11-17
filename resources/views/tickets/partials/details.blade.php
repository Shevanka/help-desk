<x-card title="Detail Tiket">
    <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Nomor</dt>
            <dd class="font-mono text-slate-900">{{ $ticket->ticket_number }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Status</dt>
            <dd><x-status-badge :status="$ticket->status" /></dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Prioritas</dt>
            <dd><x-priority-badge :priority="$ticket->priority" /></dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Kategori</dt>
            <dd class="text-right text-slate-900">{{ $ticket->category->name }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Pelapor</dt>
            <dd class="text-right text-slate-900">{{ $ticket->requester->name }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Teknisi</dt>
            <dd class="text-right text-slate-900">
                {{ $ticket->technician?->name ?? '—' }}
            </dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Dibuat</dt>
            <dd class="text-right text-slate-900">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Diperbarui</dt>
            <dd class="text-right text-slate-900">{{ $ticket->updated_at->format('d M Y H:i') }}</dd>
        </div>
        @if ($ticket->resolved_at)
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Diselesaikan</dt>
                <dd class="text-right text-slate-900">{{ $ticket->resolved_at->format('d M Y H:i') }}</dd>
            </div>
        @endif
        @if ($ticket->closed_at)
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Ditutup</dt>
                <dd class="text-right text-slate-900">{{ $ticket->closed_at->format('d M Y H:i') }}</dd>
            </div>
        @endif
    </dl>
</x-card>
