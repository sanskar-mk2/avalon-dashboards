<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $availableMonths = Inventory::select('uploaded_for_month')
            ->distinct()
            ->orderBy('uploaded_for_month', 'desc')
            ->pluck('uploaded_for_month');

        $currentMonth = $request->get('month', $availableMonths->first());

        $inventories = Inventory::select(
            'fiscal_period',
            'location',
            'item_no',
            'qty_on_hand',
            'average_cost',
            'quantity_committed',
            'quantity_open_order',
            'quantity_backorder',
            DB::raw('qty_on_hand * average_cost as amount_on_hand')
        )
            ->when($currentMonth, function ($query) use ($currentMonth) {
                $query->where('uploaded_for_month', $currentMonth);
            })
            ->paginate(8);

        return Inertia::render('Inventories/Index', [
            'inventories' => $inventories,
            'availableMonths' => $availableMonths,
            'currentMonth' => $currentMonth,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Inventories/Create');
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
        $uploaded_for_month = date('Y-m-d', strtotime($request->year.'-'.$request->month.'-01'));

        // If replace is true, delete existing records for the month
        if ($request->boolean('replace')) {
            Inventory::where('uploaded_for_month', $uploaded_for_month)->delete();
        }

        // Set UTF-8 encoding for reading CSV
        $collection = (new FastExcel)
            ->configureCsv(',')
            ->import(storage_path('app/private/'.$path));

        // Split collection into chunks of 1000 records and save to DB
        $chunks = $collection->chunk(1000);
        foreach ($chunks as $chunk) {
            $records = $chunk->map(function ($row) use ($uploaded_for_month) {
                $fields = array_values($row);

                // Ensure all string values are UTF-8 encoded
                $sanitizeString = function ($value) {
                    if (is_string($value)) {
                        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }

                    return $value;
                };

                return [
                    'company_no' => $fields[0] ?? null,
                    'fiscal_period' => $fields[1] ? date('Y-m-d', strtotime($fields[1].'01')) : null,
                    'location' => $sanitizeString($fields[2] ?? null),
                    'item_no' => $sanitizeString($fields[3] ?? null),
                    'qty_on_hand' => $fields[4] ?? null,
                    'average_cost' => $fields[5] ?? null,
                    'quantity_committed' => $fields[6] ?? null,
                    'quantity_open_order' => $fields[7] ?? null,
                    'quantity_backorder' => $fields[8] ?? null,
                    'board_material' => $sanitizeString($fields[9] ?? null),
                    'board_thickness' => $sanitizeString($fields[10] ?? null),
                    'laminate_material' => $sanitizeString($fields[11] ?? null),
                    'laminate_color' => $sanitizeString($fields[12] ?? null),
                    'as_of_date' => $fields[13] ? date('Y-m-d', strtotime($fields[13])) : null,
                    'uploaded_for_month' => $uploaded_for_month,
                ];
            })->toArray();

            Inventory::insert($records);
        }

        // Delete temporary file
        unlink(storage_path('app/private/'.$path));

        return redirect()->route('inventories.index')->with('success', 'Inventories imported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        //
    }

    public function deleteAll()
    {
        Inventory::truncate();

        return redirect()->route('inventories.index')->with('success', 'All inventories deleted successfully');
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

        $uploaded_for_month = date('Y-m-d', strtotime($request->year.'-'.$request->month.'-01'));

        $exists = Inventory::where('uploaded_for_month', $uploaded_for_month)->exists();

        return response()->json(['exists' => $exists]);
    }
}
