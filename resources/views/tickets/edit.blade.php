<x-app-layout title="Edit Ticket">
    <x-slot name="header">
        <x-page-title :subtitle="$ticket->ticket_number">Edit Tiket</x-page-title>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-6">
                @csrf
                @method('PUT')
                @include('tickets.partials.fields', ['ticket' => $ticket])

                <p class="text-xs text-slate-500">Prioritas, status, dan teknisi diatur dari halaman detail tiket.</p>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                    <x-button-link variant="secondary" :href="route('tickets.show', $ticket)">Batal</x-button-link>
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
