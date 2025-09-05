<?php

namespace App\Domains\Contact\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class ContactTypeService
{
    private $table_name = 'm_contact_types';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->get();
    }
}
