<?php

namespace App\Repositories\Eloquent;

use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRepository extends BaseRepository implements MaintenanceRepositoryInterface
{
    protected function model(): Model
    {
        return new Maintenance();
    }

    public function totals(): array
    {
        $row = $this->query()
            ->selectRaw('COALESCE(SUM(price), 0) as price, COALESCE(SUM(total_price), 0) as total_price, COALESCE(SUM(discount), 0) as discount, COUNT(*) as entries')
            ->first();

        return [
            'price' => (int) $row->price,
            'total_price' => (int) $row->total_price,
            'discount' => (int) $row->discount,
            'entries' => (int) $row->entries,
        ];
    }
}
