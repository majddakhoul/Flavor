<?php

namespace App\Http\Controllers\Api\Manage;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\People\CustomerService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customers)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        return $this->paginated(
            $this->customers->paginate(QueryOptions::fromRequest($request, ['user'])),
            CustomerResource::class
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        return $this->ok(new CustomerResource(
            $customer->load(['user.location', 'orders.meals', 'reservations.tables'])
        ));
    }

    public function ban(Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        $customer = $this->customers->ban($customer);

        return $this->ok(new CustomerResource($customer), __('flash.customers.banned'));
    }

    public function unban(Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        $customer = $this->customers->lift($customer);

        return $this->ok(new CustomerResource($customer), __('flash.customers.unbanned'));
    }
}
