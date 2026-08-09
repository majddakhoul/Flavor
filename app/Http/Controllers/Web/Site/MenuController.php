<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\MealService;
use App\Support\QueryOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(
        private readonly MealService $meals,
        private readonly CategoryService $categories,
    ) {
    }

    public function index(Request $request): View
    {
        $options = QueryOptions::fromRequest($request, defaultPerPage: config('flavor.pagination.menu'));

        return view('site.menu.index', [
            'meals' => $this->meals->menu($options),
            'categories' => $this->categories->tree(),
            'options' => $options,
        ]);
    }

    public function show(Meal $meal): View
    {
        return view('site.menu.show', [
            'meal' => $this->meals->find($meal->id),
            'related' => $this->meals->featured(3),
        ]);
    }
}
