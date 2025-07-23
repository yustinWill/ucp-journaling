<?php

namespace App\Domains\SalesPerson\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesPersonService
{
    private $table_name = 'm_sales_persons';

    public function findAllDatatable()
    {
        // Mock data for now since contacts table may not exist yet
        $mockData = [
            [
                'id' => 1,
                'sales_person_code' => 'SP-001',
                'employee_name' => 'Dewi Kartika',
                'status' => 'ACTIVE',
                'commission_rate' => 5.00,
                'created_at' => '2025-01-20 10:00:00'
            ],
            [
                'id' => 2,
                'sales_person_code' => 'SP-002',
                'employee_name' => 'Andi Wijaya',
                'status' => 'ACTIVE',
                'commission_rate' => 7.50,
                'created_at' => '2025-01-21 11:00:00'
            ],
            [
                'id' => 3,
                'sales_person_code' => 'SP-003',
                'employee_name' => 'Budi Santoso',
                'status' => 'INACTIVE',
                'commission_rate' => 6.00,
                'created_at' => '2025-01-22 12:00:00'
            ]
        ];

        // Apply filters
        $filteredData = $mockData;
        
        if ($status = request()->input('status')) {
            $filteredData = array_filter($filteredData, function($item) use ($status) {
                return $item['status'] === $status;
            });
        }

        if ($name = request()->input('name')) {
            $filteredData = array_filter($filteredData, function($item) use ($name) {
                return stripos($item['employee_name'], $name) !== false;
            });
        }

        // DataTable pagination
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = count($mockData);
        $recordsFiltered = count($filteredData);
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        
        // Apply pagination
        $paginatedData = array_slice($filteredData, $start, $length);
        
        // Convert to objects for consistency
        $data = array_map(function($item) {
            return (object) $item;
        }, $paginatedData);

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function findOne($id)
    {
        // Mock data for now since contacts table may not exist yet
        $mockData = [
            1 => [
                'id' => 1,
                'code' => 'SP-001',
                'f_employee_contact_id' => 1,
                'employee_name' => 'Dewi Kartika',
                'status' => 'ACTIVE',
                'commission_rate' => 5.00,
                'description' => 'Sales person utama untuk wilayah Jakarta',
                'created_at' => '2025-01-20 10:00:00',
                'updated_at' => '2025-01-20 10:00:00'
            ],
            2 => [
                'id' => 2,
                'code' => 'SP-002',
                'f_employee_contact_id' => 2,
                'employee_name' => 'Andi Wijaya',
                'status' => 'ACTIVE',
                'commission_rate' => 7.50,
                'description' => 'Sales person untuk wilayah Bandung',
                'created_at' => '2025-01-21 11:00:00',
                'updated_at' => '2025-01-21 11:00:00'
            ],
            3 => [
                'id' => 3,
                'code' => 'SP-003',
                'f_employee_contact_id' => 3,
                'employee_name' => 'Budi Santoso',
                'status' => 'INACTIVE',
                'commission_rate' => 6.00,
                'description' => 'Sales person tidak aktif',
                'created_at' => '2025-01-22 12:00:00',
                'updated_at' => '2025-01-22 12:00:00'
            ]
        ];

        return isset($mockData[$id]) ? (object) $mockData[$id] : null;
    }

    public function findAll()
    {
        return DB::table($this->table_name . ' as sp')
            ->leftJoin('contacts as c', 'sp.f_employee_contact_id', '=', 'c.id')
            ->select(['sp.*', 'c.name as employee_name'])
            ->where('sp.status', 'ACTIVE')
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
        
        $data['code'] = 'SP-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return DB::table($this->table_name)->insertGetId([
            'code' => $data['code'],
            'f_employee_contact_id' => $data['f_employee_contact_id'],
            'status' => $data['status'],
            'commission_rate' => $data['commission_rate'] ?? 0.00,
            'description' => $data['description'] ?? '',
            'created_at' => now(),
            'created_by' => Auth::id()
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
                'updated_by' => Auth::id()
            ]);
    }

    public function delete($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }

    public function getAvailableEmployees($excludeSalesPersonId = null)
    {
        // Mock data for now since contacts table may not exist yet
        return [
            ['id' => 1, 'name' => 'DEWI001 - Dewi Kartika'],
            ['id' => 2, 'name' => 'ANDI002 - Andi Wijaya'],
            ['id' => 3, 'name' => 'BUDI003 - Budi Santoso'],
            ['id' => 4, 'name' => 'SARI004 - Sari Indah'],
            ['id' => 5, 'name' => 'TOMI005 - Tomi Rahman']
        ];
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