<?php

namespace App\Domains\Tag\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TagService
{
    private $table_name = 'm_tags';

    public function createTag(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'f_tag_color_id' => $data['f_tag_color_id'],
            'description' => $data['description'] ?? null,
            'memo' => $data['memo'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function updateTag(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'name' => $data['name'],
                'code' => $data['code'],
                'status' => $data['status'],
                'f_tag_color_id' => $data['f_tag_color_id'],
                'description' => $data['description'] ?? null,
                'memo' => $data['memo'] ?? null,
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);
    }

    public function getAllTagsDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.status',
                't.usage_count',
                't.description',
                't.memo',
                't.created_at',
                't.updated_at',
                DB::raw("CONCAT(tc.preview, ' | ', tc.name) as tag_color"),
                't.created_by',
                't.updated_by',
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id');

        // Search support
        if ($search = request()->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('t.name', 'like', "%{$search}%")
                    ->orWhere('t.code', 'like', "%{$search}%")
                    ->orWhere('t.status', 'like', "%{$search}%")
                    ->orWhere('tc.name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        // Filter by tag color
        if ($colorId = request()->input('f_tag_color_id')) {
            $query->where('t.f_tag_color_id', $colorId);
        }

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $recordsFiltered = $query->count();
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
