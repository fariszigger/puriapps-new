<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;

class PerformanceReport extends Component
{
    public $filter = 'monthly';
    public $selectedMonth;
    public $selectedDate;
    public $selectedWeek = 1;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->selectedWeek = min(5, (int) ceil(Carbon::now()->day / 7));
        $this->updateDateRange();
    }

    public function updatedFilter()
    {
        $this->updateDateRange();
    }

    public function updatedSelectedMonth()
    {
        $this->updateDateRange();
    }

    public function updatedSelectedDate()
    {
        $this->updateDateRange();
    }

    public function updatedSelectedWeek()
    {
        $this->updateDateRange();
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
            // Validate and parse the selected month
            try {
                $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
            } catch (\Exception $e) {
                // Fallback to current month if format is invalid
                $date = Carbon::now();
                $this->selectedMonth = $date->format('Y-m');
            }
            $this->startDate = $date->copy()->startOfMonth();
            $this->endDate = $date->copy()->endOfMonth();
        }
    }

    public function render()
    {
        // Get all users who have the role 'AO'
        $aos = User::role('AO')
            ->withCount(['customerVisits as visits_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->withCount(['customerVisits as visits_kol_1_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '1');
            }])
            ->withCount(['customerVisits as visits_kol_2_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '2');
            }])
            ->withCount(['customerVisits as visits_kol_3_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '3');
            }])
            ->withCount(['customerVisits as visits_kol_4_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '4');
            }])
            ->withCount(['customerVisits as visits_kol_5_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '5');
            }])
            ->withSum(['customerVisits as direct_paid_sum' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }], 'jumlah_bayar')
            ->withSum(['customerVisits as fulfilled_paid_sum' => function ($query) {
                $query->whereBetween('janji_bayar_fulfilled_at', [$this->startDate, $this->endDate]);
            }], 'jumlah_bayar_fulfilled')
            ->orderBy('name')
            ->get()
            ->groupBy(function($ao) {
                return trim($ao->office_branch) ?: 'Kantor Pusat';
            })
            ->sortBy(function($group, $key) {
                if ($key === 'Kantor Pusat') return 0;
                if ($key === 'Kantor Kas Mojosari') return 1;
                return 2;
            });

        $kabagUsers = User::role('Kabag')
            ->withCount(['customerVisits as visits_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->withCount(['customerVisits as visits_kol_1_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '1');
            }])
            ->withCount(['customerVisits as visits_kol_2_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '2');
            }])
            ->withCount(['customerVisits as visits_kol_3_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '3');
            }])
            ->withCount(['customerVisits as visits_kol_4_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '4');
            }])
            ->withCount(['customerVisits as visits_kol_5_count' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])->where('kolektibilitas', '5');
            }])
            ->withSum(['customerVisits as direct_paid_sum' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }], 'jumlah_bayar')
            ->withSum(['customerVisits as fulfilled_paid_sum' => function ($query) {
                $query->whereBetween('janji_bayar_fulfilled_at', [$this->startDate, $this->endDate]);
            }], 'jumlah_bayar_fulfilled')
            ->orderBy('name')
            ->get();

        return view('livewire.performance-report', [
            'aosGroups' => $aos,
            'kabagUsers' => $kabagUsers,
            'periodLabel' => $this->getPeriodLabel()
        ]);
    }

    private function getPeriodLabel()
    {
        if ($this->filter === 'daily') {
            return 'Harian (' . $this->startDate->format('d M Y') . ')';
        } elseif ($this->filter === 'weekly') {
            return 'Minggu Ke-' . $this->selectedWeek . ' Bulan ' . $this->startDate->translatedFormat('F Y') . ' (' . $this->startDate->format('d M') . ' - ' . $this->endDate->format('d M Y') . ')';
        } else {
            return 'Bulan Ini (' . $this->startDate->format('F Y') . ')';
        }
    }
}
