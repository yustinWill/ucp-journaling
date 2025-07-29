<?php

namespace App\Enums;

enum AuditLogTypes: string
{
    case MASTER_DATA_UNIT = 'MASTER_DATA_UNIT';
    case MASTER_DATA_CATEGORY = 'MASTER_DATA_CATEGORY';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'Master Data Unit',
            self::MASTER_DATA_CATEGORY => 'Master Data Kategori',

        };
    }

    public function code(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'MASTER_DATA_UNIT',
            self::MASTER_DATA_CATEGORY => 'MASTER_DATA_CATEGORY',
        };
    }
}
