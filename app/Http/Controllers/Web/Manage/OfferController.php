<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\OfferData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\OfferRequest;
use App\Models\Offer;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Services\Catalog\OfferService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function __construct(
        private readonly OfferService $offers,
        private readonly MealRepositoryInterface $meals,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Offer::class);

        return view('manage.offers.index', [
            'offers' => $this->offers->paginate(QueryOptions::fromRequest($request)),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Offer::class);

        return view('manage.offers.create', $this->formData());
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $this->authorize('create', Offer::class);

        $offer = $this->offers->create(OfferData::fromArray($request->validated()));

        return $this->done('manage.offers.edit', __('flash.offers.created'), $offer);
    }

    public function edit(Offer $offer): View
    {
        $this->authorize('update', $offer);

        return view('manage.offers.edit', $this->formData() + [
            'offer' => $offer->load(['meals.ingredients', 'translations']),
        ]);
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $this->offers->update($offer, OfferData::fromArray($request->validated()));

        return $this->done('manage.offers.edit', __('flash.offers.updated'), $offer);
    }

    public function toggle(Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $updated = $this->offers->toggle($offer);

        return $this->done(
            'manage.offers.index',
            $updated->is_active ? __('flash.offers.activated') : __('flash.offers.deactivated')
        );
    }

    public function detachMeal(Offer $offer, int $meal): RedirectResponse
    {
        $this->authorize('update', $offer);

        $this->offers->detachMeal($offer, $meal);

        return $this->done('manage.offers.edit', __('flash.offers.meal_removed'), $offer);
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $this->offers->delete($offer);

        return $this->done('manage.offers.index', __('flash.offers.deleted'));
    }

    protected function formData(): array
    {
        return [
            'meals' => $this->meals->query()->with('ingredients')->orderBy('name')->get(),
        ];
    }
}
