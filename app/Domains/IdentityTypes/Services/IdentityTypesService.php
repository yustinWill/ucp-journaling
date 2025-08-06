<?php

namespace App\Domains\IdentityTypes\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class IdentityTypesService
{
    private $table_name = 'm_identity_types';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->orderBy('id', 'asc')
            ->get();
    }
}