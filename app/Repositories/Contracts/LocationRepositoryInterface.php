<?php

namespace App\Repositories\Contracts;

use App\Models\Location;
use App\Support\QueryOptions;

interface LocationRepositoryInterface extends RepositoryInterface
{
    public function optionsLabelled(): array;
}
