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
            'customer_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
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
            'customer_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $disbursement->update($validated);

        return redirect()->route('credit-disbursements.index')
            ->with('success', 'Data pencairan berhasil diperbarui.');
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
