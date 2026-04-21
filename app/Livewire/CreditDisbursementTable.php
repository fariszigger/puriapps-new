<?php

namespace App\Livewire;

use App\Models\CreditDisbursement;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CreditDisbursementTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterMonth = '';
    public $filterMonthEnd = '';
    public $filterAo = '';
    public $viewMode = 'monthly'; // 'monthly', 'yearly', 'period'

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filterMonth' => ['except' => ''],
        'filterMonthEnd' => ['except' => ''],
        'filterAo' => ['except' => ''],
        'viewMode' => ['except' => 'monthly'],
    ];

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
        $this->filterMonthEnd = now()->format('Y-m');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }

    public function updatingFilterMonthEnd()
    {
        $this->resetPage();
    }

    public function updatingFilterAo()
    {
        $this->resetPage();
    }

    public function updatingViewMode()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user->cannot('delete credit-disbursements')) {
            return;
        }

        $disbursement = CreditDisbursement::findOrFail($id);
        $disbursement->delete();
        session()->flash('success', 'Data pencairan berhasil dihapus.');
    }

    public function render()
    {
        $query = CreditDisbursement::with('user');

        // Time filter
        if ($this->viewMode === 'yearly' && $this->filterMonth) {
            $year = date('Y', strtotime($this->filterMonth));
            $query->whereYear('disbursement_date', $year);
        } elseif ($this->viewMode === 'period' && $this->filterMonth && $this->filterMonthEnd) {
            $start = $this->filterMonth . '-01';
            $end = date('Y-m-t', strtotime($this->filterMonthEnd . '-01'));
            $query->whereBetween('disbursement_date', [$start, $end]);
        } elseif ($this->filterMonth) {
            $query->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$this->filterMonth]);
        }

        // AO filter
        if ($this->filterAo) {
            $query->where('user_id', $this->filterAo);
        }

        // Search
        $query->when(!empty($this->search), function ($query) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_spk', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('code', 'like', '%' . $this->search . '%');
                  });
            });
        });

        // Time summary
        $summaryQuery = CreditDisbursement::query();
        if ($this->viewMode === 'yearly' && $this->filterMonth) {
            $year = date('Y', strtotime($this->filterMonth));
            $summaryQuery->whereYear('disbursement_date', $year);
        } elseif ($this->viewMode === 'period' && $this->filterMonth && $this->filterMonthEnd) {
            $start = $this->filterMonth . '-01';
            $end = date('Y-m-t', strtotime($this->filterMonthEnd . '-01'));
            $summaryQuery->whereBetween('disbursement_date', [$start, $end]);
        } elseif ($this->filterMonth) {
            $summaryQuery->whereRaw("DATE_FORMAT(disbursement_date, '%Y-%m') = ?", [$this->filterMonth]);
        }

        $aoUsers = User::role(['AO', 'Kabag'])->orderBy('name')->get(['id', 'name', 'code', 'disbursement_target']);

        // Build a map of user targets
        $targetMap = $aoUsers->pluck('disbursement_target', 'id');

        $aoSummary = $summaryQuery
            ->selectRaw('user_id, SUM(amount) as total_amount, COUNT(*) as total_count')
            ->groupBy('user_id')
            ->with('user:id,name,code,disbursement_target')
            ->get()
            ->map(function ($item) use ($targetMap) {
                $baseTarget = $targetMap->get($item->user_id, 400000000);
                
                $multiplier = 1;
                if ($this->viewMode === 'yearly') {
                    $multiplier = 12;
                } elseif ($this->viewMode === 'period' && $this->filterMonth && $this->filterMonthEnd) {
                    $d1 = new \DateTime($this->filterMonth . '-01');
                    $d2 = new \DateTime($this->filterMonthEnd . '-01');
                    $multiplier = (($d2->format('Y') - $d1->format('Y')) * 12) + ($d2->format('m') - $d1->format('m')) + 1;
                    $multiplier = max(1, $multiplier);
                }

                $target = $baseTarget * $multiplier;
                return [
                    'user_id' => $item->user_id,
                    'name' => $item->user->name ?? '-',
                    'code' => $item->user->code ?? '-',
                    'total_amount' => $item->total_amount,
                    'total_count' => $item->total_count,
                    'limit' => $target,
                    'percentage' => $target > 0 ? round(($item->total_amount / $target) * 100, 1) : 0,
                ];
            });

        $grandTotal = $aoSummary->sum('total_amount');
        $aoCount = $aoUsers->count();
        $baseTotalTarget = $aoUsers->sum('disbursement_target');
        
        $totalMultiplier = 1;
        if ($this->viewMode === 'yearly') {
            $totalMultiplier = 12;
        } elseif ($this->viewMode === 'period' && $this->filterMonth && $this->filterMonthEnd) {
            $d1 = new \DateTime($this->filterMonth . '-01');
            $d2 = new \DateTime($this->filterMonthEnd . '-01');
            $totalMultiplier = (($d2->format('Y') - $d1->format('Y')) * 12) + ($d2->format('m') - $d1->format('m')) + 1;
            $totalMultiplier = max(1, $totalMultiplier);
        }
        $totalTarget = $baseTotalTarget * $totalMultiplier;

        return view('livewire.credit-disbursement-table', [
            'disbursements' => $query->orderBy('nomor_spk', 'desc')->paginate($this->perPage),
            'aoSummary' => $aoSummary,
            'grandTotal' => $grandTotal,
            'totalTarget' => $totalTarget,
            'aoCount' => $aoCount,
            'aoUsers' => $aoUsers,
        ]);
    }
}
