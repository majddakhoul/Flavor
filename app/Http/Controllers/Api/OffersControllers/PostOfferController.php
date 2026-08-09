<?php
namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OffersRequests\PostOfferRequest;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class PostOfferController extends Controller
{
    public function add_offer(PostOfferRequest $request)
    {
        try {
            $data = $request->validated();

            $offer = Offer::create([
                'title'           => $data['title'],
                'description'     => $data['description'],
                'discount_amount' => $data['discount_amount'],
                'type'            => $data['type'],
                'is_active'      => true,
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Offer created successfully. Attach meals using the next endpoint.',
                'data' => $offer,
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            Log::error('Offer creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create offer',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}