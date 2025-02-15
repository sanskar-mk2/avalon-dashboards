<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\QueryBuilder\QueryBuilder;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $availableMonths = Sale::select('uploaded_for_month')
            ->distinct()
            ->orderBy('uploaded_for_month', 'desc')
            ->pluck('uploaded_for_month');

        $currentMonth = $request->get('month', $availableMonths->first());

        $query = QueryBuilder::for(Sale::class)
            ->select(
                'company',
                'location', 
                'order_no',
                'order_date',
                'customer_name',
                'invoice_no',
                'invoice_date',
                'item_no',
                'item_desc',
                'qty',
                'ext_sales',
                'ext_cost',
                'period',
                'requested_ship_date',
                'mfg_code'
            )
            ->allowedFilters(['location', 'period', 'salesperson', 'customer_name'])
            ->with('salespersonModel', 'locationModel')
            ->defaultSort('-uploaded_for_month');

        if (!$request->has('filter')) {
            $query->where('uploaded_for_month', $currentMonth);
        }

        $sales = $query->paginate(8)
            ->appends($request->query());

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'availableMonths' => $availableMonths,
            'currentMonth' => $currentMonth,
            'filters' => $request->query(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Sales/Create', [
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'replace' => 'boolean',
        ]);

        $path = $request->file('file')->store('temp');
        $uploaded_for_month = date('Y-m-d', strtotime($request->year . '-' . $request->month . '-01'));

        // If replace is true, delete existing records for the month
        if ($request->boolean('replace')) {
            Sale::where('uploaded_for_month', $uploaded_for_month)->delete();
        }

        // Set UTF-8 encoding for reading CSV
        $collection = (new FastExcel)
            ->configureCsv(',')
            ->import(storage_path('app/private/' . $path));

        // Split collection into chunks of 1000 records and save to DB
        $chunks = $collection->chunk(1000);
        foreach ($chunks as $chunk) {
            $records = $chunk->map(function ($row) use ($uploaded_for_month) {
                $fields = array_values($row);

                // Ensure all string values are UTF-8 encoded
                $sanitizeString = function ($value) {
                    if (is_string($value)) {
                        // Convert to UTF-8 if not already and remove invalid sequences
                        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }

                    return $value;
                };

                return [
                    'company' => $fields[0] ?? null,
                    'location' => $sanitizeString($fields[1] ?? null),
                    'order_no' => $fields[2] ?? null,
                    'backorder' => $fields[3] ?? null,
                    'order_date' => $fields[4] ?? null,
                    'order_type' => $sanitizeString($fields[5] ?? null),
                    'customer_no' => $sanitizeString($fields[6] ?? null),
                    'customer_name' => $sanitizeString($fields[7] ?? null),
                    'customer_class' => $sanitizeString($fields[8] ?? null),
                    'brand' => $sanitizeString($fields[9] ?? null),
                    'flag' => $sanitizeString($fields[10] ?? null),
                    'salesperson' => $fields[11] ?? null,
                    'invoice_no' => $fields[12] ?? null,
                    'invoice_date' => $fields[13] ? date('Y-m-d', strtotime($fields[13])) : null,
                    'item_no' => $sanitizeString($fields[14] ?? null),
                    'item_desc' => $sanitizeString($fields[15] ?? null),
                    'item_division' => $fields[16] ?? null,
                    'inv_class' => $fields[17] ?? null,
                    'qty' => $fields[18] ?? null,
                    'ext_sales' => $fields[19] ?? null,
                    'ext_cost' => $fields[20] ?? null,
                    'period' => $fields[21] ? date('Y-m-d', strtotime($fields[21] . '01')) : null,
                    'order_status' => $sanitizeString($fields[22] ?? null),
                    'advertising_source' => $sanitizeString($fields[23] ?? null),
                    'finance_co_rate' => $fields[24] ?? null,
                    'price_matrix' => $sanitizeString($fields[25] ?? null),
                    'price_list_applied' => $sanitizeString($fields[26] ?? null),
                    'price_after_disc' => $fields[27] ?? null,
                    'ship_to_no' => $sanitizeString($fields[28] ?? null),
                    'ship_to_name' => $sanitizeString($fields[29] ?? null),
                    'ship_to_city' => $sanitizeString($fields[30] ?? null),
                    'ship_to_state' => $sanitizeString($fields[31] ?? null),
                    'requested_ship_date' => $fields[32] ?? null,
                    'customer_desire_date' => $fields[33] ? date('Y-m-d', strtotime($fields[33])) : null,
                    'mfg_code' => $sanitizeString($fields[34] ?? null),
                    'uploaded_for_month' => $uploaded_for_month,
                ];
            })->toArray();

            \App\Models\Sale::insert($records);
        }

        // Delete temporary file
        unlink(storage_path('app/private/' . $path));

        return redirect()->route('sales.index')->with('success', 'Sales records imported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }

    public function deleteAll()
    {
        Sale::truncate();

        return back()->with('success', 'All sales records deleted successfully');
    }

    /**
     * Check if data exists for a given month.
     */
    public function checkExistingData(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        $uploaded_for_month = date('Y-m-d', strtotime($request->year . '-' . $request->month . '-01'));

        $exists = Sale::where('uploaded_for_month', $uploaded_for_month)->exists();

        return response()->json(['exists' => $exists]);
    }
}
