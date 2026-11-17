<?php

namespace App\Enums;

/**
 * The built-in roles. More roles can be added as data in the `roles` table;
 * these constants exist so code never compares against bare strings.
 */
enum RoleName: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Teacher => 'Teacher',
            self::Admin => 'Administrator',
        };
    }
}
