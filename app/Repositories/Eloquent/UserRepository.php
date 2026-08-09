<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): Model
    {
        return new User();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', strtolower($email))->first();
    }

    public function staffPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()->staff()->applyOptions($options)->paginate($options->perPage)->withQueryString();
    }
}
