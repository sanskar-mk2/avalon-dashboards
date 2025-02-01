<?php

namespace App\Http\Controllers;

use App\Models\OpenOrder;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', [
            'cards_data' => $this->getCardsData(),
            'location_chart_data' => $this->getLocationChartData(),
            'top_sales_by_location' => $this->getTopSalesByLocation(),
            'sales_by_salesperson' => $this->getSalesBySalesperson(),
            'top_sales_by_salesperson' => $this->getTopSalesBySalesperson(),
            'sales_by_customer' => $this->getSalesByCustomer(),
            'top_sales_by_customer' => $this->getTopSalesByCustomer(),
        ]);
    }

    private function getCardsData()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        $sales = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('period')
            ->select('period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('period', 'desc')
            ->get();

        $lastTwoMonths_open_orders = OpenOrder::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        $open_orders = OpenOrder::whereIn('period', $lastTwoMonths_open_orders)
            ->groupBy('period')
            ->select('period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('period', 'desc')
            ->get();

        $active_locations = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('period')
            ->select(
                'period',
                DB::raw('COUNT(DISTINCT CASE WHEN location IS NOT NULL THEN location END) as count')
            )
            ->orderBy('period', 'desc')
            ->get();

        $active_salespeople = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('period')
            ->select(
                'period',
                DB::raw('COUNT(DISTINCT CASE WHEN salesperson IS NOT NULL THEN salesperson END) as count')
            )
            ->orderBy('period', 'desc')
            ->get();

        return [
            'sales' => $sales,
            'open_orders' => $open_orders,
            'active_locations' => $active_locations,
            'active_salespeople' => $active_salespeople,
        ];
    }

    private function getLocationChartData()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        $sales_by_location = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('location', 'period')
            ->select(
                'location',
                'period',
                DB::raw('SUM(ext_sales) as total_amount')
            )
            ->with('locationModel')
            ->orderBy('period', 'desc')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->locationModel);
            });

        $periods = $sales_by_location->pluck('period')->unique()->sortDesc();
        $latest_period = $periods->first();

        $location_data = $sales_by_location
            ->where('period', $latest_period)
            ->groupBy('locationModel.location_abbreviation')
            ->map(function ($group) {
                return [
                    'abbreviation' => $group->first()->locationModel->location_abbreviation,
                    'total_amount' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        $location_abbreviations = $location_data->pluck('abbreviation');

        return [
            'labels' => $location_abbreviations->values()->all(),
            'datasets' => $periods->map(function ($period) use ($sales_by_location, $location_abbreviations) {
                $period_data = $sales_by_location
                    ->where('period', $period)
                    ->groupBy('locationModel.location_abbreviation')
                    ->map(function ($group) {
                        return $group->sum('total_amount');
                    });

                return [
                    'label' => $period,
                    'data' => $location_abbreviations->map(function ($abbr) use ($period_data) {
                        return $period_data->get($abbr, 0);
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    private function getTopSalesByLocation()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        return Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('location')
            ->select(
                'location',
                DB::raw('MAX(ext_sales) as highest_sale')
            )
            ->with(['locationModel' => function ($query) {
                $query->select('id', 'location', 'location_abbreviation');
            }])
            ->orderBy('highest_sale', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->locationModel);
            })
            ->groupBy('locationModel.location_abbreviation')
            ->map(function ($group) {
                return $group->sortByDesc('highest_sale')->first();
            })
            ->values();
    }

    private function getSalesBySalesperson()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        $sales_by_salesperson = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('salesperson', 'period')
            ->select(
                'salesperson',
                'period',
                DB::raw('SUM(ext_sales) as total_amount')
            )
            ->with('salespersonModel')
            ->orderBy('period', 'desc')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->salespersonModel);
            });

        $periods = $sales_by_salesperson->pluck('period')->unique()->sortDesc();
        $latest_period = $periods->first();

        $salesperson_data = $sales_by_salesperson
            ->where('period', $latest_period)
            ->groupBy('salespersonModel.salesman_name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->salespersonModel->salesman_name,
                    'total_amount' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        $salesperson_names = $salesperson_data->pluck('name');

        return [
            'labels' => $salesperson_names->values()->all(),
            'datasets' => $periods->map(function ($period) use ($sales_by_salesperson, $salesperson_names) {
                $period_data = $sales_by_salesperson
                    ->where('period', $period)
                    ->groupBy('salespersonModel.salesman_name')
                    ->map(function ($group) {
                        return $group->sum('total_amount');
                    });

                return [
                    'label' => $period,
                    'data' => $salesperson_names->map(function ($name) use ($period_data) {
                        return $period_data->get($name, 0);
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    private function getTopSalesBySalesperson()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        return Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('salesperson')
            ->select(
                'salesperson',
                DB::raw('MAX(ext_sales) as highest_sale')
            )
            ->with(['salespersonModel' => function ($query) {
                $query->select('id', 'salesman_no', 'salesman_name');
            }])
            ->orderBy('highest_sale', 'desc')
            ->get()
            ->filter(function ($sale) {
                return ! is_null($sale->salespersonModel);
            })
            ->groupBy('salespersonModel.salesman_name')
            ->map(function ($group) {
                return $group->sortByDesc('highest_sale')->first();
            })
            ->values();
    }

    private function getSalesByCustomer()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        $sales_by_customer = Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('customer_name', 'period')
            ->select(
                'customer_name',
                'period',
                DB::raw('SUM(ext_sales) as total_amount')
            )
            ->orderBy('period', 'desc')
            ->orderBy('total_amount', 'desc')
            ->get();

        $periods = $sales_by_customer->pluck('period')->unique()->sortDesc();
        $latest_period = $periods->first();

        $customer_data = $sales_by_customer
            ->where('period', $latest_period)
            ->groupBy('customer_name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->customer_name,
                    'total_amount' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('total_amount')
            ->take(10)
            ->values();

        $customer_names = $customer_data->pluck('name');

        return [
            'labels' => $customer_names->values()->all(),
            'datasets' => $periods->map(function ($period) use ($sales_by_customer, $customer_names) {
                $period_data = $sales_by_customer
                    ->where('period', $period)
                    ->groupBy('customer_name')
                    ->map(function ($group) {
                        return $group->sum('total_amount');
                    });

                return [
                    'label' => $period,
                    'data' => $customer_names->map(function ($name) use ($period_data) {
                        return $period_data->get($name, 0);
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    private function getTopSalesByCustomer()
    {
        $lastTwoMonths_sales = Sale::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->take(2)
            ->pluck('period');

        return Sale::whereIn('period', $lastTwoMonths_sales)
            ->groupBy('customer_name')
            ->select(
                'customer_name',
                DB::raw('MAX(ext_sales) as highest_sale')
            )
            ->orderBy('highest_sale', 'desc')
            ->take(10)
            ->get();
    }
}
