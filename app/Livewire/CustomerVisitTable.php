<?php

namespace App\Livewire;

use App\Models\CustomerVisit;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CustomerVisitTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filter = 'monthly';
    public $selectedMonth;
    public $selectedDate;
    public $selectedWeek = 1;

    public $aoCodeFilter = '';
    public $penagihanFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filter' => ['except' => 'monthly'],
        'selectedMonth' => ['except' => ''],
        'selectedDate' => ['except' => ''],
        'selectedWeek' => ['except' => 1],
        'aoCodeFilter' => ['except' => ''],
        'penagihanFilter' => ['except' => ''],
    ];


    public function mount()
    {
        if (!$this->selectedMonth) {
            $this->selectedMonth = Carbon::now()->format('Y-m');
        }
        if (!$this->selectedDate) {
            $this->selectedDate = Carbon::now()->format('Y-m-d');
        }
        // If not set by query string, calculate default week
        if ($this->selectedWeek == 1 && !request()->has('selectedWeek')) {
             $this->selectedWeek = min(5, (int) ceil(Carbon::now()->day / 7));
        }
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function updatedSelectedWeek()
    {
        $this->resetPage();
    }

    public function updatedAoCodeFilter()
    {
        $this->resetPage();
    }

    public function updatedPenagihanFilter()
    {
        $this->resetPage();
    }

    private function getDateRange()
    {
        $now = Carbon::now();
        if ($this->filter === 'daily') {
            try {
                $date = Carbon::parse($this->selectedDate);
            } catch (\Exception $e) {
                $date = $now;
            }
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        } elseif ($this->filter === 'weekly') {
            try {
                $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
            } catch (\Exception $e) {
                $date = $now;
            }
            $startOfMonth = $date->copy()->startOfMonth();
            
            $daysInMonth = $startOfMonth->daysInMonth;
            $maxWeeks = (int) ceil($daysInMonth / 7);

            $week = (int) $this->selectedWeek;
            if ($week < 1) $week = 1;
            if ($week > $maxWeeks) $week = $maxWeeks;

            $startDay = ($week - 1) * 7 + 1;
            $endDay = $week * 7;
            
            $startDate = $startOfMonth->copy()->addDays($startDay - 1)->startOfDay();
            
            if ($week == $maxWeeks) {
                $endDate = $startOfMonth->copy()->endOfMonth()->endOfDay();
            } else {
                $potentialEndDate = $startOfMonth->copy()->addDays($endDay - 1)->endOfDay();
                $endDate = $potentialEndDate > $startOfMonth->copy()->endOfMonth() 
                                ? $startOfMonth->copy()->endOfMonth()->endOfDay() 
                                : $potentialEndDate;
            }
            
            if ($startDate > $startOfMonth->copy()->endOfMonth()) {
                $startDate = $startOfMonth->copy()->endOfMonth()->startOfDay();
                $endDate = $startOfMonth->copy()->endOfMonth()->endOfDay();
            }
        } else {
            try {
                $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
            } catch (\Exception $e) {
                $date = Carbon::now();
            }
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        }

        return [$startDate, $endDate];
    }

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
        [$startDate, $endDate] = $this->getDateRange();

        $query = CustomerVisit::with(['customer', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!auth()->user()->can('view all data')) {
            $query->where('user_id', auth()->id());
        } else {
            // For global viewers, hide the accompanying duplicates so each visit only appears once
            $query->where('is_accompanying', false);
        }

        $query->when($this->aoCodeFilter, function ($q) {
            $q->whereHas('user', function ($uq) {
                $uq->where('code', $this->aoCodeFilter);
            });
        });

        $query->when($this->penagihanFilter, function ($q) {
            $q->where('penagihan_ke', $this->penagihanFilter);
        });

        $query->when(!empty($this->search), function ($query) {
            $query->where(function ($q) {
                $q->whereHas('customer', function ($cq) {
                    $cq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('address', 'like', '%' . $this->search . '%')
                ->orWhere('kolektibilitas', 'like', '%' . $this->search . '%')
                ->orWhere('ketemu_dengan', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($uq) {
                    $uq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            });
        });

        return view('livewire.customer-visit-table', [
            'visits' => $query->orderBy('id', 'desc')->paginate($this->perPage),
            'aoUsers' => \App\Models\User::role(['AO', 'Kabag'])->with('roles')->whereNotNull('code')->orderBy('code')->get(),
            'maxPenagihan' => \App\Models\CustomerVisit::max('penagihan_ke') ?? 1,
        ]);
    }
}
