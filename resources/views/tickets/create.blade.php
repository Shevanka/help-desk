<x-app-layout title="Create Ticket">
    <x-slot name="header">
        <x-page-title subtitle="Laporkan kendala teknis yang Anda alami">Buat Tiket Baru</x-page-title>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                @csrf
                @include('tickets.partials.fields', ['ticket' => null])

                <div>
                    <x-input-label value="Prioritas" />
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        @foreach ($priorities as $priority)
                            <label class="flex cursor-pointer gap-3 rounded-lg border border-slate-200 p-3 hover:bg-slate-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="priority" value="{{ $priority->value }}" class="mt-0.5 text-indigo-600 focus:ring-indigo-500"
                                       @checked(old('priority', 'medium') === $priority->value) required>
                                <span>
                                    <x-priority-badge :priority="$priority" />
                                    <span class="mt-1 block text-xs text-slate-500">{{ $priority->description() }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                    <x-button-link variant="secondary" :href="route('tickets.index')">Batal</x-button-link>
                    <x-primary-button>Kirim Laporan</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
