<?php

namespace App\Enums;

enum CourseStatus: string
{
    case PUBLISHED = "published";
    case PENDING = "pending";

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->name] = $case->value;
        }
        return $options;
    }
}