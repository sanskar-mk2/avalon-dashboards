<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sale;

class CustomerController extends Controller
{
    public function show($customer_name)
    {
        $sales = Sale::where('customer_name', $customer_name)
            ->with(['locationModel', 'salespersonModel'])
            ->paginate(8);

        return Inertia::render('Customers/Show', [
            'customer_name' => $customer_name,
            'sales' => $sales
        ]);
    }
} 