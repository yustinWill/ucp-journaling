<?php

namespace App\Domains\Contact\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    private $table_name = 'm_contacts';
    // public function findAllDatatable()
    // {
    //     $query = DB::table('m_contacts as c')
    //         ->select([
    //             'c.id',
    //             'c.name as contact_name',
    //             'c.type as contact_type',
    //             'c.type as category_type',
    //             'c.status',
    //             'c.description',
    //             'c.created_at'
    //         ]);

    //     $query->where('c.company_code', session('user_company_code'));
    //     // Filter by status
    //     if ($status = request()->input('status_filter')) {
    //         $query->where('c.status', $status);
    //     }

    //     // Filter by category type
    //     if ($type = request()->input('category_type_filter')) { 
    //         $query->where('c.type', $type);
    //     }

    //     // Search functionality
    //     if ($search = request()->input('search.value')) {
    //         $query->where(function($q) use ($search) {
    //             $q->where('c.name', 'LIKE', "%{$search}%")
    //               ->orWhere('c.code', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     // DataTable pagination
    //     $draw = (int) request()->input('draw', 1);
    //     $recordsTotal = DB::table('m_categories')->count();
    //     $filteredQuery = clone $query;
    //     $start = (int) request()->input('start', 0);
    //     $length = (int) request()->input('length', 10);
    //     $query->skip($start)->take($length);
    //     $recordsFiltered = $filteredQuery->count();
    //     $query->orderBy('c.created_at', 'DESC');

    //     $data = $query->get()->map(function($row) {
    //         // Add product_count calculation
    //         $row->product_count = rand(0, 50); // TODO: Calculate actual product count
    //         return $row;
    //     });

    //     return [
    //         'draw' => $draw,
    //         'recordsTotal' => $recordsTotal,
    //         'recordsFiltered' => $recordsFiltered,
    //         'data' => $data
    //     ];
    // }

    // public function findOne($id)
    // {
    //     return DB::table($this->table_name)
    //         ->where('id', $id)
    //         ->where('company_code', session('user_company_code'))
    //         ->first();
    // }

    // public function findAll()
    // {
    //     return DB::table($this->table_name)
    //         ->where('status', 'ACTIVE')
    //         ->where('company_code', session('user_company_code'))
    //         ->get();
    // }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['contact_name'],
            'group_contact_name' => $data['group_name'] ?? null,
            'type' => $data['contact_type'],
            'status' => $data['contact_status'],
            'f_identity_type_code' => $data['identity_type'],
            'identity_number' => $data['identity_number'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'f_sales_pic_id' => $data['sales_user'],
            'company_name' => $data['company_name'],
            'company_npwp' => $data['npwp'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'company_fax_no' => $data['company_fax'] ?? null,
            'f_coa_account_payable' => $data['payable_account'],
            'f_coa_account_receivable' => $data['receivable_account'],
            'f_payment_terms_id' => $data['payment_term'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'created_at' => now(),
            'created_by' => Auth::id(),
            'company_code' => session('user_company_code'),
        ]);
    }

    // public function update(int $id, array $data): int
    // {
    //     return DB::table($this->table_name)
    //         ->where('id', $id)
    //         ->where('company_code', session('user_company_code'))
    //         ->update([
    //             'name' => $data['name'],
    //             'type' => $data['type'],
    //             'status' => $data['status'],
    //             'description' => $data['description'] ?? '',
    //             'updated_at' => now(),
    //             'updated_by' => Auth::id()
    //         ]);
    // }
}
