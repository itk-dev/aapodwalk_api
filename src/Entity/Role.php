<?php

namespace App\Entity;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
    case USER_ADMIN = 'ROLE_USER_ADMIN';

    public static function asArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }

    public static function values(): array
    {
        return array_keys(static::asArray());
    }
}
