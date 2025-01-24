<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
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

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $sales = $location->sales()->with('salespersonModel')->paginate(10);

        return Inertia::render('Locations/Show', [
            'location' => $location,
            'sales' => $sales,
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
