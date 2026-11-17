<x-app-layout title="Dashboard">
    <x-slot name="header">
        <x-page-title subtitle="Ringkasan seluruh tiket">Dashboard Administrator</x-page-title>
    </x-slot>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
        <x-stat-card label="Total Tiket" :value="$stats['total']" tone="indigo" :href="route('tickets.index')" />
        <x-stat-card label="Open" :value="$stats['open']" tone="sky" :href="route('tickets.index', ['status' => 'open'])" />
        <x-stat-card label="In Progress" :value="$stats['in_progress']" tone="amber" :href="route('tickets.index', ['status' => 'in_progress'])" />
        <x-stat-card label="Resolved" :value="$stats['resolved']" tone="emerald" :href="route('tickets.index', ['status' => 'resolved'])" />
        <x-stat-card label="Closed" :value="$stats['closed']" tone="zinc" :href="route('tickets.index', ['status' => 'closed'])" />
        <x-stat-card label="Teknisi" :value="$stats['technicians']" tone="slate" :href="route('admin.users.index', ['role' => 'technician'])" />
    </div>

    @if ($stats['unassigned'] > 0)
        <a href="{{ route('tickets.index', ['technician' => 'none']) }}"
           class="mt-4 flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 hover:bg-amber-100">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
            <span><strong>{{ $stats['unassigned'] }}</strong> tiket aktif belum ditugaskan ke teknisi. Klik untuk menugaskan.</span>
        </a>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <x-card title="Tiket Terbaru" :padding="false" class="xl:col-span-2">
            <x-slot name="headerActions">
                <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat semua</a>
            </x-slot>
            <x-ticket-table :tickets="$recent" />
        </x-card>

        <x-card title="Tiket per Kategori">
            @php $max = max(1, $categories->max('tickets_count')); @endphp
            <ul class="space-y-3">
                @foreach ($categories as $category)
                    <li>
                        <div class="flex items-center justify-between text-sm">
                            <span @class(['text-slate-700', 'line-through text-slate-400' => ! $category->is_active])>{{ $category->name }}</span>
                            <span class="font-medium tabular-nums text-slate-900">{{ $category->tickets_count }}</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ round($category->tickets_count / $max * 100) }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-app-layout>
