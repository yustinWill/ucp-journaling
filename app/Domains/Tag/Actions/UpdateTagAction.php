<?php

namespace App\Domains\Tag\Actions;

use App\Domains\Tag\Services\TagService;

class UpdateTagAction
{
    public function execute(int $id,array $data)
    {
        return app(TagService::class)->update($id, $data);
    }
}