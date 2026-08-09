<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\MealService;
use App\Services\Catalog\OfferService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly MealService $meals,
        private readonly OfferService $offers,
        private readonly CategoryService $categories,
    ) {
    }

    public function __invoke(): View
    {
        return view('site.home', [
            'featured' => $this->meals->featured(6),
            'offers' => $this->offers->running(3),
            'categories' => $this->categories->tree(),
        ]);
    }
}
