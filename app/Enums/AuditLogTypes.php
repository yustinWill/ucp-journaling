<?php

namespace App\Enums;

enum AuditLogTypes: string
{
    case MASTER_DATA_UNIT = 'MASTER_DATA_UNIT';
    case MASTER_DATA_CATEGORY = 'MASTER_DATA_CATEGORY';
    case MASTER_DATA_TAG = 'MASTER_DATA_TAG';
    case MASTER_DATA_SALES_PERSON = 'MASTER_DATA_SALES_PERSON';
    case MASTER_DATA_USER = 'MASTER_DATA_USER';
    case MASTER_DATA_ROLE = 'MASTER_DATA_ROLE';
    case MASTER_DATA_CONTACT = 'MASTER_DATA_CONTACT';
    case MAIN_TRANSACTION_JOURNAL = 'MAIN_TRANSACTION_JOURNAL';
    case MASTER_DATA_PRODUCT = 'MASTER_DATA_PRODUCT';
    case MASTER_DATA_REVENUE_SPLIT = 'MASTER_DATA_REVENUE_SPLIT';
    case MASTER_DATA_INVENTORY_ASSET = 'MASTER_DATA_INVENTORY_ASSET';

    public function label(): string
    {
        return match ($this) {
            self::MASTER_DATA_UNIT => 'Master Data Unit',
            self::MASTER_DATA_CATEGORY => 'Master Data Kategori',
            self::MASTER_DATA_TAG => 'Master Data Tag',
            self::MASTER_DATA_SALES_PERSON => 'Master Data Sales Person',
            self::MASTER_DATA_USER => 'Master Data Pengguna',
            self::MASTER_DATA_ROLE => 'Master Data Role',
            self::MASTER_DATA_CONTACT => 'Master Data Kontak',
            self::MAIN_TRANSACTION_JOURNAL => 'Main Transaction Journal',
            self::MASTER_DATA_PRODUCT => 'Master Data Produk',
            self::MASTER_DATA_REVENUE_SPLIT => 'Master Data Keuntungan Penjualan',
            self::MASTER_DATA_INVENTORY_ASSET => 'Master Data Aset/Inventaris',
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
            self::MASTER_DATA_ROLE => 'MASTER_DATA_ROLE',
            self::MASTER_DATA_CONTACT => 'MASTER_DATA_CONTACT',
            self::MAIN_TRANSACTION_JOURNAL => 'MAIN_TRANSACTION_JOURNAL',
            self::MASTER_DATA_PRODUCT => 'MASTER_DATA_PRODUCT',
            self::MASTER_DATA_REVENUE_SPLIT => 'MASTER_DATA_REVENUE_SPLIT',
            self::MASTER_DATA_INVENTORY_ASSET => 'MASTER_DATA_INVENTORY_ASSET',
        };
    }
}
