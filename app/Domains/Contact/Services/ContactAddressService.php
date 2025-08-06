<?php

namespace App\Domains\Contact\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactAddressService
{
    private $table_name = 'm_contact_addresses';

    public function createMany(array $data)
    {
        foreach ($data as &$item) {
            $item['created_by'] = Auth::id();
            $item['created_at'] = now();
        }
        return DB::table($this->table_name)->insert($data);
    }
}
