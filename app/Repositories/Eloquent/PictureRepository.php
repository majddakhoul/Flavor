<?php

namespace App\Repositories\Eloquent;

use App\Models\Picture;
use App\Repositories\Contracts\PictureRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class PictureRepository extends BaseRepository implements PictureRepositoryInterface
{
    protected function model(): Model
    {
        return new Picture();
    }

    public function findByPath(string $path): ?Picture
    {
        return $this->query()->where('path', $path)->first();
    }
}
