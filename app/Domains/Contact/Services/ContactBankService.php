<?php

namespace App\Domains\Contact\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactBankService
{
    private $table_name = 'm_contact_banks';

    public function createMany(array $data)
    {
        foreach ($data as &$item) {
            $item['created_by'] = Auth::id();
            $item['created_at'] = now();
        }
        return DB::table($this->table_name)->insert($data);
    }

    public function findAllBasedOnContactId($id)
    {
        return DB::table($this->table_name . ' AS t')
            ->select('f_bank_code', 'branch_office', 'account_holder', 'account_number', 'm_banks.name AS bank_name')
            ->join('m_banks', 'm_banks.code', '=', 't.f_bank_code')
            ->where('t.f_contact_id', $id)
            ->orderBy('t.id', 'asc')
            ->get();
    }
}
