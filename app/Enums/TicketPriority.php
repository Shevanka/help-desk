<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function description(): string
    {
        return match ($this) {
            self::Low => 'Masalah ringan yang tidak mengganggu aktivitas utama.',
            self::Medium => 'Masalah yang mengganggu sebagian aktivitas.',
            self::High => 'Masalah yang menghambat aktivitas penting.',
            self::Critical => 'Masalah serius yang membutuhkan penanganan segera.',
        };
    }

    public function chartColor(): string
    {
        return match ($this) {
            self::Low => '#94a3b8',
            self::Medium => '#3b82f6',
            self::High => '#f97316',
            self::Critical => '#ef4444',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-100 text-slate-700 ring-slate-500/20',
            self::Medium => 'bg-blue-100 text-blue-800 ring-blue-600/20',
            self::High => 'bg-orange-100 text-orange-800 ring-orange-600/20',
            self::Critical => 'bg-red-100 text-red-800 ring-red-600/20',
        };
    }
}
