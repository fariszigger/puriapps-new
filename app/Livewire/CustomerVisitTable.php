<?php

namespace App\Livewire;

use App\Models\CustomerVisit;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerVisitTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $visit = CustomerVisit::findOrFail($id);
        $visit->delete();

        session()->flash('success', 'Kunjungan berhasil dihapus.');
    }

    public function render()
    {
        $query = CustomerVisit::with(['customer', 'user']);

        if (!auth()->user()->can('view all data')) {
            $query->where('user_id', auth()->id());
        }

        $query->when(!empty($this->search), function ($query) {
            $query->where(function ($q) {
                $q->whereHas('customer', function ($cq) {
                    $cq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('address', 'like', '%' . $this->search . '%')
                ->orWhere('kolektibilitas', 'like', '%' . $this->search . '%')
                ->orWhere('ketemu_dengan', 'like', '%' . $this->search . '%');
            });
        });

        return view('livewire.customer-visit-table', [
            'visits' => $query->orderBy('id', 'desc')->paginate($this->perPage),
        ]);
    }
}
