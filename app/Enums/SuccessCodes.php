<?php

namespace App\Enums;

enum SuccessCodes: string
{
    public const OK = 200;

    case LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';
    case CREATE_TAG_SUCCESS = 'CREATE_TAG_SUCCESS';

    public function message(): string
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => 'Anda berhasil logout.',
            self::CREATE_TAG_SUCCESS => 'Tag berhasil dibuat.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => self::OK,
            self::CREATE_TAG_SUCCESS => self::OK,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
