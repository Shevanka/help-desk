@php
    $user = auth()->user();
    $title = match (true) {
        $user->isAdmin() => 'All Tickets',
        $user->isTechnician() => 'Assigned Tickets',
        default => 'My Tickets',
    };
    $currentStatus = $filters['status'] ?? null;
    $tabs = ['' => 'Semua'] + collect(App\Enums\TicketStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all();
@endphp

<x-app-layout :title="$title">
    <x-slot name="header">
        <x-page-title :subtitle="$tickets->total().' tiket'">{{ $title }}</x-page-title>
    </x-slot>
    @can('create', App\Models\Ticket::class)
        <x-slot name="actions">
            <x-button-link :href="route('tickets.create')">+ Buat Tiket</x-button-link>
        </x-slot>
    @endcan

    <x-card :padding="false">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 md:flex-row md:items-center md:justify-between">
            <nav class="-mb-px flex gap-1 overflow-x-auto" aria-label="Filter status">
                @foreach ($tabs as $value => $label)
                    @php $active = (string) $currentStatus === (string) $value; @endphp
                    <a href="{{ route('tickets.index', array_filter(['status' => $value, 'technician' => $filters['technician'] ?? null])) }}"
                       @class([
                           'whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium',
                           'bg-indigo-50 text-indigo-700' => $active,
                           'text-slate-600 hover:bg-slate-100' => ! $active,
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            @if ($user->isAdmin())
                <form method="GET" action="{{ route('tickets.index') }}" class="flex items-center gap-2">
                    @if ($currentStatus)
                        <input type="hidden" name="status" value="{{ $currentStatus }}">
                    @endif
                    <label for="technician" class="text-sm text-slate-500">Teknisi</label>
                    <select id="technician" name="technician" onchange="this.form.submit()"
                            class="rounded-lg border-slate-300 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="none" @selected(($filters['technician'] ?? '') === 'none')>Belum ditugaskan</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(($filters['technician'] ?? '') == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    <noscript><button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white">Terapkan</button></noscript>
                </form>
            @endif
        </div>

        <x-ticket-table
            :tickets="$tickets"
            :show-requester="! $user->isRequester()"
            :show-technician="! $user->isTechnician()"
            :empty="$user->isTechnician() ? 'Tidak ada tiket yang ditugaskan kepada Anda.' : 'Tidak ada tiket yang cocok.'" />

        @if ($tickets->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $tickets->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
