<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Evaluation;
use App\Models\Collateral;
use App\Models\GpsTracker;
use App\Models\CustomerVisit;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        // Fetch Customers with valid coordinates
        $customers = Customer::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['evaluations' => function($query) {
                $query->select('id', 'customer_id', 'application_id', 'user_id')->with('user:id,code,name');
            }])
            ->get(['id', 'name', 'address', 'village', 'district', 'regency', 'province', 'latitude', 'longitude', 'path_distance', 'photo_path', 'created_at', 'updated_at']);

        // Fetch Businesses (Evaluations) with valid business coordinates
        $businesses = Evaluation::whereNotNull('business_latitude')
            ->whereNotNull('business_longitude')
 ->with(['customer:id,name', 'user:id,code,name']) // Get customer name and AO info
            ->get(['id', 'customer_id', 'user_id', 'application_id', 'customer_entreprenuership_name', 'customer_entreprenuership_type', 'business_latitude', 'business_longitude', 'business_detail_1_path', 'business_province', 'business_regency', 'business_district', 'business_village', 'created_at', 'updated_at']);

        // Fetch Collaterals with valid coordinates
        $collaterals = Collateral::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['evaluation' => function($query) {
                $query->select('id', 'application_id', 'user_id')->with('user:id,code,name');
            }])
            ->get(['id', 'evaluation_id', 'type', 'owner_name', 'location_address', 'latitude', 'longitude', 'market_value', 'vehicle_image_1', 'property_image_1', 'image_proof', 'created_at', 'updated_at']);

        // Fetch active GPS Trackers assigned to users
        $gpsTrackers = GpsTracker::with('user:id,name,code')
            ->where('status', 'active')
            ->whereNotNull('user_id')
            ->get(['id', 'name', 'imei', 'user_id']);

        // Fetch Customer Visits
        $visits = CustomerVisit::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['customer:id,name', 'user:id,code,name'])
            ->get(['id', 'customer_id', 'user_id', 'address', 'village', 'district', 'regency', 'province', 'latitude', 'longitude', 'kolektibilitas', 'ketemu_dengan', 'kondisi_saat_ini', 'photo_path', 'created_at', 'updated_at']);

        return view('map.index', compact('customers', 'businesses', 'collaterals', 'gpsTrackers', 'visits'));
    }
}
