<?php

namespace App\Domains\User\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserService
{
    private $table_name = 'users';

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
                't.name',
                't.email',
                't.phone',
                't.f_tag_codes',
                't.last_login',
                't.status',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by',
                'r.id AS role_id',
                'r.name AS role_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            ])
            ->leftJoin('roles as r', 't.role_id', '=', 'r.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');;

        // Filter by status

        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        if ($email = request()->input('email')) {
            $query->where('t.email', 'like', "%{$email}%");
        }

        if ($role = request()->input('role')) {
            $query->where('r.id', $role);
        }

        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
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
