<?php

namespace App\Enums;

enum ErrorCodes: string
{
    public const BAD_REQUEST = 400;
    public const INTERNAL_SERVER_ERROR = 500;

    case INVALID_LOGIN_CREDENTIALS = 'INVALID_LOGIN_CREDENTIALS';
    case FORM_VALIDATION_ERROR = 'FORM_VALIDATION_ERROR';

    public function message(): string
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => 'Invalid username or password.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::INVALID_LOGIN_CREDENTIALS => self::BAD_REQUEST,
            self::FORM_VALIDATION_ERROR => self::BAD_REQUEST,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
