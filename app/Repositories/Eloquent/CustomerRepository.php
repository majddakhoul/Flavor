<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected function model(): Model
    {
        return new Customer();
    }

    public function forUser(int $userId): ?Customer
    {
        return $this->query()->with('user')->where('user_id', $userId)->first();
    }

    public function bannedCount(): int
    {
        return $this->query()->banned()->count();
    }
}
