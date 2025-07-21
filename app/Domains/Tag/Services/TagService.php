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
            'f_tsag_color_id' => $data['f_tag_color_id'],
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

    public function getAllTags()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code', 'status', 'f_tag_color', 'description')
            ->orderBy('name')
            ->get();
    }
}