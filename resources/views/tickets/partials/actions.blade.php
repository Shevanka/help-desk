@php
    $selectClass = 'block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $buttonClass = 'w-full rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500';
    $canAssign = auth()->user()->can('assign', $ticket);
    $canPriority = auth()->user()->can('changePriority', $ticket);
@endphp

@if ($allowedStatuses !== [] || $canAssign || $canPriority)
    <x-card title="Tindakan">
        <div class="space-y-6">
            @if ($allowedStatuses !== [])
                <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="block text-sm font-medium text-slate-700">Ubah Status</label>

                    @if (count($allowedStatuses) === 1)
                        @php $next = $allowedStatuses[0]; @endphp
                        <input type="hidden" name="status" value="{{ $next->value }}">
                        <textarea name="note" rows="2" maxlength="1000" class="{{ $selectClass }}" placeholder="Catatan (opsional)"></textarea>
                        <button class="{{ $buttonClass }}">
                            @switch($next)
                                @case(App\Enums\TicketStatus::InProgress) Mulai Kerjakan (In Progress) @break
                                @case(App\Enums\TicketStatus::Resolved) Tandai Resolved @break
                                @case(App\Enums\TicketStatus::Closed) Konfirmasi Selesai &amp; Tutup Tiket @break
                                @default Ubah ke {{ $next->label() }}
                            @endswitch
                        </button>
                    @else
                        <select id="status" name="status" class="{{ $selectClass }}" required>
                            @foreach ($allowedStatuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <textarea name="note" rows="2" maxlength="1000" class="{{ $selectClass }}" placeholder="Catatan (opsional)"></textarea>
                        <button class="{{ $buttonClass }}">Simpan Status</button>
                    @endif
                </form>
            @endif

            @if ($canAssign)
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <label for="technician_id" class="block text-sm font-medium text-slate-700">Tugaskan Teknisi</label>
                    <select id="technician_id" name="technician_id" class="{{ $selectClass }}">
                        <option value="">— Tidak ada —</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected($ticket->technician_id === $tech->id)>
                                {{ $tech->name }} ({{ $tech->active_tickets_count }} tiket aktif)
                            </option>
                        @endforeach
                    </select>
                    <button class="{{ $buttonClass }}">{{ $ticket->technician_id ? 'Ubah Penugasan' : 'Tugaskan' }}</button>
                </form>
            @endif

            @if ($canPriority)
                <form method="POST" action="{{ route('tickets.priority', $ticket) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <label for="priority" class="block text-sm font-medium text-slate-700">Ubah Prioritas</label>
                    <div class="flex gap-2">
                        <select id="priority" name="priority" class="{{ $selectClass }}">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}" @selected($ticket->priority === $priority)>{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                        <button class="shrink-0 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Simpan</button>
                    </div>
                </form>
            @endif
        </div>
    </x-card>
@endif
