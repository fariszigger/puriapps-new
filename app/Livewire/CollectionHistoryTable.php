<?php

namespace App\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CollectionHistoryTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filter = 'monthly';
    public $selectedMonth;
    public $selectedDate;
    public $selectedWeek = 1;
    public $startDate;
    public $endDate;
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
        
        $this->updateDateRange();
    }

    public function updatedFilter()
    {
        $this->updateDateRange();
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->updateDateRange();
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->updateDateRange();
        $this->resetPage();
    }

    public function updatedSelectedWeek()
    {
        $this->updateDateRange();
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

    private function updateDateRange()
    {
        $now = Carbon::now();
        if ($this->filter === 'daily') {
            try {
                $date = Carbon::parse($this->selectedDate);
            } catch (\Exception $e) {
                $date = $now;
                $this->selectedDate = $date->format('Y-m-d');
            }
            $this->startDate = $date->copy()->startOfDay();
            $this->endDate = $date->copy()->endOfDay();
        } elseif ($this->filter === 'weekly') {
            try {
                $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
            } catch (\Exception $e) {
                $date = $now;
                $this->selectedMonth = $date->format('Y-m');
            }
            $startOfMonth = $date->copy()->startOfMonth();
            
            $daysInMonth = $startOfMonth->daysInMonth;
            $maxWeeks = (int) ceil($daysInMonth / 7);

            $week = (int) $this->selectedWeek;
            if ($week < 1) $week = 1;
            if ($week > $maxWeeks) $week = $maxWeeks;

            $startDay = ($week - 1) * 7 + 1;
            $endDay = $week * 7;
            
            $this->startDate = $startOfMonth->copy()->addDays($startDay - 1)->startOfDay();
            
            if ($week == $maxWeeks) {
                $this->endDate = $startOfMonth->copy()->endOfMonth()->endOfDay();
            } else {
                $potentialEndDate = $startOfMonth->copy()->addDays($endDay - 1)->endOfDay();
                $this->endDate = $potentialEndDate > $startOfMonth->copy()->endOfMonth() 
                                ? $startOfMonth->copy()->endOfMonth()->endOfDay() 
                                : $potentialEndDate;
            }
            
            if ($this->startDate > $startOfMonth->copy()->endOfMonth()) {
                $this->startDate = $startOfMonth->copy()->endOfMonth()->startOfDay();
                $this->endDate = $startOfMonth->copy()->endOfMonth()->endOfDay();
            }
        } else {
            try {
                $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
            } catch (\Exception $e) {
                $date = Carbon::now();
                $this->selectedMonth = $date->format('Y-m');
            }
            $this->startDate = $date->copy()->startOfMonth();
            $this->endDate = $date->copy()->endOfMonth();
        }
    }

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
        $visitConstraints = function($vq) {
            $vq->whereBetween('created_at', [$this->startDate, $this->endDate]);
            if ($this->penagihanFilter) {
                $vq->where('penagihan_ke', $this->penagihanFilter);
            }
            if ($this->aoCodeFilter) {
                $vq->whereHas('user', function($vuq) {
                    $vuq->where('code', $this->aoCodeFilter);
                });
            }
        };

        $letterConstraints = function($wq) {
            $wq->whereBetween('created_at', [$this->startDate, $this->endDate]);
            if ($this->aoCodeFilter) {
                $wq->whereHas('user', function($wuq) {
                    $wuq->where('code', $this->aoCodeFilter);
                });
            }
        };

        // Get all customers who have AT LEAST ONE matching visit OR matching warning letter
        $query = Customer::with([
            'user',
            'visits' => $visitConstraints,
            'warningLetters' => $letterConstraints
        ])->where(function($q) use ($visitConstraints, $letterConstraints) {
            $q->whereHas('visits', $visitConstraints);
            
            // If filtering by penagihan, do not include warning letters (they don't have penagihan_ke)
            if (!$this->penagihanFilter) {
                $q->orWhereHas('warningLetters', $letterConstraints);
            }
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

        // Add select subquery to track the latest activity date within the filtered period for sorting
        $query->addSelect([
            'latest_activity_at' => DB::table('customer_visits')
                ->select('created_at')
                ->whereColumn('customer_id', 'customers.id')
                ->where('is_accompanying', false)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->unionAll(
                    DB::table('warning_letters')
                        ->select('created_at')
                        ->whereColumn('customer_id', 'customers.id')
                        ->whereBetween('created_at', [$this->startDate, $this->endDate])
                )
                ->orderBy('created_at', 'desc')
                ->limit(1)
        ]);

        $customers = $query->orderBy('latest_activity_at', 'desc')->paginate($this->perPage);

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
            'aoUsers' => \App\Models\User::role('AO')->whereNotNull('code')->orderBy('code')->get(),
            'maxPenagihan' => \App\Models\CustomerVisit::max('penagihan_ke') ?? 1,
        ]);
    }
}
