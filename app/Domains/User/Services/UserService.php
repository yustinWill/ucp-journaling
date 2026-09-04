<?php

namespace App\Domains\User\Services;

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
            'password' => bcrypt($data['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'],
            'updated_at' => now(),
        ];
        if (isset($data['password']) && $data['password'] !== '') {
            $updateData['password'] = bcrypt($data['password']);
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($updateData);
    }

    public function updateLastLogin(int $id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update(['last_login' => now()]);
    }

    public function softDelete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'status' => 'INACTIVE',
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.name',
                't.email',
                't.phone',
                't.status',
                't.last_login',
                't.deleted_at',
                't.created_at',
                't.updated_at',
                't.status as account_status',
                DB::raw("COALESCE(r.name, 'N/A') as role_name"),
                DB::raw('COALESCE(r.id, 0) as role_id'),
            ])
            /** Spatie Laravel Permission joins */
            ->leftJoin('model_has_roles as mhr', function ($join) {
                $join->on('t.id', '=', 'mhr.model_id')
                    ->where('mhr.model_type', '=', \App\Models\User::class);
            })
            ->leftJoin('roles as r', 'mhr.role_id', '=', 'r.id')
            ->whereNull('t.deleted_at');

        // Filter by name
        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        // Filter by email
        if ($email = request()->input('email')) {
            $query->where('t.email', 'like', "%{$email}%");
        }

        // Filter by role
        if ($role = request()->input('role')) {
            $query->where('r.id', $role);
        }

        // Deleted users remain in the database but never appear in this list.
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->whereNull('deleted_at')->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->distinct()->count('t.id');
        $query->orderBy('t.name', 'ASC');
        $query->distinct('t.id');
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
        $user = DB::table($this->table_name)
            ->where('id', $id)
            ->first();

        if (! $user) {
            return null;
        }

        // Get user's role from Spatie
        $roleData = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $id)
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->select('roles.id', 'roles.name')
            ->first();

        $user->role_id = $roleData->id ?? null;
        $user->role_name = $roleData->name ?? null;

        return $user;
    }

    public function findAllBasedOnRole($roleId)
    {
        $data = DB::table($this->table_name.' as t')
            ->join('model_has_roles as mhr', function ($join) {
                $join->on('t.id', '=', 'mhr.model_id')
                    ->where('mhr.model_type', '=', DB::raw('"'.\App\Models\User::class.'"'));
            })
            ->join('roles as r', 'mhr.role_id', '=', 'r.id')
            ->select([
                't.id',
                't.name',
                't.email',
                't.phone',
                't.created_at',
                'r.id as role_id',
                'r.name as role_name',
            ])
            ->where('r.id', $roleId)
            ->whereNull('t.deleted_at')
            ->get();

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
            ->whereNull('t.deleted_at')
            ->where('t.status', 'ACTIVE')
            ->orderBy('t.name', 'ASC')
            ->get();
    }
}
