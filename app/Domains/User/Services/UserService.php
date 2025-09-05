<?php

namespace App\Domains\User\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    private $table_name = 'users';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'],
            'role_id' => $data['role'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'password' => bcrypt($data['password']),
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'],
            'role_id' => $data['role'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];
        if (isset($data['password']) && $data['password'] !== '') {
            $updateData['password'] = bcrypt($data['password']);
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($updateData);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
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
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->join('m_user_companies as uc', 't.id', '=', 'uc.f_user_id')
            ->leftJoin('roles as r', 't.role_id', '=', 'r.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');

        // Filter by status
        $query->where('uc.f_company_code', session('user_company_code'));
        $query->where('uc.status', GeneralStatuses::ACTIVE);

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
        $recordsFiltered = $filteredQuery->get()->count();
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
        $data = DB::table($this->table_name.' as t')
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
                'r.name as role_name',
            ])
            ->join('m_user_companies as uc', 't.id', '=', 'uc.f_user_id')
            ->join('roles as r', 't.role_id', '=', 'r.id')
            ->where('t.id', $id)
            ->where('uc.f_company_code', session('user_company_code'))
            ->where('uc.status', GeneralStatuses::ACTIVE)
            ->first();

        $data->f_tag_codes = json_decode($data->f_tag_codes);

        return $data;
    }

    public function findAllBasedOnRole($roleId)
    {
        $data = DB::table($this->table_name.' as t')
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
                'r.name as role_name',
            ])
            ->join('m_user_companies as uc', 't.id', '=', 'uc.f_user_id')
            ->join('roles as r', 't.role_id', '=', 'r.id')
            ->where('t.role_id', $roleId)
            ->where('uc.f_company_code', session('user_company_code'))
            ->get();

        foreach ($data as $item) {
            $item->f_tag_codes = json_decode($item->f_tag_codes);
        }

        return $data;
    }

    public function findAll()
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.name',
            ])
            ->join('m_user_companies as uc', 't.id', '=', 'uc.f_user_id')
            ->where('uc.f_company_code', session('user_company_code'))
            ->orderBy('t.name', 'ASC')
            ->get();
    }
}
