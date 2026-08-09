<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Services\Catalog\OfferService;
use App\Support\QueryOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function __construct(private readonly OfferService $offers)
    {
    }

    public function index(Request $request): View
    {
        return view('site.offers.index', [
            'offers' => $this->offers->paginate(
                QueryOptions::fromRequest($request, defaultPerPage: config('flavor.pagination.menu'))
            ),
        ]);
    }

    public function show(Offer $offer): View
    {
        return view('site.offers.show', ['offer' => $this->offers->find($offer->id)]);
    }
}
