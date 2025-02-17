<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\OpenOrder;
use App\Models\Sale;
use App\Models\Salesperson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class SalespersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salespeople = Salesperson::withCount(['sales', 'openOrders'])->paginate(8);

        return Inertia::render('Salespeople/Index', [
            'salespeople' => $salespeople,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Salespeople/Create', [
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
                        // Convert to UTF-8 if not already and remove invalid sequences
                        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }

                    return $value;
                };

                return [
                    'company_no' => $fields[0] ?? null,
                    'salesman_no' => $fields[1] ?? null,
                    'salesman_name' => $sanitizeString($fields[2] ?? null),
                    'as_of_date' => $fields[3] ? date('Y-m-d', strtotime($fields[3])) : null,
                ];
            })->toArray();

            \App\Models\Salesperson::insert($records);
        }

        // Delete temporary file
        unlink(storage_path('app/private/'.$path));

        return redirect()->route('salespeople.index')->with('success', 'Salespeople imported successfully');
    }

    private function getCardsData($current_month, $default_month, $salesperson)
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
                    'total_amount' => Sale::whereSalesperson($salesperson)
                        ->whereYear('period', $current_year)
                        ->sum('ext_sales'),
                ],
                [
                    'period' => ($current_year - 1).' YTD',
                    'total_amount' => Sale::whereSalesperson($salesperson)
                        ->whereYear('period', $current_year - 1)
                        ->whereDate('period', '<=', date('Y-m-d', strtotime($end_date.' -1 year')))
                        ->sum('ext_sales'),
                ],
            ]);
        } else {
            // Regular monthly sales data
            $sales = Sale::whereSalesperson($salesperson)
                ->whereIn('period', [$current_month, $prev_month])
                ->groupBy('period')
                ->select('period', DB::raw('SUM(ext_sales) as total_amount'))
                ->orderBy('period', 'desc')
                ->get();
        }

        // Open Orders data
        $open_orders = OpenOrder::whereSalesperson($salesperson)
            ->whereIn('uploaded_for_month', [$default_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select('uploaded_for_month as period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        return [
            'sales' => $sales,
            'open_orders' => $open_orders,
        ];
    }

    private function getLocationChartData($current_month, $salesperson)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $location_data = $query->whereSalesperson($salesperson)
            ->groupBy('location')
            ->select(
                'location',
                DB::raw('SUM(ext_sales) as total_sales'),
                DB::raw('SUM(ext_sales - ext_cost) as total_gp')
            )
            ->with('locationModel')
            ->orderBy('total_sales', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->locationModel);
            });

        $location_abbreviations = $location_data->pluck('locationModel.location_abbreviation');

        return [
            'sales_by_location' => [
                'labels' => $location_abbreviations->values()->all(),
                'datasets' => [
                    [
                        'label' => $current_month,
                        'data' => $location_data->pluck('total_sales')->values()->all(),
                    ],
                ],
            ],
            'gp_by_location' => [
                'labels' => $location_abbreviations->values()->all(),
                'datasets' => [
                    [
                        'label' => $current_month,
                        'data' => $location_data->pluck('total_gp')->values()->all(),
                    ],
                ],
            ],
            'location_abbrevation_mapping' => $location_data->pluck('locationModel.location', 'locationModel.location_abbreviation'),
        ];
    }

    private function getTopSalesByLocation($current_month, $salesperson)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->whereSalesperson($salesperson)
            ->groupBy('location')
            ->select(
                'location',
                DB::raw('SUM(ext_sales) as total_sales')
            )
            ->with(['locationModel' => function ($query) {
                $query->select('id', 'location', 'location_abbreviation');
            }])
            ->orderBy('total_sales', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->locationModel);
            })
            ->values();
    }

    private function getSalesByCustomer($current_month, $salesperson)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_customer = $query->whereSalesperson($salesperson)
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

    private function getTopSalesByCustomer($current_month, $salesperson)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->whereSalesperson($salesperson)
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
     * Display the specified resource.
     */
    public function show(Salesperson $salesperson)
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

        $sales = $salesperson->sales()
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
            ->with('locationModel')
            ->when($current_month === 'YTD', function ($query) {
                $query->whereYear('period', date('Y'));
            }, function ($query) use ($current_month) {
                $query->where('period', $current_month);
            })
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Salespeople/Show', [
            'salesperson' => $salesperson,
            'sales' => $sales,
            'cards_data' => $this->getCardsData($current_month, $default_month, $salesperson->salesman_no),
            'availableMonths' => $available_months,
            'currentMonth' => $current_month,
            'location_chart_data' => $this->getLocationChartData($current_month, $salesperson->salesman_no),
            'top_sales_by_location' => $this->getTopSalesByLocation($current_month, $salesperson->salesman_no),
            'sales_by_customer' => $this->getSalesByCustomer($current_month, $salesperson->salesman_no),
            'top_sales_by_customer' => $this->getTopSalesByCustomer($current_month, $salesperson->salesman_no),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salesperson $salesperson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salesperson $salesperson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salesperson $salesperson)
    {
        //
    }

    public function deleteAll()
    {
        Salesperson::truncate();

        return back()->with('success', 'All Salesperson records deleted successfully');
    }
}
