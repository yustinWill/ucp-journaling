<?php

namespace App\Domains\Role\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;

class RoleService
{
    private $table_name = 'roles';

    public function findAll()
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.description',
            ])
            ->orderBy('t.name', 'ASC')
            ->get();
    }

    public function findOne(int $id)
    {
        $data = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.created_at',
                't.name',
                't.description',
                DB::raw("COUNT(DISTINCT u.id) as user_count"),
                DB::raw("COUNT(DISTINCT rp.id) as permission_count"),
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
                't.is_active',
            ])
            ->leftJoin('users as u', function ($join) {
                $join->on('t.id', '=', 'u.role_id')
                    ->where('u.status', '=', GeneralStatuses::ACTIVE);
            })
            ->leftJoin('m_user_companies as uc', function ($join) {
                $join->on('u.id', '=', 'uc.f_user_id')
                    ->where('uc.status', '=', GeneralStatuses::ACTIVE)
                    ->where('uc.f_company_code', '=', session('user_company_code'));
            })
            ->leftJoin('role_permissions as rp', function ($join) {
                $join->on('t.id', '=', 'rp.role_id')
                    ->where('rp.status', '=', GeneralStatuses::ACTIVE)
                    ->where('rp.company_code', '=', session('user_company_code'));
            })
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')->where('t.id', $id)
            ->groupBy('t.id')
            ->first();
            
        foreach ($data as &$role) {
            if (!empty($role->tags)) {
                $tags = json_decode($role->tags, true);
                $uniqueTags = [];
                foreach ($tags as $tag) {
                    $key = $tag['code'] . '_' . $tag['preview'];
                    $uniqueTags[$key] = $tag;
                }
                $role->tags = json_encode(array_values($uniqueTags));
            }
        }
       
        return $data;
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.created_at',
                't.name',
                't.description',
                DB::raw("COUNT(DISTINCT u.id) as user_count"),
                DB::raw("COUNT(DISTINCT rp.id) as permission_count"),
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
                't.is_active',
            ])
            ->leftJoin('users as u', function ($join) {
                $join->on('t.id', '=', 'u.role_id')
                    ->where('u.status', '=', GeneralStatuses::ACTIVE);
            })
            ->leftJoin('m_user_companies as uc', function ($join) {
                $join->on('u.id', '=', 'uc.f_user_id')
                    ->where('uc.status', '=', GeneralStatuses::ACTIVE)
                    ->where('uc.f_company_code', '=', session('user_company_code'));
            })
            ->leftJoin('role_permissions as rp', function ($join) {
                $join->on('t.id', '=', 'rp.role_id')
                    ->where('rp.status', '=', GeneralStatuses::ACTIVE)
                    ->where('rp.company_code', '=', session('user_company_code'));
            })
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');


        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        if ($status = request()->input('status')) {
            if ($status === 'ACTIVE') {
                $query->where('t.is_active', true);
            } else {
                $query->where('t.is_active', false);
            }
        }

        $userCount = request()->input('user_count');
        if ($userCount == '0') {
            $query->having('user_count', '=', 0);
        } else if ($userCount >= '1' && $userCount <= '5') {
            $query->having('user_count', '>=', 1)
                ->having('user_count', '<=', 5);
        } else if ($userCount >= '6' && $userCount <= '10') {
            $query->having('user_count', '>=', 6)
                ->having('user_count', '<=', 10);
        } else if ($userCount >= '11') {
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
        $query->orderBy('name', 'ASC');
        $data = $query->get();

        foreach ($data as &$role) {
            if (!empty($role->tags)) {
                $tags = json_decode($role->tags, true);
                $uniqueTags = [];
                foreach ($tags as $tag) {
                    $key = $tag['code'] . '_' . $tag['preview'];
                    $uniqueTags[$key] = $tag;
                }
                $role->tags = json_encode(array_values($uniqueTags));
            }
        }

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
            'f_tag_codes' => json_encode($data['tags']),
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
        ]);
    }
}
