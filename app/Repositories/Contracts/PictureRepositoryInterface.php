<?php

namespace App\Repositories\Contracts;

use App\Models\Picture;
use App\Support\QueryOptions;

interface PictureRepositoryInterface extends RepositoryInterface
{
    public function findByPath(string $path): ?Picture;
}
