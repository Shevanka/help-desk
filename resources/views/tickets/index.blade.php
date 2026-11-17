@php
    use App\Enums\TicketStatus;

    $user = auth()->user();
    $title = match (true) {
        $history => 'Ticket History',
        $user->isAdmin() => 'All Tickets',
        $user->isTechnician() => 'Assigned Tickets',
        default => 'My Tickets',
    };
    $route = $history ? 'tickets.history' : 'tickets.index';
    $currentStatus = is_string($filters['status'] ?? null) ? $filters['status'] : null;

    $statuses = $history ? [TicketStatus::Resolved, TicketStatus::Closed] : TicketStatus::cases();
    $tabs = ['' => 'Semua'] + collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all();

    // Filters other than status/q, used to show the "advanced" panel state.
    $advanced = collect($filters)->only(['category', 'priority', 'technician', 'from', 'to'])->filter();
    $keep = collect($filters)->except('status')->filter(fn ($v) => is_string($v) || is_int($v))->all();
@endphp

<x-app-layout :title="$title">
    <x-slot name="header">
        <x-page-title :subtitle="$history ? 'Tiket yang sudah Anda selesaikan · '.$tickets->total().' tiket' : $tickets->total().' tiket'">{{ $title }}</x-page-title>
    </x-slot>
    @can('create', App\Models\Ticket::class)
        <x-slot name="actions">
            <x-button-link :href="route('tickets.create')">+ Buat Tiket</x-button-link>
        </x-slot>
    @endcan

    <x-card :padding="false">
        {{-- Search & filter --}}
        <form method="GET" action="{{ route($route) }}" x-data="{ open: {{ $advanced->isNotEmpty() ? 'true' : 'false' }} }"
              class="space-y-3 border-b border-slate-200 px-4 py-4">
            @if ($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <div class="flex flex-col gap-2 sm:flex-row">
                <label for="q" class="sr-only">Cari tiket</label>
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" maxlength="100"
                           placeholder="Cari subject, nomor tiket, atau deskripsi…"
                           class="block w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="open = !open" :aria-expanded="open"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                        Filter
                        @if ($advanced->isNotEmpty())
                            <span class="rounded-full bg-indigo-600 px-1.5 text-xs text-white">{{ $advanced->count() }}</span>
                        @endif
                    </button>
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Cari</button>
                </div>
            </div>

            <div x-show="open" x-cloak class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label for="f-category" class="block text-xs font-medium text-slate-500">Kategori</label>
                    <select id="f-category" name="category" class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected(($filters['category'] ?? null) == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="f-priority" class="block text-xs font-medium text-slate-500">Prioritas</label>
                    <select id="f-priority" name="priority" class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        @foreach ($priorities as $p)
                            <option value="{{ $p->value }}" @selected(($filters['priority'] ?? null) === $p->value)>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($user->isAdmin())
                    <div>
                        <label for="f-technician" class="block text-xs font-medium text-slate-500">Teknisi</label>
                        <select id="f-technician" name="technician" class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            <option value="none" @selected(($filters['technician'] ?? '') === 'none')>Belum ditugaskan</option>
                            @foreach ($technicians as $tech)
                                <option value="{{ $tech->id }}" @selected(($filters['technician'] ?? '') == $tech->id)>{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label for="f-from" class="block text-xs font-medium text-slate-500">Dibuat dari</label>
                    <input id="f-from" name="from" type="date" value="{{ $filters['from'] ?? '' }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="f-to" class="block text-xs font-medium text-slate-500">Sampai</label>
                    <input id="f-to" name="to" type="date" value="{{ $filters['to'] ?? '' }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            @if (collect($filters)->except($history ? ['status'] : [])->filter()->isNotEmpty())
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Menampilkan hasil filter.</span>
                    <a href="{{ route($route) }}" class="font-medium text-indigo-600 hover:text-indigo-500">Reset filter</a>
                </div>
            @endif
        </form>

        {{-- Status tabs (keep other filters) --}}
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-4 py-2" aria-label="Filter status">
            @foreach ($tabs as $value => $label)
                @php $active = (string) $currentStatus === (string) $value; @endphp
                <a href="{{ route($route, array_filter(['status' => $value] + $keep)) }}"
                   @class([
                       'whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium',
                       'bg-indigo-50 text-indigo-700' => $active,
                       'text-slate-600 hover:bg-slate-100' => ! $active,
                   ]) @if ($active) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <x-ticket-table
            :tickets="$tickets"
            :show-requester="! $user->isRequester()"
            :show-technician="! $user->isTechnician()"
            :empty="$history ? 'Belum ada tiket yang Anda selesaikan.' : ($user->isTechnician() ? 'Tidak ada tiket yang cocok di antara tiket yang ditugaskan kepada Anda.' : 'Tidak ada tiket yang cocok.')" />

        @if ($tickets->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $tickets->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
