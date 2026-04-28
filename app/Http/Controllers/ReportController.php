<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use App\Models\CustomerVisit;
use Carbon\Carbon;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function performance()
    {
        $this->authorize('view performance reports');
        return view('reports.performance');
    }

    public function detail(Request $request, User $user)
    {
        $this->authorize('view performance reports');

        $filter = $request->query('filter', 'monthly');
        $selectedMonth = $request->query('month');
        $selectedDate = $request->query('date');
        $selectedWeek = $request->query('week', 1);
        $now = Carbon::now();

        if ($filter === 'daily') {
            try {
                $date = $selectedDate ? Carbon::parse($selectedDate) : $now;
            } catch (\Exception $e) {
                $date = $now;
            }
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $periodLabel = 'Harian (' . $startDate->format('d M Y') . ')';
        } elseif ($filter === 'weekly') {
            try {
                $date = $selectedMonth ? Carbon::createFromFormat('Y-m', $selectedMonth) : $now;
            } catch (\Exception $e) {
                $date = $now;
            }
            $startOfMonth = $date->copy()->startOfMonth();
            
            $daysInMonth = $startOfMonth->daysInMonth;
            $maxWeeks = (int) ceil($daysInMonth / 7);

            $week = (int) $selectedWeek;
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

            $periodLabel = 'Minggu Ke-' . $week . ' Bulan ' . $startOfMonth->translatedFormat('F Y') . ' (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
        } else {
            // Monthly
            if ($selectedMonth) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $selectedMonth);
                } catch (\Exception $e) {
                    $date = $now;
                }
            } else {
                $date = $now;
            }
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $periodLabel = 'Bulan ' . $startDate->translatedFormat('F Y');
        }

        $visits = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // Build fulfilled-janji-bayar keys (customer_id|date) via a separate query
        // using janji_bayar_fulfilled_at — NOT from $visits — so it works even when
        // the original janji_bayar was created outside the viewed period (e.g. daily filter).
        $fulfilledKeys = CustomerVisit::where('user_id', $user->id)
            ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
            ->whereNotNull('customer_id')
            ->get(['customer_id', 'janji_bayar_fulfilled_at'])
            ->map(fn($v) => $v->customer_id . '|' . \Carbon\Carbon::parse($v->janji_bayar_fulfilled_at)->toDateString())
            ->values()
            ->toArray();

        $dates = $visits->groupBy(function ($visit) {
            return $visit->created_at->format('Y-m-d');
        })->map(function ($dateVisits) use ($fulfilledKeys) {
            return $dateVisits->map(function ($visit) use ($fulfilledKeys) {
                // Mark whether this bayar visit is a duplicate (shadowed by a fulfilled janji_bayar)
                $isDuplicate = $visit->hasil_penagihan === 'bayar'
                    && in_array($visit->customer_id . '|' . $visit->created_at->toDateString(), $fulfilledKeys);
                return [
                    'id' => $visit->id,
                    'customer_name' => $visit->customer->name ?? '-',
                    'address' => $visit->address ?? '-',
                    'photo_path' => $visit->photo_path,
                    'photo_rumah_path' => $visit->photo_rumah_path,
                    'photo_orang_path' => $visit->photo_orang_path,
                    'kolektibilitas' => $visit->kolektibilitas,
                    'ketemu_dengan' => $visit->ketemu_dengan,
                    'hasil_penagihan' => $visit->hasil_penagihan,
                    'jumlah_bayar' => $visit->jumlah_bayar,
                    'tanggal_janji_bayar' => $visit->tanggal_janji_bayar,
                    'jumlah_pembayaran' => $visit->jumlah_pembayaran,
                    'janji_bayar_fulfilled' => $visit->janji_bayar_fulfilled,
                    'janji_bayar_fulfilled_at' => $visit->janji_bayar_fulfilled_at,
                    'jumlah_bayar_fulfilled' => $visit->jumlah_bayar_fulfilled,
                    'kondisi_saat_ini' => $visit->kondisi_saat_ini,
                    'rencana_penyelesaian' => $visit->rencana_penyelesaian,
                    'time' => $visit->created_at->format('H:i'),
                    'is_duplicate_bayar' => $isDuplicate,
                ];
            });
        });

        // Sum fulfilled janji_bayar amounts within the period
        $fulfilledPaidSum = CustomerVisit::where('user_id', $user->id)
            ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
            ->sum('jumlah_bayar_fulfilled');

        // Sum direct bayar amounts, but skip visits that are duplicate of a fulfilled janji_bayar
        $directPaidSum = $visits
            ->filter(function ($v) use ($fulfilledKeys) {
                if ($v->hasil_penagihan !== 'bayar') return false;
                $key = $v->customer_id . '|' . $v->created_at->toDateString();
                return !in_array($key, $fulfilledKeys);
            })
            ->sum('jumlah_bayar');

        return view('reports.performance-detail', [
            'aoUser' => $user,
            'dates' => $dates,
            'periodLabel' => $periodLabel,
            'totalVisits' => $visits->count(),
            'counts' => [
                'kol_1' => $visits->where('kolektibilitas', '1')->count(),
                'kol_2' => $visits->where('kolektibilitas', '2')->count(),
                'kol_3' => $visits->where('kolektibilitas', '3')->count(),
                'kol_4' => $visits->where('kolektibilitas', '4')->count(),
                'kol_5' => $visits->where('kolektibilitas', '5')->count(),
            ],
            'totalPaid' => $directPaidSum + $fulfilledPaidSum,
        ]);
    }

    public function recap(Request $request)
    {
        $this->authorize('view performance reports');

        $filter = $request->query('filter', 'monthly');
        $selectedMonth = $request->query('month');
        $selectedDate = $request->query('date');
        $selectedWeek = $request->query('week', 1);
        $now = Carbon::now();

        if ($filter === 'daily') {
            try {
                $date = $selectedDate ? Carbon::parse($selectedDate) : $now;
            } catch (\Exception $e) {
                $date = $now;
            }
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $periodLabel = 'Harian (' . $startDate->format('d M Y') . ')';
        } elseif ($filter === 'weekly') {
            try {
                $date = $selectedMonth ? Carbon::createFromFormat('Y-m', $selectedMonth) : $now;
            } catch (\Exception $e) {
                $date = $now;
            }
            $startOfMonth = $date->copy()->startOfMonth();
            
            $daysInMonth = $startOfMonth->daysInMonth;
            $maxWeeks = (int) ceil($daysInMonth / 7);

            $week = (int) $selectedWeek;
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

            $periodLabel = 'Minggu Ke-' . $week . ' Bulan ' . $startOfMonth->translatedFormat('F Y') . ' (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
        } else {
            // Monthly
            if ($selectedMonth) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $selectedMonth);
                } catch (\Exception $e) {
                    $date = $now;
                }
            } else {
                $date = $now;
            }
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $periodLabel = 'Bulan ' . $startDate->translatedFormat('F Y');
        }

        $visits = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function ($query) {
                $query->role(['AO', 'Kabag']); // Include both AO and Kabag roles
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by user, then by date
        $recapData = $visits->groupBy(function ($visit) {
            return $visit->user_id;
        })->map(function ($userVisits) use ($startDate, $endDate) {
            $user = $userVisits->first()->user;

            // Build fulfilled-janji-bayar keys via separate query using janji_bayar_fulfilled_at
            // so it works even when the original janji_bayar was created outside the viewed period.
            $userFulfilledKeys = CustomerVisit::where('user_id', $user->id)
                ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
                ->whereNotNull('customer_id')
                ->get(['customer_id', 'janji_bayar_fulfilled_at'])
                ->map(fn($v) => $v->customer_id . '|' . \Carbon\Carbon::parse($v->janji_bayar_fulfilled_at)->toDateString())
                ->values()
                ->toArray();

            $userFulfilledSum = CustomerVisit::where('user_id', $user->id)
                ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
                ->sum('jumlah_bayar_fulfilled');

            $userDirectSum = $userVisits
                ->filter(function ($v) use ($userFulfilledKeys) {
                    if ($v->hasil_penagihan !== 'bayar') return false;
                    $key = $v->customer_id . '|' . $v->created_at->toDateString();
                    return !in_array($key, $userFulfilledKeys);
                })
                ->sum('jumlah_bayar');

            return [
                'user' => $user,
                'counts' => [
                    'total' => $userVisits->count(),
                    'kol_1' => $userVisits->where('kolektibilitas', '1')->count(),
                    'kol_2' => $userVisits->where('kolektibilitas', '2')->count(),
                    'kol_3' => $userVisits->where('kolektibilitas', '3')->count(),
                    'kol_4' => $userVisits->where('kolektibilitas', '4')->count(),
                    'kol_5' => $userVisits->where('kolektibilitas', '5')->count(),
                    'total_paid' => $userDirectSum + $userFulfilledSum,
                ],
                'dates' => $userVisits->groupBy(function ($visit) {
                    return $visit->created_at->format('Y-m-d');
                })->map(function ($dateVisits) use ($userFulfilledKeys) {
                    return $dateVisits->map(function ($visit) use ($userFulfilledKeys) {
                        $isDuplicate = $visit->hasil_penagihan === 'bayar'
                            && in_array($visit->customer_id . '|' . $visit->created_at->toDateString(), $userFulfilledKeys);
                        return [
                            'id' => $visit->id,
                            'customer_name' => $visit->customer->name ?? '-',
                            'address' => $visit->address ?? '-',
                            'photo_path' => $visit->photo_path,
                            'photo_rumah_path' => $visit->photo_rumah_path,
                            'photo_orang_path' => $visit->photo_orang_path,
                            'kolektibilitas' => $visit->kolektibilitas,
                            'ketemu_dengan' => $visit->ketemu_dengan,
                            'hasil_penagihan' => $visit->hasil_penagihan,
                            'jumlah_bayar' => $visit->jumlah_bayar,
                            'tanggal_janji_bayar' => $visit->tanggal_janji_bayar,
                            'jumlah_pembayaran' => $visit->jumlah_pembayaran,
                            'janji_bayar_fulfilled' => $visit->janji_bayar_fulfilled,
                            'janji_bayar_fulfilled_at' => $visit->janji_bayar_fulfilled_at,
                            'jumlah_bayar_fulfilled' => $visit->jumlah_bayar_fulfilled,
                            'kondisi_saat_ini' => $visit->kondisi_saat_ini,
                            'rencana_penyelesaian' => $visit->rencana_penyelesaian,
                            'time' => $visit->created_at->format('H:i'),
                            'is_duplicate_bayar' => $isDuplicate,
                        ];
                    });
                }),
            ];
        })->values(); // Reset keys for clean looping in view

        // Overall grand-total: build fulfilled keys via separate query (not from $visits)
        // so it works for daily/weekly filters where the original janji_bayar may be outside the period.
        $allFulfilledKeys = CustomerVisit::whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
            ->whereNotNull('customer_id')
            ->whereHas('user', fn($q) => $q->role(['AO', 'Kabag']))
            ->get(['user_id', 'customer_id', 'janji_bayar_fulfilled_at'])
            ->map(fn($v) => $v->user_id . '|' . $v->customer_id . '|' . \Carbon\Carbon::parse($v->janji_bayar_fulfilled_at)->toDateString())
            ->values()
            ->toArray();

        $grandDirectSum = $visits
            ->filter(function ($v) use ($allFulfilledKeys) {
                if ($v->hasil_penagihan !== 'bayar') return false;
                $key = $v->user_id . '|' . $v->customer_id . '|' . $v->created_at->toDateString();
                return !in_array($key, $allFulfilledKeys);
            })
            ->sum('jumlah_bayar');

        $grandFulfilledSum = CustomerVisit::whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
            ->whereHas('user', function($q) { $q->role(['AO', 'Kabag']); })
            ->sum('jumlah_bayar_fulfilled');

        return view('reports.performance-recap', [
            'recapData' => $recapData,
            'periodLabel' => $periodLabel,
            'totalVisitsOverall' => $visits->count(),
            'totals' => [
                'kol_1' => $visits->where('kolektibilitas', '1')->count(),
                'kol_2' => $visits->where('kolektibilitas', '2')->count(),
                'kol_3' => $visits->where('kolektibilitas', '3')->count(),
                'kol_4' => $visits->where('kolektibilitas', '4')->count(),
                'kol_5' => $visits->where('kolektibilitas', '5')->count(),
                'total_paid' => $grandDirectSum + $grandFulfilledSum,
            ],
        ]);
    }

    public function exportXls(Request $request)
    {
        if (auth()->user()->cannot('view performance reports')) {
            abort(403);
        }

        $filter = $request->get('filter', 'monthly');
        $monthStr = $request->get('month', Carbon::now()->format('Y-m'));
        $dateStr = $request->get('date', Carbon::now()->format('Y-m-d'));
        $week = $request->get('week', 1);

        $now = Carbon::now();
        if ($filter === 'daily') {
            try { $date = Carbon::parse($dateStr); } catch (\Exception $e) { $date = $now; }
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        } elseif ($filter === 'weekly') {
            try { $date = Carbon::createFromFormat('Y-m', $monthStr); } catch (\Exception $e) { $date = $now; }
            $startOfMonth = $date->copy()->startOfMonth();
            $startDay = ($week - 1) * 7 + 1;
            $endDay = min($week * 7, $startOfMonth->daysInMonth);
            $startDate = $startOfMonth->copy()->addDays($startDay - 1)->startOfDay();
            $endDate = $startOfMonth->copy()->addDays($endDay - 1)->endOfDay();
        } else {
            try { $date = Carbon::createFromFormat('Y-m', $monthStr); } catch (\Exception $e) { $date = $now; }
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        }

        $visits = CustomerVisit::with(['customer', 'user'])
            ->join('users', 'customer_visits.user_id', '=', 'users.id')
            ->select('customer_visits.*')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('customer_visits.created_at', [$startDate, $endDate])
                  ->orWhereBetween('customer_visits.janji_bayar_fulfilled_at', [$startDate, $endDate]);
            })
            ->whereHas('user', function ($query) {
                $query->role(['AO', 'Kabag']);
            })
            ->orderBy('users.name', 'asc')
            ->orderBy('customer_visits.created_at', 'asc')
            ->get();

        $filename = 'Performance_Report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->view('reports.performance-export-xls', compact('visits', 'startDate', 'endDate'), 200, $headers);
    }
}
