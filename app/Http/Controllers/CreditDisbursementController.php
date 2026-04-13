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

        $aoUsers = User::role(['AO', 'Kabag'])->orderBy('name')->get();

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
            'status' => 'required|in:aktif,lunas',
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
        $aoUsers = User::role(['AO', 'Kabag'])->orderBy('name')->get();

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
            'status' => 'required|in:aktif,lunas',
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
        $filterMonthEnd = $request->query('month_end', date('Y-m'));
        $filterAo = $request->query('ao');
        $viewMode = $request->query('view_mode', 'monthly');

        $query = CreditDisbursement::with('user:id,name,code,disbursement_target,office_branch')->orderBy('disbursement_date', 'asc');

        if ($viewMode === 'yearly' && $filterMonth) {
            $year = date('Y', strtotime($filterMonth));
            $query->whereYear('disbursement_date', $year);
        } elseif ($viewMode === 'period' && $filterMonth && $filterMonthEnd) {
            $start = $filterMonth . '-01';
            $end = date('Y-m-t', strtotime($filterMonthEnd . '-01'));
            $query->whereBetween('disbursement_date', [$start, $end]);
        } elseif ($filterMonth) {
            $query->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$filterMonth]);
        }
         if ($filterAo) {
            $query->where('user_id', $filterAo);
        }

        $disbursements = $query->get();
        $groupedDisbursements = $disbursements->groupBy([
            fn($item) => $item->user->office_branch ?? 'PUSAT',
            fn($item) => $item->user->code ?? 'N/A'
        ]);

        // Calculate totals for print
        $totalAmount = $disbursements->sum('amount');
        
        $aoUsers = User::role('AO')->get(['id', 'name', 'disbursement_target']);
        $targetMap = $aoUsers->pluck('disbursement_target', 'id');

        // Target total logic depends on filtered AO and view mode
        if ($filterAo) {
            $baseTarget = $targetMap->get($filterAo, 400000000);
        } else {
            $baseTarget = $aoUsers->sum('disbursement_target');
        }
        
        $multiplier = 1;
        if ($viewMode === 'yearly') {
            $multiplier = 12;
        } elseif ($viewMode === 'period' && $filterMonth && $filterMonthEnd) {
            $d1 = new \DateTime($filterMonth . '-01');
            $d2 = new \DateTime($filterMonthEnd . '-01');
            $multiplier = (($d2->format('Y') - $d1->format('Y')) * 12) + ($d2->format('m') - $d1->format('m')) + 1;
            $multiplier = max(1, $multiplier);
        }
        
        $totalTarget = $baseTarget * $multiplier;

        return view('credit-disbursements.print', compact('disbursements', 'groupedDisbursements', 'filterMonth', 'filterMonthEnd', 'filterAo', 'totalAmount', 'totalTarget', 'viewMode'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        if ($user->cannot('view credit-disbursements')) {
            abort(403);
        }

        $filterMonth = $request->query('month', date('Y-m'));
        $filterMonthEnd = $request->query('month_end', date('Y-m'));
        $filterAo = $request->query('ao');
        $viewMode = $request->query('view_mode', 'monthly');

        $query = CreditDisbursement::with('user:id,name,code,disbursement_target,office_branch')->orderBy('disbursement_date', 'asc');

        if ($viewMode === 'yearly' && $filterMonth) {
            $year = date('Y', strtotime($filterMonth));
            $query->whereYear('disbursement_date', $year);
        } elseif ($viewMode === 'period' && $filterMonth && $filterMonthEnd) {
            $start = $filterMonth . '-01';
            $end = date('Y-m-t', strtotime($filterMonthEnd . '-01'));
            $query->whereBetween('disbursement_date', [$start, $end]);
        } elseif ($filterMonth) {
            $query->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$filterMonth]);
        }

        if ($filterAo) {
            $query->where('user_id', $filterAo);
        }

        $disbursements = $query->get();

        $filename = 'Register_Pencairan_' . $filterMonth . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->view('credit-disbursements.export', compact('disbursements', 'filterMonth', 'filterMonthEnd', 'viewMode', 'filterAo'), 200, $headers);
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

    public function analytics(Request $request)
    {
        $user = auth()->user();
        if ($user->cannot('view credit-disbursements')) {
            abort(403);
        }

        $viewMode = $request->query('view_mode', 'monthly');
        $filterMonth = $request->query('month', date('Y-m'));
        $filterMonth2 = $request->query('month2');
        $filterAo = $request->query('ao');

        $aoUsers = User::role(['AO', 'Kabag'])->orderBy('name')->get(['id', 'name', 'code', 'disbursement_target']);

        if ($request->query('json')) {
            $period1 = $this->getPeriodData($viewMode, $filterMonth, $filterAo, $aoUsers);

            $result = [
                'period1' => $period1,
                'period2' => null,
            ];

            if ($filterMonth2) {
                $result['period2'] = $this->getPeriodData($viewMode, $filterMonth2, $filterAo, $aoUsers);
            }

            return response()->json($result);
        }

        return view('credit-disbursements.analytics', compact('aoUsers'));
    }

    private function getPeriodData($viewMode, $filterMonth, $filterAo, $aoUsers)
    {
        $query = CreditDisbursement::query();

        if ($viewMode === 'yearly') {
            $year = date('Y', strtotime($filterMonth . '-01'));
            $query->whereYear('disbursement_date', $year);
            $periodLabel = $year;
        } else {
            $query->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$filterMonth]);
            $periodLabel = \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y');
        }

        if ($filterAo) {
            $query->where('user_id', $filterAo);
        }

        $disbursements = $query->with('user:id,name,code')->get();

        $multiplier = $viewMode === 'yearly' ? 12 : 1;
        $targetMap = $aoUsers->pluck('disbursement_target', 'id');

        $aoGrouped = $disbursements->groupBy('user_id');
        $aoLabels = [];
        $aoRealization = [];
        $aoTargets = [];
        $aoPercentages = [];

        $relevantUsers = $filterAo
            ? $aoUsers->where('id', $filterAo)
            : $aoUsers;

        foreach ($relevantUsers as $aoUser) {
            $aoLabels[] = $aoUser->code ?: $aoUser->name;
            $total = isset($aoGrouped[$aoUser->id])
                ? $aoGrouped[$aoUser->id]->sum('amount')
                : 0;
            $target = ($targetMap->get($aoUser->id, 400000000)) * $multiplier;
            $aoRealization[] = $total;
            $aoTargets[] = $target;
            $aoPercentages[] = $target > 0 ? round(($total / $target) * 100, 1) : 0;
        }

        if ($viewMode === 'yearly') {
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $growthLabels = $monthNames;
            $growthData = array_fill(0, 12, 0);
            $cumulativeData = array_fill(0, 12, 0);

            foreach ($disbursements as $d) {
                $monthIdx = (int) $d->disbursement_date->format('m') - 1;
                $growthData[$monthIdx] += $d->amount;
            }

            $cumulative = 0;
            foreach ($growthData as $i => $val) {
                $cumulative += $val;
                $cumulativeData[$i] = $cumulative;
            }

            $monthlyTarget = $filterAo
                ? ($targetMap->get((int) $filterAo, 400000000))
                : $aoUsers->sum('disbursement_target');
            $targetLine = [];
            for ($i = 0; $i < 12; $i++) {
                $targetLine[] = $monthlyTarget * ($i + 1);
            }
        } else {
            $daysInMonth = date('t', strtotime($filterMonth . '-01'));
            $growthLabels = [];
            $growthData = array_fill(0, (int) $daysInMonth, 0);
            $cumulativeData = array_fill(0, (int) $daysInMonth, 0);

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $growthLabels[] = (string) $d;
            }

            foreach ($disbursements as $d) {
                $dayIdx = (int) $d->disbursement_date->format('d') - 1;
                $growthData[$dayIdx] += $d->amount;
            }

            $cumulative = 0;
            foreach ($growthData as $i => $val) {
                $cumulative += $val;
                $cumulativeData[$i] = $cumulative;
            }

            $totalTarget = $filterAo
                ? ($targetMap->get((int) $filterAo, 400000000))
                : $aoUsers->sum('disbursement_target');
            $targetLine = array_fill(0, (int) $daysInMonth, $totalTarget);
        }

        $totalRealization = $disbursements->sum('amount');
        $totalTarget = $filterAo
            ? ($targetMap->get((int) $filterAo, 400000000)) * $multiplier
            : $aoUsers->sum('disbursement_target') * $multiplier;
        $totalCount = $disbursements->count();
        $achievement = $totalTarget > 0 ? round(($totalRealization / $totalTarget) * 100, 1) : 0;

        return [
            'label' => $periodLabel,
            'summary' => [
                'total_realization' => $totalRealization,
                'total_target' => $totalTarget,
                'achievement' => $achievement,
                'total_count' => $totalCount,
            ],
            'aoChart' => [
                'labels' => $aoLabels,
                'realization' => $aoRealization,
                'targets' => $aoTargets,
                'percentages' => $aoPercentages,
            ],
            'growthChart' => [
                'labels' => $growthLabels,
                'daily' => $growthData,
                'cumulative' => $cumulativeData,
                'target_line' => $targetLine,
            ],
        ];
    }
}

