<?php

namespace App\Http\Controllers;

use App\Models\CreditDisbursement;
use App\Models\User;
use Illuminate\Http\Request;

class CreditDisbursementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->cannot('view credit-disbursements')) {
            abort(403);
        }

        return view('credit-disbursements.index');
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->cannot('create credit-disbursements')) {
            abort(403);
        }

        $aoUsers = User::role('AO')->orderBy('name')->get();

        return view('credit-disbursements.create', compact('aoUsers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->cannot('create credit-disbursements')) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nomor_spk' => 'nullable|string|max:100',
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'jangka_waktu' => 'required|integer|min:1',
            'suku_bunga' => 'required|numeric|min:0',
            'jenis_pinjaman' => 'required|in:flat,anuitas,musiman',
            'angsuran' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        CreditDisbursement::create($validated);

        return redirect()->route('credit-disbursements.index')
            ->with('success', 'Data pencairan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        if ($user->cannot('edit credit-disbursements')) {
            abort(403);
        }

        $disbursement = CreditDisbursement::findOrFail($id);
        $aoUsers = User::role('AO')->orderBy('name')->get();

        return view('credit-disbursements.edit', compact('disbursement', 'aoUsers'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->cannot('edit credit-disbursements')) {
            abort(403);
        }

        $disbursement = CreditDisbursement::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nomor_spk' => 'nullable|string|max:100',
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'jangka_waktu' => 'required|integer|min:1',
            'suku_bunga' => 'required|numeric|min:0',
            'jenis_pinjaman' => 'required|in:flat,anuitas,musiman',
            'angsuran' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $disbursement->update($validated);

        return redirect()->route('credit-disbursements.index')
            ->with('success', 'Data pencairan berhasil diperbarui.');
    }

    public function print(Request $request)
    {
        $user = auth()->user();
        if ($user->cannot('view credit-disbursements')) {
            abort(403);
        }

        $filterMonth = $request->query('month', date('Y-m'));
        $filterAo = $request->query('ao');

        $query = CreditDisbursement::with('user:id,name,code,disbursement_target')->orderBy('disbursement_date', 'asc');

        if ($filterMonth) {
            $query->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$filterMonth]);
        }
        if ($filterAo) {
            $query->where('user_id', $filterAo);
        }

        $disbursements = $query->get();

        // Calculate totals for print
        $totalAmount = $disbursements->sum('amount');
        
        $aoUsers = User::role('AO')->get(['id', 'name', 'disbursement_target']);
        $targetMap = $aoUsers->pluck('disbursement_target', 'id');

        // Target total logic depends on filtered AO
        if ($filterAo) {
            $totalTarget = $targetMap->get($filterAo, 400000000);
        } else {
            $totalTarget = $aoUsers->sum('disbursement_target');
        }

        return view('credit-disbursements.print', compact('disbursements', 'filterMonth', 'filterAo', 'totalAmount', 'totalTarget'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->cannot('delete credit-disbursements')) {
            abort(403);
        }

        $disbursement = CreditDisbursement::findOrFail($id);
        $disbursement->delete();

        return redirect()->route('credit-disbursements.index')
            ->with('success', 'Data pencairan berhasil dihapus.');
    }
}
