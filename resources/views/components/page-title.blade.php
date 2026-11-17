@props(['subtitle' => null])

<div class="min-w-0">
    <h1 class="truncate text-lg font-semibold text-slate-900">{{ $slot }}</h1>
    @if ($subtitle)
        <p class="truncate text-xs text-slate-500">{{ $subtitle }}</p>
    @endif
</div>
