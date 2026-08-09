<?php
namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class ActivateOfferController extends Controller
{
    public function offer_activation($id)
    {
        $offer = Offer::find($id);
        
        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found',
                'data' => null,
                'status' => 404
            ], 404);
        }

        try {
            $offer->is_active = !$offer->is_active;
            $offer->save();

            return response()->json([
                'success' => true,
                'message' => 'Offer status updated successfully',
                'data' => [
                    'is_active' => $offer->is_active
                ],
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating offer status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update offer status',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}