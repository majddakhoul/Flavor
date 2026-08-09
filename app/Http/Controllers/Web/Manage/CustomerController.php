<?php

namespace App\Http\Controllers\Web\Manage;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\People\CustomerService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customers)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        return view('manage.customers.index', [
            'customers' => $this->customers->paginate(QueryOptions::fromRequest($request)),
        ]);
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        return view('manage.customers.show', [
            'customer' => $customer->load(['user.location', 'orders.meals', 'reservations.tables']),
        ]);
    }

    public function ban(Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        $this->customers->ban($customer);

        return $this->done('manage.customers.show', __('flash.customers.banned'), $customer);
    }

    public function unban(Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        $this->customers->lift($customer);

        return $this->done('manage.customers.show', __('flash.customers.unbanned'), $customer);
    }
}
