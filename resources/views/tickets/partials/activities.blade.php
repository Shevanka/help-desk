@php
    $dot = [
        'created' => 'bg-sky-500',
        'assigned' => 'bg-violet-500',
        'status_changed' => 'bg-amber-500',
        'priority_changed' => 'bg-orange-500',
        'commented' => 'bg-slate-400',
        'updated' => 'bg-slate-400',
        'attachment' => 'bg-teal-500',
    ];
@endphp

<x-card title="Riwayat Aktivitas">
    <ol class="relative space-y-4 border-l border-slate-200 pl-5">
        @forelse ($ticket->activities as $activity)
            <li class="relative">
                <span class="absolute -left-[1.6rem] top-1.5 h-2.5 w-2.5 rounded-full ring-4 ring-white {{ $dot[$activity->action] ?? 'bg-slate-400' }}"></span>
                <p class="text-sm text-slate-800">{{ $activity->description }}</p>
                @if ($activity->old_value && $activity->new_value)
                    <p class="text-xs text-slate-500">{{ $activity->old_value }} → {{ $activity->new_value }}</p>
                @endif
                <p class="mt-0.5 text-xs text-slate-400">
                    {{ $activity->user?->name ?? 'Sistem' }} · {{ $activity->created_at->format('d M Y, H:i') }}
                </p>
            </li>
        @empty
            <li class="text-sm text-slate-500">Belum ada aktivitas.</li>
        @endforelse
    </ol>
</x-card>
