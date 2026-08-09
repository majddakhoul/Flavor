<?php

namespace App\Services\Catalog;

use App\DTOs\TableData;
use App\Exceptions\Domain\DomainException;
use App\Models\Table;
use App\Repositories\Contracts\TableRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TableService
{
    private const TAGS = ['floor', 'reservations', 'dashboard'];

    public function __construct(
        private readonly TableRepositoryInterface $tables,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->tables->paginate($options->withRelations(['translations']));
    }

    public function create(TableData $data): Table
    {
        $table = DB::transaction(function () use ($data) {
            $table = $this->tables->create($data->toArray());
            $table->syncTranslations($data->translations);

            return $table;
        });

        $this->cache->flush(self::TAGS);

        return $table;
    }

    public function update(Table $table, TableData $data): Table
    {
        DB::transaction(function () use ($table, $data) {
            $this->tables->update($table, $data->toArray());
            $table->syncTranslations($data->translations);
        });

        $this->cache->flush(self::TAGS);

        return $table->refresh();
    }

    public function delete(Table $table): void
    {
        if ($table->reservations()->exists()) {
            throw new DomainException(__('errors.table_has_reservations'), 422);
        }

        $this->tables->delete($table);
        $this->cache->flush(self::TAGS);
    }
}
