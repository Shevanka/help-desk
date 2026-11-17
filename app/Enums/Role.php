<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Technician = 'technician';
    case Requester = 'requester';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Technician => 'Technician',
            self::Requester => 'Requester',
        };
    }
}
