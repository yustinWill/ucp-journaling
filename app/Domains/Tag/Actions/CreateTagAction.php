<?php

namespace App\Domains\Tag\Actions;

use App\Domains\Tag\Services\TagService;

class CreateTagAction
{
    public function execute(array $data)
    {
        return app(TagService::class)->createTag($data);
    }
}