<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerVisit;
use App\Models\WarningLetter;
use Illuminate\Http\Request;

class CollectionHistoryController extends Controller
{
    public function index()
    {
        return view('collection-history.index');
    }

    public function print($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        // Ensure AO can only view their own customers
        if (!auth()->user()->can('view all data') && $customer->user_id !== auth()->id()) {
            abort(403);
        }

        // 1. Fetch all visits for this customer
        $visits = CustomerVisit::where('customer_id', $customerId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        // 2. Fetch all warning letters for this customer
        $letters = WarningLetter::where('customer_id', $customerId)
                    ->orderBy('letter_date', 'asc')
                    ->get();

        // 3. Combine and sort
        $history = collect();

        // We'll need to calculate which "Penagihan ke-X" each visit was
        $visitCount = 1;

        foreach ($visits as $visit) {
            $history->push([
                'type' => 'visit',
                'title' => 'Penagihan ' . $visitCount++,
                'date' => $visit->created_at, // Has date and time
                'display_date' => $visit->created_at,
                'ao' => $visit->user->name ?? '-',
                'details' => $visit->notes ?? 'Kunjungan / Penagihan',
                'raw_data' => $visit
            ]);
        }

        foreach ($letters as $letter) {
            // For letters, we use created_at as the primary sorting key to get the "hour" 
            // but we might want the letter_date as the displayed date.
            $history->push([
                'type' => 'letter',
                'title' => $letter->type_label,
                'date' => $letter->created_at, // Using created_at for chronological sorting with hours
                'display_date' => $letter->letter_date, // This is the official date
                'ao' => $letter->user->name ?? '-',
                'details' => $letter->notes ?? 'Batas Waktu: ' . formatIndonesianDate($letter->deadline_date),
                'raw_data' => $letter
            ]);
        }

        // Sort everything chronologically by date and time
        $history = $history->sortBy('date')->values();

        return view('collection-history.print', compact('customer', 'history'));
    }
}
