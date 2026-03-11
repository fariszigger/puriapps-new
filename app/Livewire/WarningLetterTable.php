<?php

namespace App\Livewire;

use App\Models\WarningLetter;
use Livewire\Component;
use Livewire\WithPagination;

class WarningLetterTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $activeTab = 'sp1';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'activeTab' => ['except' => 'sp1'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function delete($id)
    {
        $letter = WarningLetter::findOrFail($id);
        $letter->delete();
        session()->flash('success', 'Surat berhasil dihapus.');
    }

    public function render()
    {
        $query = WarningLetter::with(['customer', 'user'])
            ->where('type', $this->activeTab);

        $query->when(!empty($this->search), function ($query) {
            $query->where(function ($q) {
                $q->whereHas('customer', function ($cq) {
                    $cq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('letter_number', 'like', '%' . $this->search . '%')
                ->orWhere('credit_agreement_number', 'like', '%' . $this->search . '%');
            });
        });

        // Count per type for tab badges
        $counts = [
            'sp1' => WarningLetter::where('type', 'sp1')->count(),
            'sp2' => WarningLetter::where('type', 'sp2')->count(),
            'sp3' => WarningLetter::where('type', 'sp3')->count(),
            'panggilan' => WarningLetter::where('type', 'panggilan')->count(),
        ];

        return view('livewire.warning-letter-table', [
            'letters' => $query->orderBy('id', 'desc')->paginate($this->perPage),
            'counts' => $counts,
        ]);
    }
}
