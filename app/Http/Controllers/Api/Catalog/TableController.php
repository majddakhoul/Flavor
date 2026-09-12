<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\TableResource;
use App\Repositories\Contracts\TableRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct(private readonly TableRepositoryInterface $tables)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->tables->paginate(QueryOptions::fromRequest($request));

        return $this->paginated($paginator, TableResource::class);
    }
}
