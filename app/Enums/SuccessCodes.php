<?php

namespace App\Enums;

enum SuccessCodes: string
{
    public const OK = 200;

    case LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';

    public function message(): string
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => 'Anda berhasil logout.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => self::OK,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
