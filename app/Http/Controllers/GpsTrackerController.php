<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GpsTracker;
use App\Models\User;

class GpsTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GpsTracker::with('user');
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('imei', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }
        
        $trackers = $query->latest()->paginate(10);
        return view('gps_trackers.index', compact('trackers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('gps_trackers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'imei' => 'required|string|max:255|unique:gps_trackers,imei',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        GpsTracker::create($validated);

        return redirect()->route('gps-trackers.index')
            ->with('success', 'GPS Tracker berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('gps-trackers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GpsTracker $gps_tracker)
    {
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('gps_trackers.edit', compact('gps_tracker', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GpsTracker $gps_tracker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'imei' => 'required|string|max:255|unique:gps_trackers,imei,' . $gps_tracker->id,
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $gps_tracker->update($validated);

        return redirect()->route('gps-trackers.index')
            ->with('success', 'GPS Tracker berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GpsTracker $gps_tracker)
    {
        $gps_tracker->delete();

        return redirect()->route('gps-trackers.index')
            ->with('success', 'GPS Tracker berhasil dihapus.');
    }
}
