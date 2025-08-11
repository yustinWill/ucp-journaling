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
    case USER_NO_PERMISSIONS = 'USER_NO_PERMISSIONS';
    case USER_NO_COMPANY = 'USER_NO_COMPANY';
    case JOURNAL_IS_LOCKED = 'JOURNAL_IS_LOCKED';

    public function message(): string
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => 'Invalid username or password.',
            self::FORM_VALIDATION_ERROR => 'Terjadi kesalahan pada server.',
            self::GENERAL_INTERNAL_SERVER_ERROR => 'Terjadi kesalahan pada server.',
            self::NOT_AUTHORIZED => 'Terjadi kesalahan pada server.',
            self::USER_NO_PERMISSIONS => 'User tidak memiliki hak akses apapun.',
            self::USER_NO_COMPANY => 'User tidak memiliki perusahaan yang ditetapkan.',
            self::JOURNAL_IS_LOCKED => 'Jurnal ini sudah terkunci / dilakukan closing.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => self::BAD_REQUEST,
            self::FORM_VALIDATION_ERROR => self::BAD_REQUEST,
            self::GENERAL_INTERNAL_SERVER_ERROR => self::INTERNAL_SERVER_ERROR,
            self::NOT_AUTHORIZED => self::UNAUTHORIZED,
            self::USER_NO_PERMISSIONS => self::UNAUTHORIZED,
            self::USER_NO_COMPANY => self::UNAUTHORIZED,
            self::JOURNAL_IS_LOCKED => self::BAD_REQUEST,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
