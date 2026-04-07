<?php

namespace App\Domains\BudgetCategory\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetCategoryService
{
    private $table_name = 'm_budget_categories';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'category_name' => $data['category_name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'category_name' => $data['category_name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
    }

    public function findById($id)
    {
        $data = DB::table($this->table_name)
            ->where('id', $id)
            ->first();

        if ($data == null) {
            abort(404);
        }

        return $data;
    }

    public function findOne($id)
    {
        return $this->findById($id);
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'category_name', 'status')
            ->orderBy('category_name', 'ASC')
            ->get();
    }

    public function findAllActive()
    {
        return DB::table($this->table_name)
            ->where('status', 'active')
            ->select('id', 'category_name')
            ->orderBy('category_name', 'ASC')
            ->get();
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name)
            ->leftJoin('users', 'm_budget_categories.created_by', '=', 'users.id')
            ->select('m_budget_categories.*', 'users.name as created_by_name');

        // Status filter
        if ($status = request()->input('status')) {
            $query->where('m_budget_categories.status', $status);
        }

        // Category name filter
        if ($categoryName = request()->input('category_name')) {
            $query->where('m_budget_categories.category_name', 'like', "%{$categoryName}%");
        }

        // Global search filter (from DataTables)
        if ($search = request()->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('m_budget_categories.category_name', 'like', "%{$search}%")
                  ->orWhere('m_budget_categories.description', 'like', "%{$search}%");
            });
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('m_budget_categories.category_name', 'ASC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function delete($id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }
}
