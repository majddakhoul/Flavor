<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Models\Maintenance;

class GetAllSumOfDiscountAndPriceAndTotalPriceOfAllMaintenancesController
{

    public function get_all_sum_of_discount_and_price_and_total_price_of_all_maintenances(){

        $AllMaintenances = Maintenance::all();

        if($AllMaintenances->isNotEmpty()){

            $sumOfTotalPrice = 0;
            $sumOfPrice = 0;
            $sumOfDiscount = 0;
            foreach($AllMaintenances as $AllMaintenance){

                $sumOfPrice = $sumOfPrice + $AllMaintenance->price;
                $sumOfDiscount = $sumOfDiscount + $AllMaintenance->discount;

            }
            $sumOfTotalPrice = $sumOfPrice - $sumOfDiscount;
            $Response['data'] = ['sum_of_price' => $sumOfPrice, 'sum_of_discount' => $sumOfDiscount ,'sum_of_total_price' => $sumOfTotalPrice];
            $Response['success'] = true;
            $Response['message'] = 'All maintenance bills have been calculated.';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }
        elseif($AllMaintenances->isEmpty()){

            $Response['data'] = ['sum_of_price' => 0 , 'sum_of_discount' => 0 ,'sum_of_total_price' => 0];
            $Response['success'] = true;
            $Response['message'] = 'There is nothing to display.';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }
        else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response,400);

        }

    }

}
