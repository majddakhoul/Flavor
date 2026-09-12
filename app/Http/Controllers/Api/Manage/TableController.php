<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\TableData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\TableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use App\Services\Catalog\TableService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct(private readonly TableService $tables)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Table::class);

        return $this->paginated($this->tables->paginate(QueryOptions::fromRequest($request)), TableResource::class);
    }

    public function store(TableRequest $request): JsonResponse
    {
        $this->authorize('create', Table::class);

        $table = $this->tables->create(TableData::fromArray($request->validated()));

        return $this->created(new TableResource($table), __('flash.tables.created'));
    }

    public function update(TableRequest $request, Table $table): JsonResponse
    {
        $this->authorize('update', $table);

        $table = $this->tables->update($table, TableData::fromArray($request->validated()));

        return $this->ok(new TableResource($table), __('flash.tables.updated'));
    }

    public function destroy(Table $table): JsonResponse
    {
        $this->authorize('delete', $table);

        $this->tables->delete($table);

        return $this->noContent(__('flash.tables.deleted'));
    }
}
