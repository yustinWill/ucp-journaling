<?php

namespace App\Domains\Coa\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoaService
{
    private $table_name = 'm_coas';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'code' => $data['code'],
            'name' => $data['name'],
            'level' => $data['level'],
            'note' => $data['note'],
            'status' => $data['status'],
            'type' => $data['type'],
            'f_tag_codes' => json_encode($data['tags']),
            'current_balance' => 0,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        $updateData = [
            'name' => $data['name'],
            'level' => $data['level'],
            'note' => $data['note'],
            'status' => $data['status'],
            'type' => $data['type'],
            'updated_at' => now(),
            'updated_by' => Auth::id()
        ];
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($updateData);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.code',
                't.name',
                't.level',
                't.type',
                't.note',
                't.status',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            ])
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');

        // Filter by status

        if ($code = request()->input('code')) {
            $query->where('t.code', 'like', "%{$code}%");
        }

        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        if ($level = request()->input('level')) {
            $query->where('t.level', 'like', "%{$level}%");
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
        $query->orderBy('code', 'ASC');
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
        $data = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.role_id',
                't.name',
                't.email',
                't.phone',
                't.f_tag_codes as f_tag_codes',
                't.status as status',
                't.created_at as created_at',
                't.last_login',
                't.last_password_change',
                'r.name as role_name'
            ])
            ->join('roles as r', 't.role_id', '=', 'r.id')
            ->where('t.id', $id)
            ->first();

        $data->f_tag_codes = json_decode($data->f_tag_codes);
        return $data;
    }
}
