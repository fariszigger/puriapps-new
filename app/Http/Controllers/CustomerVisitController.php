<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerVisitController extends Controller
{
    public function index()
    {
        return view('customer-visits.index');
    }

    public function create()
    {
        if (auth()->user()->cannot('create customer-visits')) abort(403);
        $customers = Customer::orderBy('id', 'desc')->get();
        $otherUsers = \App\Models\User::where('id', '!=', auth()->id())
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['AO', 'Kabag']);
            })
            ->get();
        return view('customer-visits.create', compact('customers', 'otherUsers'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->cannot('create customer-visits')) abort(403);
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'village' => 'nullable|string',
            'district' => 'nullable|string',
            'regency' => 'nullable|string',
            'province' => 'nullable|string',
            'kolektibilitas' => 'required|string',
            'baki_debet' => 'nullable|numeric',
            'ketemu_dengan' => 'required|string',
            'nama_orang_ditemui' => 'nullable|string|max:255',
            'kondisi_saat_ini' => 'nullable|string',
            'rencana_penyelesaian' => 'nullable|string',
            'hasil_penagihan' => 'nullable|string',
            'jumlah_bayar' => 'nullable|numeric',
            'tanggal_janji_bayar' => 'nullable|date',
            'jumlah_pembayaran' => 'nullable|numeric',
            'spk_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'photo_rumah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'photo_orang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'location_image' => 'nullable|string',
            'accompanying_aos' => 'nullable|array',
            'accompanying_aos.*' => 'exists:users,id',
        ]);

        // Auto-calculate penagihan_ke
        $pengihanKe = CustomerVisit::where('customer_id', $request->customer_id)->where('is_accompanying', false)->count() + 1;

        $visitData = [
            'customer_id' => $request->customer_id,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'village' => $request->village,
            'district' => $request->district,
            'regency' => $request->regency,
            'province' => $request->province,
            'kolektibilitas' => $request->kolektibilitas,
            'baki_debet' => in_array($request->kolektibilitas, ['3', '4', '5']) ? $request->baki_debet : null,
            'ketemu_dengan' => $request->ketemu_dengan,
            'nama_orang_ditemui' => $request->ketemu_dengan !== 'Debitur' ? $request->nama_orang_ditemui : null,
            'kondisi_saat_ini' => $request->kondisi_saat_ini,
            'rencana_penyelesaian' => $request->rencana_penyelesaian,
            'hasil_penagihan' => $request->hasil_penagihan,
            'jumlah_bayar' => $request->hasil_penagihan === 'bayar' ? $request->jumlah_bayar : null,
            'tanggal_janji_bayar' => $request->hasil_penagihan === 'janji_bayar' ? $request->tanggal_janji_bayar : null,
            'jumlah_pembayaran' => $request->hasil_penagihan === 'janji_bayar' ? $request->jumlah_pembayaran : null,
            'spk_number' => $request->spk_number,
            'penagihan_ke' => $pengihanKe,
        ];

        // Format accompanying names
        $accompanyingNames = null;
        if (!empty($request->accompanying_aos)) {
            $names = \App\Models\User::whereIn('id', $request->accompanying_aos)->pluck('name')->toArray();
            $accompanyingNames = implode(', ', $names);
        }
        $visitData['accompanying_names'] = $accompanyingNames;

        // Handle Photo Uploads
        if ($request->hasFile('photo')) {
            $visitData['photo_path'] = $request->file('photo')->store('customer-visits/photos', 'local');
        }
        if ($request->hasFile('photo_rumah')) {
            $visitData['photo_rumah_path'] = $request->file('photo_rumah')->store('customer-visits/photos', 'local');
        }
        if ($request->hasFile('photo_orang')) {
            $visitData['photo_orang_path'] = $request->file('photo_orang')->store('customer-visits/photos', 'local');
        }

        // Handle Map Image (Base64)
        if (!empty($request->location_image)) {
            $image = $request->location_image;
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'map_' . time() . '_' . uniqid() . '.png';
            $locationImagePath = 'customer-visits/map/' . $imageName;
            Storage::disk('local')->put($locationImagePath, base64_decode($image));
            $visitData['location_image_path'] = $locationImagePath;
        }

        // 1. Create the Main Visit Record
        $mainVisitData = $visitData;
        $mainVisitData['user_id'] = auth()->id();
        $mainVisitData['is_accompanying'] = false;
        CustomerVisit::create($mainVisitData);

        // 2. Loop through Accompanying AOs and duplicate
        if (!empty($request->accompanying_aos)) {
            foreach ($request->accompanying_aos as $aoId) {
                $duplicateData = $visitData;
                $duplicateData['user_id'] = $aoId;
                $duplicateData['is_accompanying'] = true;

                // Copy files explicitly for the duplicated record
                $copyImage = function($originalPath, $diskFolder) {
                    if ($originalPath && Storage::disk('local')->exists($originalPath)) {
                        $ext = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'png';
                        $newPath = $diskFolder . '/copy_' . time() . '_' . uniqid() . '.' . $ext;
                        Storage::disk('local')->copy($originalPath, $newPath);
                        return $newPath;
                    }
                    return null;
                };

                if (isset($duplicateData['photo_path'])) {
                    $duplicateData['photo_path'] = $copyImage($duplicateData['photo_path'], 'customer-visits/photos');
                }
                if (isset($duplicateData['photo_rumah_path'])) {
                    $duplicateData['photo_rumah_path'] = $copyImage($duplicateData['photo_rumah_path'], 'customer-visits/photos');
                }
                if (isset($duplicateData['photo_orang_path'])) {
                    $duplicateData['photo_orang_path'] = $copyImage($duplicateData['photo_orang_path'], 'customer-visits/photos');
                }
                if (isset($duplicateData['location_image_path'])) {
                    $duplicateData['location_image_path'] = $copyImage($duplicateData['location_image_path'], 'customer-visits/map');
                }

                CustomerVisit::create($duplicateData);
            }
        }

        return redirect()->route('customer-visits.index')->with('success', 'Kunjungan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (auth()->user()->cannot('update customer-visits')) abort(403);
        $visit = CustomerVisit::with('customer')->findOrFail($id);
        return view('customer-visits.edit', compact('visit'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->cannot('update customer-visits')) abort(403);
        $visit = CustomerVisit::findOrFail($id);

        $validated = $request->validate([
            'kolektibilitas' => 'required|string',
            'baki_debet' => 'nullable|numeric',
            'ketemu_dengan' => 'required|string',
            'nama_orang_ditemui' => 'nullable|string|max:255',
            'kondisi_saat_ini' => 'nullable|string',
            'rencana_penyelesaian' => 'nullable|string',
            'hasil_penagihan' => 'nullable|string',
            'jumlah_bayar' => 'nullable|numeric',
            'tanggal_janji_bayar' => 'nullable|date',
            'jumlah_pembayaran' => 'nullable|numeric',
            'spk_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'photo_rumah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'photo_orang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $visit->kolektibilitas = $request->kolektibilitas;
        $visit->baki_debet = in_array($request->kolektibilitas, ['3', '4', '5']) ? $request->baki_debet : null;
        $visit->ketemu_dengan = $request->ketemu_dengan;
        $visit->nama_orang_ditemui = $request->ketemu_dengan !== 'Debitur' ? $request->nama_orang_ditemui : null;
        $visit->kondisi_saat_ini = $request->kondisi_saat_ini;
        $visit->rencana_penyelesaian = $request->rencana_penyelesaian;
        $visit->hasil_penagihan = $request->hasil_penagihan;
        $visit->jumlah_bayar = $request->hasil_penagihan === 'bayar' ? $request->jumlah_bayar : null;
        $visit->tanggal_janji_bayar = $request->hasil_penagihan === 'janji_bayar' ? $request->tanggal_janji_bayar : null;
        $visit->jumlah_pembayaran = $request->hasil_penagihan === 'janji_bayar' ? $request->jumlah_pembayaran : null;
        $visit->spk_number = $request->spk_number;

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($visit->photo_path) {
                Storage::disk('local')->delete($visit->photo_path);
            }
            $path = $request->file('photo')->store('customer-visits/photos', 'local');
            $visit->photo_path = $path;
        }

        // Handle Photo Rumah Debitur
        if ($request->hasFile('photo_rumah')) {
            if ($visit->photo_rumah_path) {
                Storage::disk('local')->delete($visit->photo_rumah_path);
            }
            $path = $request->file('photo_rumah')->store('customer-visits/photos', 'local');
            $visit->photo_rumah_path = $path;
        }

        // Handle Foto Orang yang Ditemui
        if ($request->hasFile('photo_orang')) {
            if ($visit->photo_orang_path) {
                Storage::disk('local')->delete($visit->photo_orang_path);
            }
            $path = $request->file('photo_orang')->store('customer-visits/photos', 'local');
            $visit->photo_orang_path = $path;
        }

        $visit->save();

        return redirect()->route('customer-visits.index')->with('success', 'Kunjungan berhasil diperbarui.');
    }

    public function count($customerId)
    {
        $count = CustomerVisit::where('customer_id', $customerId)->where('is_accompanying', false)->count();
        return response()->json(['count' => $count]);
    }

    public function report($id)
    {
        $visit = CustomerVisit::with(['customer', 'user'])->findOrFail($id);
        return view('customer-visits.report', compact('visit'));
    }
}
