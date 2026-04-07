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
    public $filterAo = '';
    public $viewMode = 'monthly'; // 'monthly', 'yearly'

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filterMonth' => ['except' => ''],
        'filterAo' => ['except' => ''],
        'viewMode' => ['except' => 'monthly'],
    ];

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
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
                $target = $this->viewMode === 'yearly' ? $baseTarget * 12 : $baseTarget;
                return [
                    'user_id' => $item->user_id,
                    'name' => $item->user->name ?? '-',
                    'code' => $item->user->code ?? '-',
                    'total_amount' => $item->total_amount,
                    'total_count' => $item->total_count,
                    'target' => $target,
                    'percentage' => $target > 0 ? min(100, round(($item->total_amount / $target) * 100, 1)) : 0,
                ];
            });

        $grandTotal = $aoSummary->sum('total_amount');
        $aoCount = $aoUsers->count();
        $baseTotalTarget = $aoUsers->sum('disbursement_target');
        $totalTarget = $this->viewMode === 'yearly' ? $baseTotalTarget * 12 : $baseTotalTarget;

        return view('livewire.credit-disbursement-table', [
            'disbursements' => $query->orderBy('disbursement_date', 'desc')->paginate($this->perPage),
            'aoSummary' => $aoSummary,
            'grandTotal' => $grandTotal,
            'totalTarget' => $totalTarget,
            'aoCount' => $aoCount,
            'aoUsers' => $aoUsers,
        ]);
    }
}
