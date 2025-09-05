<?php

namespace App\Enums;

enum ActionTypes: string
{
    public const CREATE = 'CREATE';

    public const UPDATE = 'UPDATE';

    public const DELETE = 'DELETE';
}
