<?php

namespace App\Domains\Contact\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactService
{
    private $table_name = 'm_contacts';

    public function findAllDatatable()
    {
        $query = DB::table('m_contacts as c')
            ->select([
                'c.id',
                'c.name as contact_name',
                'c.company_name as company_name',
                'c.email as email',
                'c.phone AS phone',
                'c.group_contact_name AS group_name',
                'c.is_customer',
                'c.is_supplier',
                'c.is_employee',
                'c.is_other',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])->leftJoin(DB::raw("JSON_TABLE(c.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('c.company_code', session('user_company_code'));

        // Filter by status
        if ($name = request()->input('contact_name')) {
            $query->where('c.name', 'LIKE', "%{$name}%");
        }

        if ($name = request()->input('company_name')) {
            $query->where('c.company_name', 'LIKE', "%{$name}%");
        }

        if ($email = request()->input('email')) {
            $query->where('c.email', 'LIKE', "%{$email}%");
        }

        if ($phone = request()->input('phone')) {
            $query->where('c.phone', 'LIKE', "%{$phone}%");
        }

        if ($groupName = request()->input('group_name')) {
            $query->where('c.group_contact_name', 'LIKE', "%{$groupName}%");
        }

        // Filter by contact type flags
        if ($type = request()->input('type')) {
            $query->where(function ($q) use ($type) {
                if (strpos($type, 'CUSTOMER') !== false) {
                    $q->orWhere('c.is_customer', true);
                }
                if (strpos($type, 'SUPPLIER') !== false) {
                    $q->orWhere('c.is_supplier', true);
                }
                if (strpos($type, 'EMPLOYEE') !== false) {
                    $q->orWhere('c.is_employee', true);
                }
                if (strpos($type, 'OTHERS') !== false) {
                    $q->orWhere('c.is_other', true);
                }
            });
        }

        $query->groupBy('c.id');

        // DataTable pagination
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('c.created_at', 'DESC');

        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOne($id)
    {
        return DB::table($this->table_name.' AS t')
            ->select([
                't.id',
                't.name',
                't.group_contact_name',
                't.is_customer',
                't.is_supplier',
                't.is_employee',
                't.is_other',
                't.status',
                't.f_identity_type_code',
                't.identity_number',
                't.email',
                't.phone',
                't.f_sales_pic_id',
                't.company_name',
                't.company_npwp',
                't.company_phone',
                't.company_fax_no',
                'f_coa_account_payable',
                'coa_payable.name AS coa_account_payable_name',
                'f_coa_account_receivable',
                'coa_receivable.name AS coa_account_receivable_name',
                'f_payment_terms_id',
                'payment_terms.name AS payment_terms_name',
            ])
            ->leftJoin('m_coas as coa_payable', 'coa_payable.code', '=', 'f_coa_account_payable')
            ->leftJoin('m_coas as coa_receivable', 'coa_receivable.code', '=', 'f_coa_account_receivable')
            ->leftJoin('m_payment_terms as payment_terms', 'payment_terms.id', '=', 'f_payment_terms_id')
            ->where('t.id', $id)
            ->where('t.company_code', session('user_company_code'))
            ->first();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('company_code', session('user_company_code'))
            ->get();
    }

    /**
     * Find contacts by type (legacy method - uses old 'type' column)
     * @deprecated Use findAllByFlag() instead
     */
    public function findAllByType($type)
    {
        return DB::table($this->table_name.' AS t')
            ->select(['t.id', 't.name', 't.type', 't.email', 'ca.address AS billing_address'])
            ->leftJoin('m_contact_addresses AS ca', function ($join) {
                $join->on('ca.f_contact_id', '=', 't.id')
                    ->where('ca.type', '=', 'BILLING'); // AND condition here
            })
            ->where('t.type', $type)
            ->where('t.status', GeneralStatuses::ACTIVE)
            ->where('t.company_code', session('user_company_code'))
            ->orderBy('t.name')
            ->get();
    }

    /**
     * Find contacts by boolean flag (e.g., 'customer', 'supplier', 'employee', 'other')
     */
    public function findAllByFlag(string $flag)
    {
        $flagColumn = 'is_'.strtolower($flag);

        return DB::table($this->table_name.' AS t')
            ->select(['t.id', 't.name', 't.email', 'ca.address AS billing_address'])
            ->leftJoin('m_contact_addresses AS ca', function ($join) {
                $join->on('ca.f_contact_id', '=', 't.id')
                    ->where('ca.type', '=', 'BILLING');
            })
            ->where($flagColumn, true)
            ->where('t.status', GeneralStatuses::ACTIVE)
            ->where('t.company_code', session('user_company_code'))
            ->orderBy('t.name')
            ->get();
    }

    public function create(array $data)
    {
        // Determine type based on boolean flags (supports multiple types)
        $isCustomer = isset($data['is_customer']) && $data['is_customer'] ? true : false;
        $isSupplier = isset($data['is_supplier']) && $data['is_supplier'] ? true : false;
        $isEmployee = isset($data['is_employee']) && $data['is_employee'] ? true : false;
        $isOther = isset($data['is_other']) && $data['is_other'] ? true : false;

        // Build type string - can be multiple comma-separated values
        $types = [];
        if ($isCustomer) $types[] = 'CUSTOMER';
        if ($isSupplier) $types[] = 'SUPPLIER';
        if ($isEmployee) $types[] = 'EMPLOYEE';
        if ($isOther) $types[] = 'OTHER';

        $type = !empty($types) ? implode(',', $types) : 'OTHER';

        return DB::table($this->table_name)->insertGetId([
            'name' => $data['contact_name'],
            'type' => $type,
            'group_contact_name' => $data['group_name'] ?? null,
            'is_customer' => $isCustomer,
            'is_supplier' => $isSupplier,
            'is_employee' => $isEmployee,
            'is_other' => $isOther,
            'status' => $data['contact_status'],
            'f_identity_type_code' => $data['identity_type'] ?? null,
            'identity_number' => $data['identity_number'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'f_sales_pic_id' => $data['sales_user'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'company_npwp' => $data['npwp'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'company_fax_no' => $data['company_fax'] ?? null,
            'f_coa_account_payable' => $data['payable_account'] ?? null,
            'f_coa_account_receivable' => $data['receivable_account'] ?? null,
            // 'f_payment_terms_id' => $data['payment_term'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'created_at' => now(),
            'created_by' => Auth::id(),
            'company_code' => session('user_company_code'),
        ]);
    }

    public function update(int $id, array $data): int
    {
        // Determine type based on boolean flags (supports multiple types)
        $isCustomer = isset($data['is_customer']) && $data['is_customer'] ? true : false;
        $isSupplier = isset($data['is_supplier']) && $data['is_supplier'] ? true : false;
        $isEmployee = isset($data['is_employee']) && $data['is_employee'] ? true : false;
        $isOther = isset($data['is_other']) && $data['is_other'] ? true : false;

        // Build type string - can be multiple comma-separated values
        $types = [];
        if ($isCustomer) $types[] = 'CUSTOMER';
        if ($isSupplier) $types[] = 'SUPPLIER';
        if ($isEmployee) $types[] = 'EMPLOYEE';
        if ($isOther) $types[] = 'OTHER';

        $type = !empty($types) ? implode(',', $types) : 'OTHER';

        return DB::table($this->table_name)->where('id', $id)->update([
            'name' => $data['contact_name'],
            'type' => $type,
            'group_contact_name' => $data['group_name'] ?? null,
            'is_customer' => $isCustomer,
            'is_supplier' => $isSupplier,
            'is_employee' => $isEmployee,
            'is_other' => $isOther,
            'status' => $data['contact_status'],
            'f_identity_type_code' => $data['identity_type'] ?? null,
            'identity_number' => $data['identity_number'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'f_sales_pic_id' => $data['sales_user'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'company_npwp' => $data['npwp'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'company_fax_no' => $data['company_fax'] ?? null,
            'f_coa_account_payable' => $data['payable_account'] ?? null,
            'f_coa_account_receivable' => $data['receivable_account'] ?? null,
            // 'f_payment_terms_id' => $data['payment_term'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);
    }
}
