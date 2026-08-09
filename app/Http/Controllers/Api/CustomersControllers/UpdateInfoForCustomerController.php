<?php

namespace App\Http\Controllers\Api\CustomersControllers;

use App\Http\Requests\Api\CustomersRequests\UpdateInfoCustomerRequest;
use App\Models\Customer;
use App\Notifications\Auth\UserAccountUpdatedNotification;

class UpdateInfoForCustomerController
{

    public function update_info_for_customer(UpdateInfoCustomerRequest $updateInfoCustomerRequest)
    {

        $ValidatedData = $updateInfoCustomerRequest->validated();

        $User = auth()->user();

        $Customer = $User->customer;

        if (!$Customer) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No customer found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);

        }

        $UpdateData = [
            'favorite_categories' => $ValidatedData['favorite_categories'] ?? $Customer->favorite_categories,
            'allergies' => $ValidatedData['allergies'] ?? $Customer->allergies,
        ];

        $Updated = $Customer->update($UpdateData);
        $User = $Customer->user()->first();

        if ($Updated) {

            $Response['data'] = $Customer->fresh();
            $Response['success'] = true;
            $Response['message'] = 'Customer information updated successfully.';
            $Response['status'] = 200;

            $User->notify(new UserAccountUpdatedNotification($User));

            return response()->json($Response, 200);

        } else {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Failed to update your customer information.';
            $Response['status'] = 400;

            return response()->json($Response, 400);

        }
    }
}
