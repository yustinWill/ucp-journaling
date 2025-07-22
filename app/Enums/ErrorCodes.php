<?php

namespace App\Enums;

enum ErrorCodes: string
{
    public const BAD_REQUEST = 400;
    public const INTERNAL_SERVER_ERROR = 500;
    public const UNAUTHORIZED = 401;

    case INVALID_LOGIN_CREDENTIALS = 'INVALID_LOGIN_CREDENTIALS';
    case FORM_VALIDATION_ERROR = 'FORM_VALIDATION_ERROR';
    case GENERAL_INTERNAL_SERVER_ERROR = 'GENERAL_INTERNAL_SERVER_ERROR';
    case NOT_AUTHORIZED = 'NOT_AUTHORIZED';

    public function message(): string
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => 'Invalid username or password.',
            self::FORM_VALIDATION_ERROR => 'Terjadi kesalahan pada server.',
            self::GENERAL_INTERNAL_SERVER_ERROR => 'Terjadi kesalahan pada server.',
            self::NOT_AUTHORIZED => 'Terjadi kesalahan pada server.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => self::BAD_REQUEST,
            self::FORM_VALIDATION_ERROR => self::BAD_REQUEST,
            self::GENERAL_INTERNAL_SERVER_ERROR => self::INTERNAL_SERVER_ERROR,
            self::NOT_AUTHORIZED => self::UNAUTHORIZED,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
