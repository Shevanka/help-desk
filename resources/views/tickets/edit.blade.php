<x-app-layout title="Edit Ticket">
    <x-slot name="header">
        <x-page-title :subtitle="$ticket->ticket_number">Edit Tiket</x-page-title>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                @include('tickets.partials.fields', ['ticket' => $ticket])

                @if ($ticket->hasAttachment())
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-slate-50 px-4 py-3 text-sm">
                        <span class="text-slate-600">Lampiran saat ini:
                            <a href="{{ route('tickets.attachment', $ticket) }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:text-indigo-500">{{ $ticket->attachment_name }}</a>
                        </span>
                        <label class="flex items-center gap-2 text-red-600">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Hapus lampiran
                        </label>
                    </div>
                @endif
                @include('tickets.partials.attachment-input', ['ticket' => $ticket])

                <p class="text-xs text-slate-500">Prioritas, status, dan teknisi diatur dari halaman detail tiket.</p>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                    <x-button-link variant="secondary" :href="route('tickets.show', $ticket)">Batal</x-button-link>
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
