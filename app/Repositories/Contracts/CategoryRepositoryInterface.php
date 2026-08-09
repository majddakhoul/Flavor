<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use App\Support\QueryOptions;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function tree(): \Illuminate\Database\Eloquent\Collection;

    public function optionsWithDepth(): array;
}
