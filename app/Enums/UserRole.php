<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case PLAYER = 2;

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PLAYER => 'Player',
        };
    }
}
