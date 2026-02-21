<?php

namespace App\Domains\Role\Services;

use Illuminate\Support\Facades\DB;

class RoleService
{
    private $table_name = 'roles';

    public function findAll()
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.name',
            ])
            ->orderBy('t.name', 'ASC')
            ->get();
    }

    public function findOne(int $id)
    {
        $data = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.created_at',
                't.name',
                't.guard_name',
                DB::raw('COUNT(DISTINCT mhr.model_id) as user_count'),
                DB::raw('COUNT(DISTINCT rhp.permission_id) as permission_count'),
                't.updated_at',
            ])
            ->leftJoin('model_has_roles as mhr', function ($join) {
                $join->on('t.id', '=', 'mhr.role_id')
                    ->where('mhr.model_type', '=', DB::raw("'".'App\\Models\\User'."'"));
            })
            ->leftJoin('role_has_permissions as rhp', 't.id', '=', 'rhp.role_id')
            ->where('t.id', $id)
            ->groupBy('t.id')
            ->first();

        return $data;
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.created_at',
                't.name',
                DB::raw('COUNT(DISTINCT mhr.model_id) as user_count'),
                DB::raw('COUNT(DISTINCT rhp.permission_id) as permission_count'),
                't.updated_at',
            ])
            ->leftJoin('model_has_roles as mhr', function ($join) {
                $join->on('t.id', '=', 'mhr.role_id')
                    ->where('mhr.model_type', '=', DB::raw("'".'App\\Models\\User'."'"));
            })
            ->leftJoin('role_has_permissions as rhp', 't.id', '=', 'rhp.role_id');

        // Filter by name
        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        // Filter by user count
        $userCount = request()->input('user_count');
        if ($userCount == '0') {
            $query->having('user_count', '=', 0);
        } elseif ($userCount == '1-5') {
            $query->having('user_count', '>=', 1)
                ->having('user_count', '<=', 5);
        } elseif ($userCount == '6-10') {
            $query->having('user_count', '>=', 6)
                ->having('user_count', '<=', 10);
        } elseif ($userCount == '10+') {
            $query->having('user_count', '>=', 11);
        }

        $query->groupBy('t.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();

        $query->orderBy('t.name', 'ASC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $data)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'updated_at' => now(),
            ]);
    }
}
