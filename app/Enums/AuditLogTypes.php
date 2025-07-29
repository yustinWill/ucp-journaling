<?php

namespace App\Enums;

enum AuditLogTypes: string
{
    case MASTER_DATA_UNIT = 'MASTER_DATA_UNIT';
    case MASTER_DATA_CATEGORY = 'MASTER_DATA_CATEGORY';
    case MASTER_DATA_TAG = 'MASTER_DATA_TAG';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'Master Data Unit',
            self::MASTER_DATA_CATEGORY => 'Master Data Kategori',
            self::MASTER_DATA_TAG => 'Master Data Tag',

        };
    }

    public function code(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'MASTER_DATA_UNIT',
            self::MASTER_DATA_CATEGORY => 'MASTER_DATA_CATEGORY',
            self::MASTER_DATA_TAG => 'MASTER_DATA_TAG',
        };
    }
}
