<?php


namespace Modules\Admin\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'Super Admin';
    case MANAGER = 'Manager';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
