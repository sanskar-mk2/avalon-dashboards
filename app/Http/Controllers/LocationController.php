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
            ->import(storage_path('app/private/'.$path));

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
        unlink(storage_path('app/private/'.$path));

        return redirect()->route('locations.index')->with('success', 'Locations imported successfully');
    }

    private function getCardsData($current_month, $default_month, $location)
    {
        // Get previous month
        $prev_month = date('Y-m-d', strtotime($current_month.'-1 month'));
        $prev_default_month = date('Y-m-d', strtotime($default_month.'-1 month'));

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
                    'period' => ($current_year - 1).' YTD',
                    'total_amount' => Sale::whereLocation($location)
                        ->whereYear('period', $current_year - 1)
                        ->whereDate('period', '<=', date('Y-m-d', strtotime($end_date.' -1 year')))
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

            // Ensure both months exist in collection
            if (! $sales->contains('period', $current_month)) {
                $sales->push(['period' => $current_month, 'total_amount' => 0]);
            }
            if (! $sales->contains('period', $prev_month)) {
                $sales->push(['period' => $prev_month, 'total_amount' => 0]);
            }

            // Sort to ensure current month is first, prev month second
            $sales = $sales->sortByDesc('period')->values();
        }

        // Open Orders data
        $open_orders = OpenOrder::whereLocation($location)
            ->whereIn('uploaded_for_month', [$current_month === 'YTD' ? $default_month : $current_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select('uploaded_for_month as period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        // Ensure both months exist in open orders
        if (! $open_orders->contains('period', $current_month === 'YTD' ? $default_month : $current_month)) {
            $open_orders->push(['period' => $current_month === 'YTD' ? $default_month : $current_month, 'total_amount' => 0]);
        }
        if (! $open_orders->contains('period', $prev_default_month)) {
            $open_orders->push(['period' => $prev_default_month, 'total_amount' => 0]);
        }

        // Sort to ensure current month is first, prev month second
        $open_orders = $open_orders->sortByDesc('period')->values();

        // Account Receivables data
        $total_receivables = AccountReceivable::whereLocation($location)
            ->whereIn('uploaded_for_month', [$current_month === 'YTD' ? $default_month : $current_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select(
                'uploaded_for_month as period',
                DB::raw('SUM(balance_due_amount) as total_amount')
            )
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        // Ensure both months exist in receivables
        if (! $total_receivables->contains('period', $current_month === 'YTD' ? $default_month : $current_month)) {
            $total_receivables->push(['period' => $current_month === 'YTD' ? $default_month : $current_month, 'total_amount' => 0]);
        }
        if (! $total_receivables->contains('period', $prev_default_month)) {
            $total_receivables->push(['period' => $prev_default_month, 'total_amount' => 0]);
        }

        // Sort to ensure current month is first, prev month second
        $total_receivables = $total_receivables->sortByDesc('period')->values();

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
            ->with('salespersonModel', 'locationModel')
            ->when($current_month === 'YTD', function ($query) {
                $query->whereYear('period', date('Y'));
            }, function ($query) use ($current_month) {
                $query->where('period', $current_month);
            })
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Locations/Show', [
            'location' => $location,
            'sales' => $sales,
            'cards_data' => $this->getCardsData($current_month, $default_month, $location->location),
            'availableMonths' => $available_months,
            'currentMonth' => $current_month,
            'sales_by_salesperson' => $this->getSalesBySalesperson($current_month, $location->location),
            'top_sales_by_salesperson' => $this->getTopSalesBySalesperson($current_month, $location->location),
            'sales_by_customer' => $this->getSalesByCustomer($current_month, $location->location),
            'top_sales_by_customer' => $this->getTopSalesByCustomer($current_month, $location->location),
        ]);
    }

    private function getSalesBySalesperson($current_month, $location)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_salesperson = $query->whereLocation($location)
            ->groupBy('salesperson')
            ->select(
                'salesperson',
                DB::raw('SUM(ext_sales) as total_amount')
            )
            ->with('salespersonModel')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->salespersonModel);
            });

        $salesperson_data = $sales_by_salesperson
            ->groupBy('salespersonModel.salesman_name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->salespersonModel->salesman_name,
                    'total_amount' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('total_amount')
            ->values();
        $salesperson_mapping = $sales_by_salesperson->pluck('salesperson', 'salespersonModel.salesman_name');

        $salesperson_names = $salesperson_data->pluck('name');

        return [
            'labels' => $salesperson_names->values()->all(),
            'datasets' => [
                [
                    'label' => $current_month === 'YTD' ? date('Y').' YTD' : $current_month,
                    'data' => $salesperson_data->pluck('total_amount')->values()->all(),
                ],
            ],
            'salesperson_mapping' => $salesperson_mapping,
        ];
    }

    private function getTopSalesBySalesperson($current_month, $location)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->whereLocation($location)
            ->groupBy('salesperson')
            ->select(
                'salesperson',
                DB::raw('SUM(ext_sales) as total_sales')
            )
            ->with(['salespersonModel' => function ($query) {
                $query->select('id', 'salesman_no', 'salesman_name');
            }])
            ->orderBy('total_sales', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->salespersonModel);
            })
            ->groupBy('salespersonModel.salesman_name')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();
    }

    private function getSalesByCustomer($current_month, $location)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_customer = $query->whereLocation($location)
            ->groupBy('customer_name')
            ->select(
                'customer_name',
                DB::raw('SUM(ext_sales) as total_amount')
            )
            ->orderBy('total_amount', 'desc')
            ->get();

        $customer_data = $sales_by_customer
            ->map(function ($sale) {
                return [
                    'name' => $sale->customer_name,
                    'total_amount' => $sale->total_amount,
                ];
            })
            ->take(10)
            ->values();

        $customer_names = $customer_data->pluck('name');

        return [
            'labels' => $customer_names->values()->all(),
            'datasets' => [
                [
                    'label' => $current_month === 'YTD' ? date('Y').' YTD' : $current_month,
                    'data' => $customer_data->pluck('total_amount')->values()->all(),
                ],
            ],
        ];
    }

    private function getTopSalesByCustomer($current_month, $location)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->whereLocation($location)
            ->groupBy('customer_name')
            ->select(
                'customer_name',
                DB::raw('SUM(ext_sales) as total_sales')
            )
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();
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
