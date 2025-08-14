<?php

namespace App\Domains\Unit\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\GeneralStatuses;

class UnitService
{
    private $table_name = 'm_units';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'f_unit_type_id' => $data['type'],
            'description' => $data['description'] ?? '',
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'f_unit_type_id' => $data['type'],
                'description' => $data['description'] ?? '',
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.code',
                't.name',
                't.status',
                't.description',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by',
                'ut.id AS unit_type_id',
                'ut.code AS unit_type_code',
                'ut.name AS unit_type_name',
            ])
            ->leftJoin('m_unit_types as ut', 't.f_unit_type_id', '=', 'ut.id');

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        if ($type = request()->input('type')) {
            $query->where('ut.id', $type);
        }
        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }
        $query->where('t.company_code', session('user_company_code'));
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('name', 'ASC');
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
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.status',
                'ut.id as unit_type_id',
                'ut.name as unit_type_name',
                'ut.code as unit_type_code',
                't.description',
                't.status',
                't.updated_at'
            ])
            ->leftJoin('m_unit_types as ut', 't.f_unit_type_id', '=', 'ut.id')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.id', $id)
            ->first();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('company_code', session('user_company_code'))
            ->orderBy('name', 'asc')
            ->get();
    }   
}
