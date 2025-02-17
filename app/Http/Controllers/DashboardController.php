<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\Inventory;
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
        // Get available months from all tables
        $available_months = collect([
            Sale::distinct()->pluck('uploaded_for_month'),
            OpenOrder::distinct()->pluck('uploaded_for_month'),
            Inventory::distinct()->pluck('uploaded_for_month'),
            AccountReceivable::distinct()->pluck('uploaded_for_month'),
        ])->flatten()->unique()->sort()->values();

        // Get latest uploaded month from all tables if no month selected
        $default_month = max([
            Sale::max('uploaded_for_month'),
            OpenOrder::max('uploaded_for_month'),
            Inventory::max('uploaded_for_month'),
            AccountReceivable::max('uploaded_for_month'),
        ]);
        $current_month = $request->get('month') ?? $default_month;

        return Inertia::render('Dashboard', [
            'availableMonths' => $available_months,
            'currentMonth' => $current_month,
            'cards_data' => $this->getCardsData($current_month, $default_month),
            'location_chart_data' => $this->getLocationChartData($current_month),
            'top_sales_by_location' => $this->getTopSalesByLocation($current_month),
            'sales_by_salesperson' => $this->getSalesBySalesperson($current_month),
            'top_sales_by_salesperson' => $this->getTopSalesBySalesperson($current_month),
            'sales_by_customer' => $this->getSalesByCustomer($current_month),
            'top_sales_by_customer' => $this->getTopSalesByCustomer($current_month),
            'us_warehouse_inventory' => $this->getUSWarehouseInventory($current_month, $default_month),
        ]);
    }

    private function getCardsData($current_month, $default_month)
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
                    'total_amount' => Sale::whereYear('period', $current_year)
                        ->sum('ext_sales'),
                ],
                [
                    'period' => ($current_year - 1) . ' YTD',
                    'total_amount' => Sale::whereYear('period', $current_year - 1)
                        ->whereDate('period', '<=', date('Y-m-d', strtotime($end_date . ' -1 year')))
                        ->sum('ext_sales'),
                ],
            ]);
        } else {
            // Regular monthly sales data
            $sales = Sale::whereIn('period', [$current_month, $prev_month])
                ->groupBy('period')
                ->select('period', DB::raw('SUM(ext_sales) as total_amount'))
                ->orderBy('period', 'desc')
                ->get();
        }

        // Open Orders data
        $open_orders = OpenOrder::whereIn('uploaded_for_month', [$default_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select('uploaded_for_month as period', DB::raw('SUM(ext_sales) as total_amount'))
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        // Inventory data
        $total_inventory = Inventory::whereIn('uploaded_for_month', [$default_month, $prev_default_month])
            ->groupBy('uploaded_for_month')
            ->select(
                'uploaded_for_month as period',
                DB::raw('SUM(qty_on_hand * average_cost) as total_amount')
            )
            ->orderBy('uploaded_for_month', 'desc')
            ->get();

        // Account Receivables data
        $total_receivables = AccountReceivable::whereIn('uploaded_for_month', [$default_month, $prev_default_month])
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
            'total_inventory' => $total_inventory,
            'total_receivables' => $total_receivables,
        ];
    }

    private function getLocationChartData($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $location_data = $query->groupBy('location')
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

    private function getTopSalesByLocation($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->groupBy('location')
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

    private function getSalesBySalesperson($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_salesperson = $query->groupBy('salesperson')
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

    private function getTopSalesBySalesperson($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->groupBy('salesperson')
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

    private function getSalesByCustomer($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        $sales_by_customer = $query->groupBy('customer_name')
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

    private function getTopSalesByCustomer($current_month)
    {
        $query = Sale::query();

        if ($current_month === 'YTD') {
            $query->whereYear('period', date('Y'));
        } else {
            $query->where('period', $current_month);
        }

        return $query->groupBy('customer_name')
            ->select(
                'customer_name',
                DB::raw('SUM(ext_sales) as total_sales')
            )
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();
    }

    private function getUSWarehouseInventory($current_month, $default_month)
    {
        if ($current_month === 'YTD') {
            $current_month = $default_month;
        }
        // Get all months up to current month
        $months = Inventory::where('uploaded_for_month', '<=', $current_month)
            ->distinct()
            ->orderBy('uploaded_for_month', 'asc') // Changed to ascending order
            ->pluck('uploaded_for_month');

        $data = $months->map(function ($period) use ($months) {
            // Get inventory value for current period
            $inventory_value = Inventory::where('location', '10')
                ->where('uploaded_for_month', $period)
                ->where('qty_on_hand', '>', 0)
                ->sum(DB::raw('qty_on_hand * average_cost'));

            // Get inventory value for previous period if exists
            $prev_period = $months->where('uploaded_for_month', '<', $period)->first();
            $prev_inventory_value = 0;
            if ($prev_period) {
                $prev_inventory_value = Inventory::where('location', '10')
                    ->where('uploaded_for_month', $prev_period)
                    ->where('qty_on_hand', '>', 0)
                    ->sum(DB::raw('qty_on_hand * average_cost'));
            }

            // Calculate average inventory value
            $avg_inventory_value = ($inventory_value + $prev_inventory_value) / 2;

            // Get sales and COGS
            $sales_data = Sale::where('period', $period)
                ->where('location', '10')
                ->select(
                    DB::raw('SUM(ext_sales) as total_sales'),
                    DB::raw('SUM(ext_cost) as total_cogs')
                )
                ->first();

            // Calculate inventory turn using average inventory value
            $inventory_turn = $avg_inventory_value > 0
                ? ($sales_data->total_cogs / $avg_inventory_value) * 12
                : 0;

            return [
                'period' => $period,
                'inventory_value' => $inventory_value,
                'sales' => $sales_data->total_sales ?? 0,
                'cogs' => $sales_data->total_cogs ?? 0,
                'inventory_turn' => $inventory_turn,
            ];
        });

        return [
            'labels' => $months->values()->all(),
            'datasets' => [
                [
                    'label' => 'Inventory Value',
                    'data' => $data->pluck('inventory_value')->values()->all(),
                ],
                [
                    'label' => 'Sales',
                    'data' => $data->pluck('sales')->values()->all(),
                ],
                [
                    'label' => 'COGS',
                    'data' => $data->pluck('cogs')->values()->all(),
                ],
                [
                    'label' => 'Inventory Turn',
                    'data' => $data->pluck('inventory_turn')->values()->all(),
                ],
            ],
        ];
    }
}
