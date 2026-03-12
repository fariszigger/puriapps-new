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
        
        if (!auth()->user()->can('view all data') && $customer->user_id !== auth()->id()) {
            $hasVisited = \App\Models\CustomerVisit::where('customer_id', $customerId)
                            ->where('user_id', auth()->id())
                            ->exists();
            if (!$hasVisited) {
                abort(403);
            }
        }

        // 1. Fetch all visits for this customer (only primary records to avoid duplicates)
        $visits = CustomerVisit::where('customer_id', $customerId)
                    ->where('is_accompanying', false)
                    ->orderBy('created_at', 'asc')
                    ->get();

        // 2. Fetch all warning letters for this customer
        $letters = WarningLetter::where('customer_id', $customerId)
                    ->orderBy('letter_date', 'asc')
                    ->get();

        // 3. Combine and sort
        $history = collect();

        foreach ($visits as $visit) {
            $history->push([
                'type' => 'visit',
                'date' => $visit->created_at,
                'display_date' => $visit->created_at,
                'ao' => $visit->user->name ?? '-',
                'accompanying_names' => $visit->accompanying_names,
                'details' => $visit->notes ?? 'Kunjungan / Penagihan',
                'raw_data' => $visit
            ]);
        }

        foreach ($letters as $letter) {
            $history->push([
                'type' => 'letter',
                'title' => $letter->type_label,
                'date' => $letter->created_at,
                'display_date' => $letter->letter_date,
                'ao' => $letter->user->name ?? '-',
                'details' => $letter->notes ?? 'Batas Waktu: ' . formatIndonesianDate($letter->deadline_date),
                'raw_data' => $letter
            ]);
        }

        // Sort everything chronologically by date and time
        $history = $history->sortBy('date')->values();

        // 4. Calculate titles for visits only after sorting
        $visitCount = 1;
        $history = $history->map(function ($item) use (&$visitCount) {
            if ($item['type'] === 'visit') {
                if (!$item['raw_data']->is_accompanying) {
                    $item['title'] = 'Penagihan ' . $visitCount++;
                } else {
                    $item['title'] = 'Penagihan (Pendamping)';
                }
            }
            return $item;
        });

        return view('collection-history.print', compact('customer', 'history'));
    }
}
