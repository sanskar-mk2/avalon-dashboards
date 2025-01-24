<?php

namespace App\Http\Controllers;

use App\Models\Salesperson;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class SalespersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salespeople = Salesperson::paginate(8);
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
            'file' => 'required|file|mimes:csv,txt'
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
        unlink(storage_path('app/private/' . $path));

        return redirect()->route('salespeople.index')->with('success', 'Salespeople imported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salesperson $salesperson)
    {
        //
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
