<?php

namespace App\Enums;

enum AuditLogTypes: string
{
    case MASTER_DATA_UNIT = 'MASTER_DATA_UNIT';
    case MASTER_DATA_CATEGORY = 'MASTER_DATA_CATEGORY';
    case MASTER_DATA_TAG = 'MASTER_DATA_TAG';
    case MASTER_DATA_SALES_PERSON = 'MASTER_DATA_SALES_PERSON';
    case MASTER_DATA_USER = 'MASTER_DATA_USER';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'Master Data Unit',
            self::MASTER_DATA_CATEGORY => 'Master Data Kategori',
            self::MASTER_DATA_TAG => 'Master Data Tag',
            self::MASTER_DATA_SALES_PERSON => 'Master Data Sales Person',
            self::MASTER_DATA_USER => 'Master Data Pengguna',

        };
    }

    public function code(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'MASTER_DATA_UNIT',
            self::MASTER_DATA_CATEGORY => 'MASTER_DATA_CATEGORY',
            self::MASTER_DATA_TAG => 'MASTER_DATA_TAG',
            self::MASTER_DATA_SALES_PERSON => 'MASTER_DATA_SALES_PERSON',
            self::MASTER_DATA_USER => 'MASTER_DATA_USER',
        };
    }
}
