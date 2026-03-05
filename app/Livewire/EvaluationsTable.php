<?php

namespace App\Livewire;

use App\Models\Evaluation;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluationsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Evaluation::query()->with(['customer', 'user']);

        // Filter by role: users with 'view all data' can see all, others only see their own
        if (!auth()->user()->can('view all data')) {
            $query->where('user_id', auth()->id());
        }

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas(
                    'customer',
                    function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    }
                )
                    ->orWhereHas(
                        'user',
                        function ($subQ) {
                            $subQ->where('code', 'like', '%' . $this->search . '%');
                        }
                    )
                    ->orWhere('application_id', 'like', '%' . $this->search . '%');
            });
        }

        $evaluations = $query->latest()->paginate($this->perPage);

        return view('livewire.evaluations-table', compact('evaluations'));
    }
}
