<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class AppException extends Exception
{
    public function __construct(
        public readonly ErrorCodes $errorCode,
        string $message = ''
    ) {
        parent::__construct($message ?: $errorCode->message(), $errorCode->httpStatus());
    }

    public function getCodeValue(): string
    {
        return $this->errorCode->code();
    }
}
