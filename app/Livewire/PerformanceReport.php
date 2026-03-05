<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;

class PerformanceReport extends Component
{
    public $filter = 'monthly';
    public $selectedMonth;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
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

    private function updateDateRange()
    {
        $now = Carbon::now();
        if ($this->filter === 'daily') {
            $this->startDate = $now->copy()->startOfDay();
            $this->endDate = $now->copy()->endOfDay();
        } elseif ($this->filter === 'weekly') {
            $this->startDate = $now->copy()->startOfWeek();
            $this->endDate = $now->copy()->endOfWeek();
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
            ->orderBy('name')
            ->get();

        return view('livewire.performance-report', [
            'aos' => $aos,
            'periodLabel' => $this->getPeriodLabel()
        ]);
    }

    private function getPeriodLabel()
    {
        if ($this->filter === 'daily') {
            return 'Hari Ini (' . $this->startDate->format('d M Y') . ')';
        } elseif ($this->filter === 'weekly') {
            return 'Minggu Ini (' . $this->startDate->format('d M') . ' - ' . $this->endDate->format('d M Y') . ')';
        } else {
            return 'Bulan Ini (' . $this->startDate->format('F Y') . ')';
        }
    }
}
