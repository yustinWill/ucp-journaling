<?php

namespace App\Domains\Storage\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageService
{
    private $table_name = 'm_storages';

    public function findAll()
    {
        return DB::table($this->table_name . ' as m_storages')
            ->select('m_storages.id', 'm_storages.name', 'm_storages.code')
            ->where('m_storages.company_code', session('user_company_code'))
            ->orderBy('m_storages.name', 'asc')
            ->get();
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name . ' as m_storages')
            ->select([
                'm_storages.*',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            ])
            ->leftJoin(DB::raw("JSON_TABLE(m_storages.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('m_storages.id', $id)
            ->groupBy('m_storages.id')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags);
        }
        
        // Get PIC user names if exists
        if ($data && $data->f_pic_ids) {
            $pic_ids = json_decode($data->f_pic_ids);
            if (is_array($pic_ids) && count($pic_ids) > 0) {
                $pic_users = DB::table('users')
                    ->whereIn('id', $pic_ids)
                    ->select('id', 'name')
                    ->get();
                $data->pic_users = $pic_users;
            }
        }
        
        return $data;
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            "company_code" => session('user_company_code'),
            "code" => $data['code'],
            "name" => $data['name'],
            "f_pic_ids" => isset($data['pic_users']) ? json_encode($data['pic_users']) : null,
            "province" => $data['province'] ?? null,
            "city" => $data['city'] ?? null,
            "district" => $data['district'] ?? null,
            "sub_district" => $data['sub_district'] ?? null,
            "address" => $data['address'] ?? null,
            "postal_code" => $data['postal_code'] ?? null,
            "f_tag_codes" => isset($data['tags']) ? json_encode($data['tags']) : null,
            "status" => $data['status'],
            "created_by" => Auth::id(),
            "created_at" => now()
        ]);
    }

    public function update(int $id, array $data): int
    {
        $update_data = [
            "name" => $data['name'],
            "f_pic_ids" => isset($data['pic_users']) ? json_encode($data['pic_users']) : null,
            "province" => $data['province'] ?? null,
            "city" => $data['city'] ?? null,
            "district" => $data['district'] ?? null,
            "sub_district" => $data['sub_district'] ?? null,
            "address" => $data['address'] ?? null,
            "postal_code" => $data['postal_code'] ?? null,
            "f_tag_codes" => isset($data['tags']) ? json_encode($data['tags']) : null,
            "status" => $data['status'],
            "updated_by" => Auth::id(),
            "updated_at" => now()
        ];

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($update_data);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.code',
                't.name',
                't.address',
                't.status',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
                DB::raw("GROUP_CONCAT(users.name SEPARATOR ', ') as pic_names")
            ])
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_pic_ids, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as pic_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('users', 'pic_json.id', '=', 'users.id');

        // Filter by company
        $query->where('t.company_code', session('user_company_code'));

        // Apply filters
        if ($status_filter = request()->input('status_filter')) {
            $query->where('t.status', $status_filter);
        }

        if ($search = request()->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('t.name', 'like', "%{$search}%")
                  ->orWhere('t.code', 'like', "%{$search}%")
                  ->orWhere('t.address', 'like', "%{$search}%");
            });
        }

        $query->groupBy('t.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('t.id', 'ASC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}