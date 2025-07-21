<?php 

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use Exception;

class AppException extends Exception
{
    public function __construct(
        public readonly ErrorCodes $errorCode
    ) {
        parent::__construct($errorCode->message(), $errorCode->httpStatus());
    }

    public function getCodeValue(): string
    {
        return $this->errorCode->code();
    }
}