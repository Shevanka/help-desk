@props(['priority'])

<span {{ $attributes->class(['inline-flex items-center whitespace-nowrap rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset', $priority->badgeClasses()]) }}
      title="{{ $priority->description() }}">
    {{ $priority->label() }}
</span>
