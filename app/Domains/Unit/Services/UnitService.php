<?php

namespace App\Domains\Unit\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UnitService
{
    private $table_name = 'm_units';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'symbol' => $data['symbol'],
            'f_unit_type_id' => $data['type'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'symbol' => $data['symbol'],
                'f_unit_type_id' => $data['type'],
                'description' => $data['description'] ?? null,
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
                't.symbol',
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
            $query->where('t.name', $name);
        }

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
                't.symbol',
                't.status',
                'ut.id as unit_type_id',
                'ut.name as unit_type_name',
                'ut.code as unit_type_code',
                't.description',
                't.status',
                't.updated_at'
            ])
            ->leftJoin('m_unit_types as ut', 't.f_unit_type_id', '=', 'ut.id')
            ->where('t.id', $id)
            ->first();
    }
}
