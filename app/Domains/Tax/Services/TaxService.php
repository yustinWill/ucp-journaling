<?php

namespace App\Domains\Tax\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaxService
{
    private $table_name = 'm_taxes';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['name'],
            'code' => $data['code'],
            'rate' => $data['rate'],
            'type' => $data['type'],
            'status' => $data['status'],
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
                'name' => $data['name'],
                'rate' => $data['rate'],
                'type' => $data['type'],
                'status' => $data['status'],
                'description' => $data['description'] ?? '',
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);
    }

    public function findAllDatatable(string $status = null, string $type = null, string $name = null)
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.code',
                't.name',
                't.rate',
                't.type',
                't.status',
                't.description',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by'
            ]);

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        // Filter by type
        if ($type = request()->input('type')) {
            $query->where('t.type', $type);
        }

        // Filter by name
        if ($name = request()->input('name')) {
            $query->where('t.name', 'LIKE', '%' . $name . '%');
        }

        // DataTable pagination
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function findOne($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id', 't.name', 't.code', 't.rate', 't.type', 't.status',
                't.description', 't.created_at', 't.updated_at'
            ])
            ->where('t.id', $id)
            ->first();
    }

    public function delete(int $id): int
    {
        return DB::table($this->table_name)->where('id', $id)->delete();
    }

    public function findAll()
    {
        return DB::table($this->table_name)->orderBy('name', 'asc')->get();
    }
}