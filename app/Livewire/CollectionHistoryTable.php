<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CollectionHistoryTable extends Component
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

    public function render()
    {
        // Get all customers who have AT LEAST ONE visit OR AT LEAST ONE warning letter
        $query = Customer::with(['user', 'visits', 'warningLetters'])
            ->where(function($q) {
                $q->has('visits')->orHas('warningLetters');
            });

        // Search logic
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('identity_number', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        // Scoping for AO (can only see their own OR customers they have visited)
        if (!auth()->user()->can('view all data')) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('visits', function ($vq) {
                      $vq->where('user_id', auth()->id());
                  });
            });
        }

        $customers = $query->orderBy('name', 'asc')->paginate($this->perPage);

        // Calculate "Tindakan Terakhir" based on system creation time for accuracy
        foreach ($customers as $customer) {
            $latestAction = 'Belum ada tindakan';
            $latestDate = null;
            $systemDate = null;
            $latestAo = '-';

            // Get the absolute latest system record time for both types (primary visits only)
            $latestLetter = $customer->warningLetters->sortByDesc('created_at')->first();
            $latestVisit = $customer->visits->where('is_accompanying', false)->sortByDesc('created_at')->first();

            if ($latestLetter && $latestVisit) {
                if ($latestLetter->created_at->greaterThanOrEqualTo($latestVisit->created_at)) {
                    $latestAction = $latestLetter->type_label;
                    $latestDate = $latestLetter->letter_date;
                    $systemDate = $latestLetter->created_at;
                    $latestAo = $latestLetter->user->name ?? '-';
                } else {
                    $latestAction = 'Penagihan ' . $customer->visits->where('is_accompanying', false)->count();
                    $latestDate = $latestVisit->created_at;
                    $systemDate = $latestVisit->created_at;
                    $latestAo = $latestVisit->user->name ?? '-';
                }
            } elseif ($latestLetter) {
                $latestAction = $latestLetter->type_label;
                $latestDate = $latestLetter->letter_date;
                $systemDate = $latestLetter->created_at;
                $latestAo = $latestLetter->user->name ?? '-';
            } elseif ($latestVisit) {
                $latestAction = 'Penagihan ' . $customer->visits->where('is_accompanying', false)->count();
                $latestDate = $latestVisit->created_at;
                $systemDate = $latestVisit->created_at;
                $latestAo = $latestVisit->user->name ?? '-';
            }

            $customer->tindakan_terakhir = $latestAction;
            $customer->tindakan_terakhir_tanggal = $latestDate;
            $customer->tindakan_terakhir_system = $systemDate;
            $customer->tindakan_terakhir_ao = $latestAo;
        }

        return view('livewire.collection-history-table', [
            'customers' => $customers,
        ]);
    }
}
