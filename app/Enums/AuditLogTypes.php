<?php

namespace App\Enums;

enum AuditLogTypes: string
{
    case MASTER_DATA_UNIT = 'MASTER_DATA_UNIT';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'Master Data Unit',
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'MASTER_DATA_UNIT',
        };
    }
}
