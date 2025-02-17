<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\Location;
use App\Models\OpenOrder;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::withCount(['sales', 'openOrders'])->paginate(8);

        return Inertia::render('Locations/Index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Locations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->store('temp');

        // Set UTF-8 encoding for reading CSV
        $collection = (new FastExcel)
            ->configureCsv(',')
            ->import(storage_path('app/private/' . $path));

        // Split collection into chunks of 1000 records and save to DB
        $chunks = $collection->chunk(1000);
        foreach ($chunks as $chunk) {
            $records = $chunk->map(function ($row) {
                $fields = array_values($row);

                // Ensure all string values are UTF-8 encoded
                $sanitizeString = function ($value) {
                    if (is_string($value)) {
                        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }

                    return $value;
                };

                return [
                    'location' => $fields[0] ?? null,
                    'location_name' => $sanitizeString($fields[1] ?? null),
                    'location_abbreviation' => $sanitizeString($fields[2] ?? null),
                ];
            })->toArray();

            Location::insert($records);
        }

        // Delete temporary file
        unlink(storage_path('app/private/' . $path));

        return redirect()->route('locations.index')->with('success', 'Locations imported successfully');
    }

    private function getCardsData($current_month, $default_month, $location)
    {
        // Get previous month
        $prev_month = date('Y-m-d', strtotime($current_month . '-1 month'));
        $prev_default_month = date('Y-m-d', strtotime($default_month . '-1 month'));

        // Handle YTD for sales data
        if ($current_month === 'YTD') {
            $current_year = date('Y');
            $end_date = date('Y-m-d');

            // Sales data for YTD
            $sales = collect([
                [
                    'period' => 'YTD',
                    'total_amount' => Sale::whereLocation($location)
                        ->whereYear('period', $current_year)
                        ->sum('ext_sales'),
                ],
                [
                    'period' => ($current_year - 1) . ' YTD',
                    'total_amount' => Sale::whereLocation($location)
                        ->whereYear('period', $current_year - 1)
                        ->whereDate('period', '<=', date('Y-m-d', strtotime($end_date . ' -1 year')))
                        ->sum('ext_sales'),
                ],
            ]);
        } else {
            // Regular monthly sales data
            $sales = Sale::whereLocation($location)
                ->whereIn('period', [$current_month, $prev_month])
                ->groupBy('period')
                ->select('period', DB::raw('SUM(ext_sales) as total_amount'))
                ->orderBy('period', 'desc')
                ->get();
        }

        // Open Orders data
        $open_orders = OpenOrder::whereLocation($location)
            ->whereIn('uploaded_for_month', [$default_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select('uploaded_for_month as period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        // Account Receivables data
        $total_receivables = AccountReceivable::whereLocation($location)
            ->whereIn('uploaded_for_month', [$default_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select(
                'uploaded_for_month as period',
                DB::raw('SUM(balance_due_amount) as total_amount')
            )
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        return [
            'sales' => $sales,
            'open_orders' => $open_orders,
            'total_receivables' => $total_receivables,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {

        $sales = $location->sales()
            ->select(
                'company',
                'location',
                'order_no',
                'order_date',
                'customer_name',
                'salesperson',
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
            ->with('salespersonModel')
            ->paginate(8);
        // Get available months from all tables

        $available_months = collect([
            Sale::distinct()->pluck('uploaded_for_month'),
            OpenOrder::distinct()->pluck('uploaded_for_month'),
            AccountReceivable::distinct()->pluck('uploaded_for_month'),
        ])->flatten()->unique()->sort()->values();

        // Get latest uploaded month from all tables if no month selected
        $default_month = max([
            Sale::max('uploaded_for_month'),
            OpenOrder::max('uploaded_for_month'),
            AccountReceivable::max('uploaded_for_month'),
        ]);
        $current_month = request()->get('month') ?? $default_month;
        return Inertia::render('Locations/Show', [
            'location' => $location,
            'sales' => $sales,
            'cards_data' => $this->getCardsData($current_month, $default_month, $location->location),
            'availableMonths' => $available_months,
            'currentMonth' => $current_month,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }

    public function deleteAll()
    {
        Location::truncate();

        return back()->with('success', 'All Location records deleted successfully');
    }
}
