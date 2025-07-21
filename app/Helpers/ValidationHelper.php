<?php 

namespace App\Helpers;

class ValidationHelper
{
    /**
     * Format validation errors into a single string with line breaks and left alignment.
     */
    public static function formatErrors(array $errors): string
    {
        return collect($errors)->flatten()->join("\n");
    }
}