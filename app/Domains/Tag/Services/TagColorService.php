<?php

namespace App\Domains\Tag\Services;
use Illuminate\Support\Facades\DB;

class TagColorService
{
    private $table_name = 'm_tag_colors';
    
    public function findAll()
    {
        return DB::table($this->table_name)->orderBy('id', 'asc')->get();
    }

    public function findOne($id)
    {
        return DB::table($this->table_name)->where('id', $id)->first();
    }
}
