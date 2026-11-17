<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    /** Hex colour for Chart.js, matching the badge hue. */
    public function chartColor(): string
    {
        return match ($this) {
            self::Open => '#0ea5e9',
            self::InProgress => '#f59e0b',
            self::Resolved => '#10b981',
            self::Closed => '#94a3b8',
        };
    }

    /** Tailwind classes for the status badge. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-sky-100 text-sky-800 ring-sky-600/20',
            self::InProgress => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            self::Resolved => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            self::Closed => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        };
    }
}
