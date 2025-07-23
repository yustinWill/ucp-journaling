<?php

namespace App\Enums;

enum SuccessCodes: string
{
    public const OK = 200;

    case GENERAL_SUCCESS = 'GENERAL_SUCCESS';
    case LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';
    case CREATE_TAG_SUCCESS = 'CREATE_TAG_SUCCESS';
    case CREATE_UNIT_SUCCESS = 'CREATE_UNIT_SUCCESS';
    case UPDATE_UNIT_SUCCESS = 'UPDATE_UNIT_SUCCESS';
    case CREATE_TAX_SUCCESS = 'CREATE_TAX_SUCCESS';
    case UPDATE_TAX_SUCCESS = 'UPDATE_TAX_SUCCESS';

    public function message(): string
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => 'Anda berhasil logout.',
            self::CREATE_TAG_SUCCESS => 'Tag berhasil dibuat.',
            self::CREATE_UNIT_SUCCESS => 'Unit berhasil dibuat.',
            self::UPDATE_UNIT_SUCCESS => 'Unit berhasil diubah.',
            self::CREATE_TAX_SUCCESS => 'Pajak berhasil dibuat.',
            self::UPDATE_TAX_SUCCESS => 'Pajak berhasil diubah.',
            self::GENERAL_SUCCESS => 'Berhasil',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => self::OK,
            self::CREATE_TAG_SUCCESS => self::OK,
            self::CREATE_UNIT_SUCCESS => self::OK,
            self::UPDATE_UNIT_SUCCESS => self::OK,
            self::CREATE_TAX_SUCCESS => self::OK,
            self::UPDATE_TAX_SUCCESS => self::OK,
            self::GENERAL_SUCCESS => self::OK,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
