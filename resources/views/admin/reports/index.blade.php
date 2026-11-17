@php
    use App\Enums\TicketPriority;
    use App\Enums\TicketStatus;

    $range = ['from' => $from->toDateString(), 'to' => $to->toDateString()];
    $presets = [
        '7 hari' => [now()->subDays(6)->toDateString(), now()->toDateString()],
        '30 hari' => [now()->subDays(29)->toDateString(), now()->toDateString()],
        '90 hari' => [now()->subDays(89)->toDateString(), now()->toDateString()],
        'Tahun ini' => [now()->startOfYear()->toDateString(), now()->toDateString()],
    ];
    $statusCases = TicketStatus::cases();
    $priorityCases = TicketPriority::cases();
@endphp

<x-app-layout title="Reports">
    <x-slot name="header">
        <x-page-title :subtitle="$from->translatedFormat('d M Y').' – '.$to->translatedFormat('d M Y')">Laporan & Statistik</x-page-title>
    </x-slot>
    <x-slot name="actions">
        <x-button-link :href="route('admin.reports.export', $range)" variant="secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export CSV
        </x-button-link>
    </x-slot>

    {{-- Date filter --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="grid flex-1 grid-cols-2 gap-3 sm:max-w-md">
                <div>
                    <x-input-label for="from" value="Dari tanggal" />
                    <x-text-input id="from" name="from" type="date" class="mt-1 block w-full text-sm" :value="$range['from']" :max="now()->toDateString()" />
                </div>
                <div>
                    <x-input-label for="to" value="Sampai tanggal" />
                    <x-text-input id="to" name="to" type="date" class="mt-1 block w-full text-sm" :value="$range['to']" :max="now()->toDateString()" />
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Terapkan</button>
                @foreach ($presets as $label => [$pFrom, $pTo])
                    @php $active = $range['from'] === $pFrom && $range['to'] === $pTo; @endphp
                    <a href="{{ route('admin.reports.index', ['from' => $pFrom, 'to' => $pTo]) }}"
                       @class(['rounded-md px-3 py-1.5 text-sm font-medium', 'bg-indigo-50 text-indigo-700' => $active, 'text-slate-600 hover:bg-slate-100' => ! $active])>{{ $label }}</a>
                @endforeach
            </div>
        </form>
    </x-card>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Tiket Masuk" :value="$summary['total']" tone="indigo" hint="Dibuat pada periode ini" />
        <x-stat-card label="Tingkat Penyelesaian" :value="$summary['resolution_rate'].'%'" tone="emerald" hint="Resolved + Closed" />
        <x-stat-card label="Rata-rata Penyelesaian" :value="$summary['avg_hours'] !== null ? $summary['avg_hours'].' jam' : '—'" tone="amber" hint="Dari dibuat sampai Resolved" />
        <x-stat-card label="Belum Ditugaskan" :value="$summary['unassigned']" tone="rose" hint="Tanpa teknisi" />
    </div>

    {{-- Per period --}}
    <x-card :title="'Jumlah Tiket per Periode ('.$perPeriod['granularity'].')'" class="mt-6">
        <x-chart type="bar" height="h-72" :legend="true" label="Jumlah tiket per periode"
            :labels="$perPeriod['labels']"
            :datasets="[
                ['label' => 'Tiket dibuat', 'data' => $perPeriod['created'], 'backgroundColor' => '#6366f1', 'borderRadius' => 3, 'maxBarThickness' => 18],
                ['label' => 'Tiket diselesaikan', 'data' => $perPeriod['resolved'], 'backgroundColor' => '#10b981', 'borderRadius' => 3, 'maxBarThickness' => 18],
            ]" />
    </x-card>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-card title="Berdasarkan Status">
            <x-chart type="doughnut" label="Tiket berdasarkan status"
                :labels="array_map(fn ($s) => $s->label(), $statusCases)"
                :datasets="[[
                    'label' => 'Tiket',
                    'data' => array_values($byStatus),
                    'backgroundColor' => array_map(fn ($s) => $s->chartColor(), $statusCases),
                    'borderWidth' => 0,
                ]]" />
        </x-card>

        <x-card title="Berdasarkan Prioritas">
            <x-chart type="bar" label="Tiket berdasarkan prioritas"
                :labels="array_map(fn ($p) => $p->label(), $priorityCases)"
                :datasets="[[
                    'label' => 'Tiket',
                    'data' => array_values($byPriority),
                    'backgroundColor' => array_map(fn ($p) => $p->chartColor(), $priorityCases),
                    'borderRadius' => 6,
                    'maxBarThickness' => 48,
                ]]" />
        </x-card>
    </div>

    <x-card title="Berdasarkan Kategori" class="mt-6">
        <x-chart type="bar" index-axis="y" height="h-72" label="Tiket berdasarkan kategori"
            :labels="array_map(fn ($c) => $c->name.($c->is_active ? '' : ' (nonaktif)'), $byCategory)"
            :datasets="[[
                'label' => 'Tiket',
                'data' => array_map(fn ($c) => (int) $c->total, $byCategory),
                'backgroundColor' => '#6366f1',
                'borderRadius' => 6,
                'maxBarThickness' => 28,
            ]]" />
    </x-card>

    {{-- Per technician --}}
    <x-card title="Kinerja Teknisi" :padding="false" class="mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Teknisi</th>
                        <th class="px-4 py-3 text-right">Ditugaskan</th>
                        <th class="px-4 py-3 text-right">Aktif</th>
                        <th class="px-4 py-3 text-right">Diselesaikan</th>
                        <th class="hidden px-4 py-3 text-right sm:table-cell">Rata-rata Penyelesaian</th>
                        <th class="hidden w-1/4 px-4 py-3 md:table-cell">Progres</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($perTechnician as $tech)
                        @php $pct = $tech->assigned ? round($tech->resolved / $tech->assigned * 100) : 0; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                <a href="{{ route('tickets.index', ['technician' => $tech->id] + $range) }}" class="hover:text-indigo-600">{{ $tech->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $tech->assigned }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $tech->active }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $tech->resolved }}</td>
                            <td class="hidden px-4 py-3 text-right tabular-nums sm:table-cell">{{ $tech->avg_hours !== null ? $tech->avg_hours.' jam' : '—' }}</td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 flex-1 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-10 text-right text-xs tabular-nums text-slate-500">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada akun teknisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
