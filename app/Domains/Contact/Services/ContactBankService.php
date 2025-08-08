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

    public function update(array $data)
    {
        $data['updated_by'] = Auth::id();
        $data['updated_at'] = now();
        return DB::table($this->table_name)
            ->where('id', $data['id'])
            ->update($data);
    }

    public function delete($contact_id, $id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('f_contact_id', $contact_id)
            ->delete();
    }

    public function findAllBasedOnContactId($id)
    {
        return DB::table($this->table_name . ' AS t')
            ->select('t.id', 'f_bank_code', 'branch_office', 'account_holder', 'account_number', 'm_banks.name AS bank_name')
            ->join('m_banks', 'm_banks.code', '=', 't.f_bank_code')
            ->where('t.f_contact_id', $id)
            ->orderBy('t.id', 'asc')
            ->get();
    }
}
