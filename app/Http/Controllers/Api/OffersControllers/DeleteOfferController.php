<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class DeleteOfferController extends Controller
{
    public function delete_offer($id)
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
            $offer->meals()->detach();
            $offer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Offer deleted successfully',
                'status' => 200,
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting offer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete offer',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}