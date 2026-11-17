@props(['label', 'value', 'tone' => 'slate', 'href' => null, 'hint' => null])

@php
    $accent = [
        'slate' => 'bg-slate-500',
        'sky' => 'bg-sky-500',
        'amber' => 'bg-amber-500',
        'emerald' => 'bg-emerald-500',
        'indigo' => 'bg-indigo-500',
        'rose' => 'bg-rose-500',
        'zinc' => 'bg-zinc-400',
    ][$tone] ?? 'bg-slate-500';
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class(['relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm', 'transition hover:border-slate-300 hover:shadow' => $href]) }}>
    <span class="absolute inset-y-0 left-0 w-1 {{ $accent }}"></span>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</{{ $tag }}>
