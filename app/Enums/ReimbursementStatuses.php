<?php

namespace App\Enums;

enum ReimbursementStatuses: string
{
    public const PENDING = 'PENDING';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
}
