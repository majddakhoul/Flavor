<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\TableData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\TableRequest;
use App\Models\Table;
use App\Services\Catalog\TableService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function __construct(private readonly TableService $tableService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Table::class);

        return view('manage.tables.index', [
            'tables' => $this->tableService->paginate(QueryOptions::fromRequest($request)),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Table::class);

        return view('manage.tables.create', $this->formData());
    }

    public function store(TableRequest $request): RedirectResponse
    {
        $this->authorize('create', Table::class);

        $this->tableService->create(TableData::fromArray($request->validated()));

        return $this->done('manage.tables.index', __('flash.tables.created'));
    }

    public function edit(Table $table): View
    {
        $this->authorize('update', $table);

        return view('manage.tables.edit', $this->formData() + ['table' => $table]);
    }

    public function update(TableRequest $request, Table $table): RedirectResponse
    {
        $this->authorize('update', $table);

        $this->tableService->update($table, TableData::fromArray($request->validated()));

        return $this->done('manage.tables.index', __('flash.tables.updated'));
    }

    public function destroy(Table $table): RedirectResponse
    {
        $this->authorize('delete', $table);

        $this->tableService->delete($table);

        return $this->done('manage.tables.index', __('flash.tables.deleted'));
    }

    protected function formData(): array
    {
        return ['locations' => \App\Enums\TableLocation::options()];
    }
}
