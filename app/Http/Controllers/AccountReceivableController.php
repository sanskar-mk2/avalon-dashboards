<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class AccountReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $account_receivables = AccountReceivable::paginate(8);

        return Inertia::render('AccountReceivables/Index', [
            'account_receivables' => $account_receivables,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AccountReceivables/Create');
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
                    'company_no' => $fields[0] ?? null,
                    'fiscal_period' => $fields[1] ? date('Y-m-d', strtotime($fields[1].'01')) : null,
                    'customer_no' => $sanitizeString($fields[2] ?? null),
                    'balance_due_amount' => $fields[3] ?? null,
                    'balance_age_1' => $fields[4] ?? null,
                    'balance_age_2' => $fields[5] ?? null,
                    'balance_age_3' => $fields[6] ?? null,
                    'balance_age_4' => $fields[7] ?? null,
                    'balance_age_5' => $fields[8] ?? null,
                    'balance_age_6' => $fields[9] ?? null,
                    'credit_manager' => $sanitizeString($fields[10] ?? null),
                    'location' => $sanitizeString($fields[11] ?? null),
                    'as_of_date' => $fields[12] ? date('Y-m-d', strtotime($fields[12])) : null,
                ];
            })->toArray();

            AccountReceivable::insert($records);
        }

        // Delete temporary file
        unlink(storage_path('app/private/'.$path));

        return redirect()->route('account_receivables.index')->with('success', 'Account receivables imported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountReceivable $accountReceivable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountReceivable $accountReceivable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountReceivable $accountReceivable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountReceivable $accountReceivable)
    {
        //
    }

    public function deleteAll()
    {
        AccountReceivable::truncate();

        return redirect()->route('account_receivables.index')->with('success', 'All account receivables deleted successfully');
    }
}
