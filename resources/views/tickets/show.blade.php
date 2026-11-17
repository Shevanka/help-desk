<x-app-layout :title="$ticket->ticket_number">
    <x-slot name="header">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="hidden rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 sm:block" aria-label="Kembali">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </a>
            <x-page-title :subtitle="$ticket->ticket_number">{{ $ticket->subject }}</x-page-title>
        </div>
    </x-slot>
    <x-slot name="actions">
        @can('update', $ticket)
            <x-button-link variant="secondary" :href="route('tickets.edit', $ticket)">Edit</x-button-link>
        @endcan
        @can('delete', $ticket)
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Hapus tiket {{ $ticket->ticket_number }}? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button class="rounded-lg px-3 py-2 text-sm font-semibold text-red-600 ring-1 ring-inset ring-red-200 hover:bg-red-50">Hapus</button>
            </form>
        @endcan
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-6 xl:col-span-2">
            <x-card>
                <div class="flex flex-wrap items-center gap-2">
                    <x-status-badge :status="$ticket->status" />
                    <x-priority-badge :priority="$ticket->priority" />
                    <span class="text-xs text-slate-500">· {{ $ticket->category->name }}</span>
                </div>
                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $ticket->description }}</p>

                @if ($ticket->hasAttachment())
                    <div class="mt-5 rounded-lg border border-slate-200 p-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Lampiran</p>
                        @if ($ticket->attachmentIsImage())
                            <a href="{{ route('tickets.attachment', $ticket) }}" target="_blank" rel="noopener" class="block">
                                <img src="{{ route('tickets.attachment', $ticket) }}" alt="Lampiran: {{ $ticket->attachment_name }}"
                                     class="max-h-80 rounded-md border border-slate-100 object-contain" loading="lazy">
                            </a>
                        @endif
                        <div class="mt-2 flex items-center justify-between gap-3 text-sm">
                            <span class="flex min-w-0 items-center gap-2 text-slate-700">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg>
                                <span class="truncate">{{ $ticket->attachment_name }}</span>
                            </span>
                            <a href="{{ route('tickets.attachment', [$ticket, 'download' => 1]) }}" class="shrink-0 font-medium text-indigo-600 hover:text-indigo-500">Unduh</a>
                        </div>
                    </div>
                @endif
                <p class="mt-4 text-xs text-slate-400">
                    Dilaporkan oleh <span class="font-medium text-slate-600">{{ $ticket->requester->name }}</span>
                    pada {{ $ticket->created_at->format('d M Y, H:i') }}
                </p>
            </x-card>

            @include('tickets.partials.comments')
        </div>

        {{-- Side column --}}
        <div class="space-y-6">
            @include('tickets.partials.details')
            @include('tickets.partials.actions')
            @include('tickets.partials.activities')
        </div>
    </div>
</x-app-layout>
