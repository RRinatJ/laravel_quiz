<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case PLAYER = 2;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PLAYER => 'Player',
        };
    }
}
