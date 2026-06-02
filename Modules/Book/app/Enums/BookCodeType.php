<?php

namespace Modules\Book\Enums;

enum BookCodeType: string
{
    case Student = 'student';
    case Teacher = 'teacher';

    public static function values(): array
    {
        return [
            self::Student->value,
            self::Teacher->value,
        ];
    }

    public function suffix(): string
    {
        return match ($this) {
            self::Student => 'S',
            self::Teacher => 'T',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Student => __('book::attributes.student'),
            self::Teacher => __('book::attributes.teacher'),
        };
    }
}
