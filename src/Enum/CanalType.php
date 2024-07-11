<?php
namespace App\Enum;

enum CanalType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';

    public static function values(): array
    {
        return array_column(CanalType::cases(), 'value');
    }
}
