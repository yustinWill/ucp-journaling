<?php

namespace App\Enums;

enum StockStatuses: string
{
    case AVAILABLE = 'AVAILABLE';
    case LOW_STOCK = 'LOW_STOCK';
    case OUT_OF_STOCK = 'OUT_OF_STOCK';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Stok Tersedia',
            self::LOW_STOCK => 'Stok Segera Habis',
            self::OUT_OF_STOCK => 'Stok Habis',
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
