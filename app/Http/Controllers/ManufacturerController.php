<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\OpenOrder;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ManufacturerController extends Controller
{
    public function show($mfg_code)
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

        $sales = Sale::where('mfg_code', $mfg_code)
            ->select(
                'id',
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
            ->with(['locationModel', 'salespersonModel'])
            ->when($current_month === 'YTD', function ($query) {
                $query->whereYear('period', date('Y'));
            }, function ($query) use ($current_month) {
                $query->where('period', $current_month);
            })
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Manufacturers/Show', [
            'mfg_code' => $mfg_code,
            'sales' => $sales,
            'cards_data' => $this->getCardsData($current_month, $default_month, $mfg_code),
            'availableMonths' => $available_months,
            'currentMonth' => $current_month,
            'sales_by_location' => $this->getSalesByLocation($current_month, $mfg_code),
            'top_sales_by_location' => $this->getTopSalesByLocation($current_month, $mfg_code),
            'sales_by_salesperson' => $this->getSalesBySalesperson($current_month, $mfg_code),
            'top_sales_by_salesperson' => $this->getTopSalesBySalesperson($current_month, $mfg_code),
            'sales_by_customer' => $this->getSalesByCustomer($current_month, $mfg_code),
            'top_sales_by_customer' => $this->getTopSalesByCustomer($current_month, $mfg_code),
        ]);
    }

    private function getSalesByCustomer($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_customer = $query->where('mfg_code', $mfg_code)
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
                    'label' => $current_month === 'YTD' ? date('Y') . ' YTD' : $current_month,
                    'data' => $customer_data->pluck('total_amount')->values()->all(),
                ],
            ],
        ];
    }

    private function getTopSalesByCustomer($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->where('mfg_code', $mfg_code)
            ->groupBy('customer_name')
            ->select(
                'customer_name',
                DB::raw('SUM(ext_sales) as total_sales')
            )
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();
    }

    private function getCardsData($current_month, $default_month, $mfg_code)
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
                    'total_amount' => Sale::where('mfg_code', $mfg_code)
                        ->whereYear('period', $current_year)
                        ->sum('ext_sales'),
                ],
                [
                    'period' => ($current_year - 1) . ' YTD',
                    'total_amount' => Sale::where('mfg_code', $mfg_code)
                        ->whereYear('period', $current_year - 1)
                        ->whereDate('period', '<=', date('Y-m-d', strtotime($end_date . ' -1 year')))
                        ->sum('ext_sales'),
                ],
            ]);
        } else {
            // Regular monthly sales data
            $sales = Sale::where('mfg_code', $mfg_code)
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
        $open_orders = OpenOrder::where('mfg_code', $mfg_code)
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

        return [
            'sales' => $sales,
            'open_orders' => $open_orders,
        ];
    }

    private function getSalesByLocation($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $location_data = $query->where('mfg_code', $mfg_code)
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
                return ! is_null($sale->locationModel) && $sale->total_sales != 0;
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

    private function getTopSalesByLocation($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->where('mfg_code', $mfg_code)
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
                return ! is_null($sale->locationModel) && $sale->total_sales != 0;
            })
            ->values();
    }

    private function getSalesBySalesperson($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_salesperson = $query->where('mfg_code', $mfg_code)
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
                    'label' => $current_month === 'YTD' ? date('Y') . ' YTD' : $current_month,
                    'data' => $salesperson_data->pluck('total_amount')->values()->all(),
                ],
            ],
            'salesperson_mapping' => $salesperson_mapping,
        ];
    }

    private function getTopSalesBySalesperson($current_month, $mfg_code)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->where('mfg_code', $mfg_code)
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
            ->values();
    }
}
