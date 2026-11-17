@props([
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => 'h-64',
    'indexAxis' => null,
    'legend' => null,
    'label' => 'Grafik',
])

@php
    $config = array_filter([
        'type' => $type,
        'labels' => array_values($labels),
        'datasets' => $datasets,
        'indexAxis' => $indexAxis,
        'legend' => $legend,
    ], fn ($v) => $v !== null);
    $empty = collect($datasets)->flatMap(fn ($d) => $d['data'])->sum() === 0;
@endphp

<div {{ $attributes->class(['relative', $height]) }}>
    @if ($empty)
        <div class="flex h-full items-center justify-center rounded-lg border border-dashed border-slate-200 text-sm text-slate-400">
            Belum ada data pada periode ini.
        </div>
    @else
        <canvas role="img" aria-label="{{ $label }}" data-chart='@json($config)'></canvas>
    @endif
</div>

{{-- Accessible text alternative of the same numbers --}}
@unless ($empty)
    <table class="sr-only">
        <caption>{{ $label }}</caption>
        <tr><th>Label</th>@foreach ($datasets as $d)<th>{{ $d['label'] ?? 'Jumlah' }}</th>@endforeach</tr>
        @foreach (array_values($labels) as $i => $l)
            <tr><td>{{ $l }}</td>@foreach ($datasets as $d)<td>{{ $d['data'][$i] ?? 0 }}</td>@endforeach</tr>
        @endforeach
    </table>
@endunless
