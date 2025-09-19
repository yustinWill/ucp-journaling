<?php

namespace App\Domains\SalesPerson\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesPersonService
{
    private $table_name = 'm_sales_persons';

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as sp')
            ->select([
                'sp.id',
                'sp.created_at as created_at',
                'sp.code as code',
                'c.name as employee_name',
                'sp.status AS status',
                'sp.commission_rate AS commission_rate',
            ])
            ->leftJoin('m_contacts as c', 'sp.f_employee_contact_id', '=', 'c.id')
            ->where('sp.company_code', session('user_company_code'));

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('sp.status', $status);
        }

        if ($name = request()->input('name')) {
            $query->where('c.name', 'LIKE', "%{$name}%");
        }

        // $query->groupBy('sp.id');

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

        // Mock data for now since contacts table may not exist yet
        // $mockData = [
        //     [
        //         'id' => 1,
        //         'sales_person_code' => 'SP-001',
        //         'employee_name' => 'Dewi Kartika',
        //         'status' => 'ACTIVE',
        //         'commission_rate' => 5.00,
        //         'created_at' => '2025-01-20 10:00:00',
        //     ],
        //     [
        //         'id' => 2,
        //         'sales_person_code' => 'SP-002',
        //         'employee_name' => 'Andi Wijaya',
        //         'status' => 'ACTIVE',
        //         'commission_rate' => 7.50,
        //         'created_at' => '2025-01-21 11:00:00',
        //     ],
        //     [
        //         'id' => 3,
        //         'sales_person_code' => 'SP-003',
        //         'employee_name' => 'Budi Santoso',
        //         'status' => 'INACTIVE',
        //         'commission_rate' => 6.00,
        //         'created_at' => '2025-01-22 12:00:00',
        //     ],
        // ];

        // // Apply filters
        // $filteredData = $mockData;

        // if ($status = request()->input('status')) {
        //     $filteredData = array_filter($filteredData, function ($item) use ($status) {
        //         return $item['status'] === $status;
        //     });
        // }

        // if ($name = request()->input('name')) {
        //     $filteredData = array_filter($filteredData, function ($item) use ($name) {
        //         return stripos($item['employee_name'], $name) !== false;
        //     });
        // }

        // // DataTable pagination
        // $draw = (int) request()->input('draw', 1);
        // $recordsTotal = count($mockData);
        // $recordsFiltered = count($filteredData);
        // $start = (int) request()->input('start', 0);
        // $length = (int) request()->input('length', 10);

        // // Apply pagination
        // $paginatedData = array_slice($filteredData, $start, $length);

        // // Convert to objects for consistency
        // $data = array_map(function ($item) {
        //     return (object) $item;
        // }, $paginatedData);

        // return [
        //     'draw' => $draw,
        //     'recordsTotal' => $recordsTotal,
        //     'recordsFiltered' => $recordsFiltered,
        //     'data' => $data,
        // ];
    }

    public function findOne($id)
    {
        return DB::table($this->table_name.' as sp')
            ->leftJoin('m_contacts as c', 'sp.f_employee_contact_id', '=', 'c.id')
            ->select(['sp.*', 'c.name as employee_name'])
            ->where('sp.id', $id)
            ->where('sp.company_code', session('user_company_code'))
            ->first();
    }

    public function findAll()
    {
        return DB::table($this->table_name.' as sp')
            ->join('m_contacts as c', 'sp.f_employee_contact_id', '=', 'c.id')
            ->select(['sp.*', 'c.name as employee_name'])
            ->where('sp.status', GeneralStatuses::ACTIVE)
            ->where('sp.company_code', session('user_company_code'))
            ->get();
    }

    public function create(array $data)
    {
        // Generate sales person code
        $lastCode = DB::table($this->table_name)
            ->where('code', 'LIKE', 'SP-%')
            ->orderBy('code', 'DESC')
            ->value('code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $data['code'] = 'SP-'.str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'code' => $data['code'],
            'f_employee_contact_id' => $data['f_employee_contact_id'],
            'status' => $data['status'],
            'commission_rate' => $data['commission_rate'] ?? 0.00,
            'description' => $data['description'] ?? '',
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'f_employee_contact_id' => $data['f_employee_contact_id'],
                'status' => $data['status'],
                'commission_rate' => $data['commission_rate'] ?? 0.00,
                'description' => $data['description'] ?? '',
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function delete($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }

    public function isEmployeeAvailable($employeeId, $excludeSalesPersonId = null)
    {
        $query = DB::table($this->table_name)
            ->where('f_employee_contact_id', $employeeId);

        if ($excludeSalesPersonId) {
            $query->where('id', '!=', $excludeSalesPersonId);
        }

        return $query->count() === 0;
    }
}
