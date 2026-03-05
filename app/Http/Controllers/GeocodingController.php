<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class GeocodingController extends Controller
{
    public function reverse(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        $lat = $request->lat;
        $lon = $request->lon;

        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'User-Agent' => 'PuriApps/1.0 (internal-tool)',
                'Referer' => config('app.url'),
            ])->get("https://nominatim.openstreetmap.org/reverse", [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lon,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json(['error' => 'Failed to fetch address from Nominatim'], $response->status());
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string',
        ]);

        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'User-Agent' => 'PuriApps/1.0 (internal-tool)',
                'Referer' => config('app.url'),
            ])->get("https://nominatim.openstreetmap.org/search", [
                'format' => 'json',
                'q' => $request->q,
                'addressdetails' => 1,
                'limit' => 5,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json(['error' => 'Failed to search address from Nominatim'], $response->status());
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
}
