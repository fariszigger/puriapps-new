<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function edit(\App\Models\Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, \App\Models\Customer $customer)
    {
        if (auth()->user()->cannot('update customers')) abort(403);

        $validatedData = $request->validate([
            // Identity
            'customer_type' => 'required', // Mapped to 'type'
            'name' => 'required|string|max:255',
            'no_id' => 'required|string|max:50', // Mapped to 'identity_number'
            'phone_number' => 'required|string|max:20',
            'job' => 'nullable|string|max:255',
            'pob' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'nullable|string', // Nullable if Badan
            'marrietal_status' => 'nullable|string', // Nullable if Badan

            // Family & Education
            'mother_name' => 'nullable|string|max:255',
            'education' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',

            // Address
            'address' => 'required|string',
            'village' => 'nullable|string',
            'district' => 'nullable|string',
            'regency' => 'nullable|string',
            'province' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            // Spouse (Nullable)
            'spouse_name' => 'nullable|string|max:255',
            'spouse_no_id' => 'nullable|string|max:50',
            'spouse_pob' => 'nullable|string|max:100',
            'spouse_dob' => 'nullable|date',
            'spouse_relation' => 'nullable|string',
            'spouse_description' => 'nullable|string',
            'spouse_job' => 'nullable|string|max:255',
            'spouse_education' => 'nullable|string',
            'spouse_notelp' => 'nullable|string|max:20',

            // Relation & Financing
            'location_image' => 'nullable|string', // Base64 image string

            // Files - Nullable on update
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'document' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        // Map Input Names to Database Columns
        $customerData = [
            'name' => $validatedData['name'],
            'type' => $validatedData['customer_type'],
            'identity_number' => $validatedData['no_id'],
            'phone_number' => $validatedData['phone_number'],
            'job' => $validatedData['job'] ?? null,
            'pob' => $validatedData['pob'],
            'dob' => $validatedData['dob'],
            'gender' => $validatedData['gender'] ?? null,
            'marital_status' => $validatedData['marrietal_status'] ?? null,
            'mother_name' => $validatedData['mother_name'] ?? null,
            'education' => $validatedData['education'] ?? null,
            'emergency_contact' => $validatedData['emergency_contact'] ?? null,

            'address' => $validatedData['address'],
            'village' => $validatedData['village'] ?? null,
            'district' => $validatedData['district'] ?? null,
            'regency' => $validatedData['regency'] ?? null,
            'province' => $validatedData['province'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,

            'spouse_name' => $validatedData['spouse_name'] ?? null,
            'spouse_identity_number' => $validatedData['spouse_no_id'] ?? null,
            'spouse_pob' => $validatedData['spouse_pob'] ?? null,
            'spouse_dob' => $validatedData['spouse_dob'] ?? null,
            'spouse_relation' => $validatedData['spouse_relation'] ?? null,
            'spouse_description' => $validatedData['spouse_description'] ?? null,
            'spouse_job' => $validatedData['spouse_job'] ?? null,
            'spouse_education' => $validatedData['spouse_education'] ?? null,
            'spouse_notelp' => $validatedData['spouse_notelp'] ?? null,

            'user_id' => $validatedData['user_id'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($customer->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($customer->photo_path);
            }
            $path = $request->file('photo')->store('customers/photos', 'local');
            $customerData['photo_path'] = $path;
        }

        if ($request->hasFile('document')) {
            // Delete old document
            if ($customer->document_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($customer->document_path);
            }
            $path = $request->file('document')->store('customers/documents', 'local');
            $customerData['document_path'] = $path;
        }

        // Handle Map Image (Base64)
        if (!empty($validatedData['location_image'])) {
            // Delete old location image
            if ($customer->location_image_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($customer->location_image_path);
            }
            $image = $validatedData['location_image'];
            // Remove the data URI scheme prefix
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'map_' . time() . '_' . uniqid() . '.png';
            $locationImagePath = 'customers/map/' . $imageName;
            \Illuminate\Support\Facades\Storage::disk('local')->put($locationImagePath, base64_decode($image));
            $customerData['location_image_path'] = $locationImagePath;
        } else if ($request->has('location_image') && $request->input('location_image') === '') {
            // If location_image is explicitly sent as empty, delete the old one
            if ($customer->location_image_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($customer->location_image_path);
            }
            $customerData['location_image_path'] = null;
        }


        // Calculate Path Distance
        if (isset($customerData['latitude']) && isset($customerData['longitude'])) {
            $officeLat = -7.487391381663846;
            $officeLon = 112.44006721604295;
            $customerData['path_distance'] = $this->calculateDistance($customerData['latitude'], $customerData['longitude'], $officeLat, $officeLon);
        }

        $customer->update($customerData);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function create()
    {
        if (auth()->user()->cannot('create customers')) abort(403);
        return view('customers.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->cannot('create customers')) abort(403);

        $validatedData = $request->validate([
            // Identity
            'customer_type' => 'required', // Mapped to 'type'
            'name' => 'required|string|max:255',
            'no_id' => 'required|string|max:50', // Mapped to 'identity_number'
            'phone_number' => 'required|string|max:20',
            'job' => 'nullable|string|max:255',
            'pob' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'nullable|string', // Nullable if Badan
            'marrietal_status' => 'nullable|string', // Nullable if Badan

            // Family & Education
            'mother_name' => 'nullable|string|max:255',
            'education' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',

            // Address
            'address' => 'required|string',
            'village' => 'nullable|string',
            'district' => 'nullable|string',
            'regency' => 'nullable|string',
            'province' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            // Spouse (Nullable)
            'spouse_name' => 'nullable|string|max:255',
            'spouse_no_id' => 'nullable|string|max:50',
            'spouse_pob' => 'nullable|string|max:100', // Restored field in blade
            'spouse_dob' => 'nullable|date',
            'spouse_relation' => 'nullable|string',
            'spouse_description' => 'nullable|string',
            'spouse_job' => 'nullable|string|max:255',
            'spouse_education' => 'nullable|string',
            'spouse_notelp' => 'nullable|string|max:20',

            // Relation & Financing
            'location_image' => 'nullable|string', // Base64 image string

            // Files
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'document' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        // Map Input Names to Database Columns
        $customerData = [
            'name' => $validatedData['name'],
            'type' => $validatedData['customer_type'],
            'identity_number' => $validatedData['no_id'],
            'phone_number' => $validatedData['phone_number'],
            'job' => $validatedData['job'] ?? null,
            'pob' => $validatedData['pob'],
            'dob' => $validatedData['dob'],
            'gender' => $validatedData['gender'] ?? null,
            'marital_status' => $validatedData['marrietal_status'] ?? null,
            'mother_name' => $validatedData['mother_name'] ?? null,
            'education' => $validatedData['education'] ?? null,
            'emergency_contact' => $validatedData['emergency_contact'] ?? null,

            'address' => $validatedData['address'],
            'village' => $validatedData['village'] ?? null,
            'district' => $validatedData['district'] ?? null,
            'regency' => $validatedData['regency'] ?? null,
            'province' => $validatedData['province'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,

            'spouse_name' => $validatedData['spouse_name'] ?? null,
            'spouse_identity_number' => $validatedData['spouse_no_id'] ?? null,
            'spouse_pob' => $validatedData['spouse_pob'] ?? null, // Now correctly mapped
            'spouse_dob' => $validatedData['spouse_dob'] ?? null,
            'spouse_relation' => $validatedData['spouse_relation'] ?? null,
            'spouse_description' => $validatedData['spouse_description'] ?? null,
            'spouse_job' => $validatedData['spouse_job'] ?? null,
            'spouse_education' => $validatedData['spouse_education'] ?? null,
            'spouse_notelp' => $validatedData['spouse_notelp'] ?? null,

            'user_id' => $validatedData['user_id'] ?? null,
        ];

        // Calculate Path Distance
        if (isset($customerData['latitude']) && isset($customerData['longitude'])) {
            $officeLat = -7.487391381663846;
            $officeLon = 112.44006721604295;
            $customerData['path_distance'] = $this->calculateDistance($customerData['latitude'], $customerData['longitude'], $officeLat, $officeLon);
        }

        $customer = new \App\Models\Customer($customerData);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('customers/photos', 'local');
            $customer->photo_path = $path;
        }

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('customers/documents', 'local');
            $customer->document_path = $path;
        }

        // Handle Map Image (Base64)
        if (!empty($validatedData['location_image'])) {
            $image = $validatedData['location_image'];
            // Remove the data URI scheme prefix
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'map_' . time() . '_' . uniqid() . '.png';
            $locationImagePath = 'customers/map/' . $imageName;
            \Illuminate\Support\Facades\Storage::disk('local')->put($locationImagePath, base64_decode($image));
            $customer->location_image_path = $locationImagePath;
        }

        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }
    public function print(\App\Models\Customer $customer)
    {
        return view('customers.print', compact('customer'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in km

        return $distance;
    }
}
