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
        $selectedMonthEnd = $request->query('month_end');
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
        } elseif ($filter === 'period') {
            try { $dateStart = $selectedMonth ? Carbon::createFromFormat('Y-m', $selectedMonth) : $now; } catch (\Exception $e) { $dateStart = $now; }
            try { $dateEnd = $selectedMonthEnd ? Carbon::createFromFormat('Y-m', $selectedMonthEnd) : $now; } catch (\Exception $e) { $dateEnd = $now; }
            $startDate = $dateStart->copy()->startOfMonth();
            $endDate = $dateEnd->copy()->endOfMonth();
            if ($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate->copy()->startOfMonth();
                $endDate = $temp->copy()->endOfMonth();
            }
            $periodLabel = 'Periode (' . $startDate->translatedFormat('F Y') . ' - ' . $endDate->translatedFormat('F Y') . ')';
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

        $visits = CustomerVisit::with(['customer:id,name', 'user:id,name,code', 'manualExcludeBy:id,name'])
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
                // or manually excluded by Admin
                $isDuplicate = $visit->hasil_penagihan === 'bayar'
                    && ($visit->is_manual_exclude_bayar || in_array($visit->customer_id . '|' . $visit->created_at->toDateString(), $fulfilledKeys));
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
                    'janji_bayar_tidak_bayar' => $visit->janji_bayar_tidak_bayar,
                    'janji_bayar_tidak_bayar_reason' => $visit->janji_bayar_tidak_bayar_reason,
                    'janji_bayar_tidak_bayar_at' => $visit->janji_bayar_tidak_bayar_at,
                    'kondisi_saat_ini' => $visit->kondisi_saat_ini,
                    'rencana_penyelesaian' => $visit->rencana_penyelesaian,
                    'time' => $visit->created_at->format('H:i'),
                    'is_duplicate_bayar' => $isDuplicate,
                    'is_manual_exclude_bayar' => $visit->is_manual_exclude_bayar,
                    'manual_exclude_by_name' => $visit->manualExcludeBy->name ?? 'Admin',
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
                if ($v->is_manual_exclude_bayar) return false;
                $key = $v->customer_id . '|' . $v->created_at->toDateString();
                return !in_array($key, $fulfilledKeys);
            })
            ->sum('jumlah_bayar');

        // Cross-period fulfilled: janji_bayar visits created OUTSIDE this period but paid WITHIN it
        $crossPeriodFulfilled = CustomerVisit::with(['customer:id,name'])
            ->where('user_id', $user->id)
            ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('created_at', '<', $startDate)
                  ->orWhere('created_at', '>', $endDate);
            })
            ->whereNotNull('janji_bayar_fulfilled_at')
            ->orderBy('janji_bayar_fulfilled_at', 'asc')
            ->get();

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
            'crossPeriodFulfilled' => $crossPeriodFulfilled,
        ]);
    }

    public function recap(Request $request)
    {
        $this->authorize('view performance reports');

        $filter = $request->query('filter', 'monthly');
        $selectedMonth = $request->query('month');
        $selectedMonthEnd = $request->query('month_end');
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
        } elseif ($filter === 'period') {
            try { $dateStart = $selectedMonth ? Carbon::createFromFormat('Y-m', $selectedMonth) : $now; } catch (\Exception $e) { $dateStart = $now; }
            try { $dateEnd = $selectedMonthEnd ? Carbon::createFromFormat('Y-m', $selectedMonthEnd) : $now; } catch (\Exception $e) { $dateEnd = $now; }
            $startDate = $dateStart->copy()->startOfMonth();
            $endDate = $dateEnd->copy()->endOfMonth();
            if ($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate->copy()->startOfMonth();
                $endDate = $temp->copy()->endOfMonth();
            }
            $periodLabel = 'Periode (' . $startDate->translatedFormat('F Y') . ' - ' . $endDate->translatedFormat('F Y') . ')';
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

        $visits = CustomerVisit::with(['customer:id,name', 'user:id,name,code', 'manualExcludeBy:id,name'])
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
                    if ($v->is_manual_exclude_bayar) return false;
                    $key = $v->customer_id . '|' . $v->created_at->toDateString();
                    return !in_array($key, $userFulfilledKeys);
                })
                ->sum('jumlah_bayar');

            // Cross-period fulfilled: janji_bayar visits created OUTSIDE this period but paid WITHIN it
            $userCrossPeriod = CustomerVisit::with(['customer:id,name'])
                ->where('user_id', $user->id)
                ->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->where('created_at', '<', $startDate)
                      ->orWhere('created_at', '>', $endDate);
                })
                ->whereNotNull('janji_bayar_fulfilled_at')
                ->orderBy('janji_bayar_fulfilled_at', 'asc')
                ->get();

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
                'cross_period_fulfilled' => $userCrossPeriod,
                'dates' => $userVisits->groupBy(function ($visit) {
                    return $visit->created_at->format('Y-m-d');
                })->map(function ($dateVisits) use ($userFulfilledKeys) {
                    return $dateVisits->map(function ($visit) use ($userFulfilledKeys) {
                        $isDuplicate = $visit->hasil_penagihan === 'bayar'
                            && ($visit->is_manual_exclude_bayar || in_array($visit->customer_id . '|' . $visit->created_at->toDateString(), $userFulfilledKeys));
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
                            'janji_bayar_tidak_bayar' => $visit->janji_bayar_tidak_bayar,
                            'janji_bayar_tidak_bayar_reason' => $visit->janji_bayar_tidak_bayar_reason,
                            'janji_bayar_tidak_bayar_at' => $visit->janji_bayar_tidak_bayar_at,
                            'kondisi_saat_ini' => $visit->kondisi_saat_ini,
                            'rencana_penyelesaian' => $visit->rencana_penyelesaian,
                            'time' => $visit->created_at->format('H:i'),
                            'is_duplicate_bayar' => $isDuplicate,
                            'is_manual_exclude_bayar' => $visit->is_manual_exclude_bayar,
                            'manual_exclude_by_name' => $visit->manualExcludeBy->name ?? 'Admin',
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
                if ($v->is_manual_exclude_bayar) return false;
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

    public function summary(Request $request)
    {
        $this->authorize('view performance reports');

        $filter = $request->query('filter', 'monthly');
        $selectedMonth = $request->query('month');
        $selectedMonthEnd = $request->query('month_end');
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
        } elseif ($filter === 'period') {
            try { $dateStart = $selectedMonth ? Carbon::createFromFormat('Y-m', $selectedMonth) : $now; } catch (\Exception $e) { $dateStart = $now; }
            try { $dateEnd = $selectedMonthEnd ? Carbon::createFromFormat('Y-m', $selectedMonthEnd) : $now; } catch (\Exception $e) { $dateEnd = $now; }
            $startDate = $dateStart->copy()->startOfMonth();
            $endDate = $dateEnd->copy()->endOfMonth();
            if ($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate->copy()->startOfMonth();
                $endDate = $temp->copy()->endOfMonth();
            }
            $periodLabel = 'Periode (' . $startDate->translatedFormat('F Y') . ' - ' . $endDate->translatedFormat('F Y') . ')';
        } else {
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

        // Helper closure to build summary data for a user query
        $buildSummary = function ($role) use ($startDate, $endDate) {
            $dupSubquery = function ($sub) {
                $sub->from('customer_visits as dup')
                    ->whereColumn('dup.user_id', 'customer_visits.user_id')
                    ->whereColumn('dup.customer_id', 'customer_visits.customer_id')
                    ->where('dup.janji_bayar_fulfilled', true)
                    ->whereNotNull('dup.janji_bayar_fulfilled_at')
                    ->whereRaw('DATE(dup.janji_bayar_fulfilled_at) = DATE(customer_visits.created_at)')
                    ->whereNull('dup.deleted_at');
            };

            return User::role($role)
                ->withCount(['customerVisits as visits_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->withCount(['customerVisits as visits_kol_1_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '1');
                }])
                ->withCount(['customerVisits as visits_kol_2_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '2');
                }])
                ->withCount(['customerVisits as visits_kol_3_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '3');
                }])
                ->withCount(['customerVisits as visits_kol_4_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '4');
                }])
                ->withCount(['customerVisits as visits_kol_5_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '5');
                }])
                ->withSum(['customerVisits as direct_paid_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->where('hasil_penagihan', 'bayar')
                          ->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate]);
                }], 'jumlah_bayar_fulfilled')
                ->withSum(['customerVisits as direct_paid_kol_1_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '1')->where('hasil_penagihan', 'bayar')->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_kol_1_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])->where('kolektibilitas', '1');
                }], 'jumlah_bayar_fulfilled')
                ->withSum(['customerVisits as direct_paid_kol_2_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '2')->where('hasil_penagihan', 'bayar')->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_kol_2_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])->where('kolektibilitas', '2');
                }], 'jumlah_bayar_fulfilled')
                ->withSum(['customerVisits as direct_paid_kol_3_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '3')->where('hasil_penagihan', 'bayar')->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_kol_3_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])->where('kolektibilitas', '3');
                }], 'jumlah_bayar_fulfilled')
                ->withSum(['customerVisits as direct_paid_kol_4_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '4')->where('hasil_penagihan', 'bayar')->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_kol_4_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])->where('kolektibilitas', '4');
                }], 'jumlah_bayar_fulfilled')
                ->withSum(['customerVisits as direct_paid_kol_5_sum' => function ($query) use ($startDate, $endDate, $dupSubquery) {
                    $query->whereBetween('created_at', [$startDate, $endDate])->where('kolektibilitas', '5')->where('hasil_penagihan', 'bayar')->whereNotExists($dupSubquery);
                }], 'jumlah_bayar')
                ->withSum(['customerVisits as fulfilled_paid_kol_5_sum' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('janji_bayar_fulfilled_at', [$startDate, $endDate])->where('kolektibilitas', '5');
                }], 'jumlah_bayar_fulfilled')
                ->orderBy('name')
                ->get();
        };

        // Build summary arrays
        $mapToSummary = function ($user) {
            return [
                'name' => $user->name,
                'code' => $user->code ?? null,
                'branch' => trim($user->office_branch) ?: 'Kantor Pusat',
                'kol_1' => $user->visits_kol_1_count,
                'kol_2' => $user->visits_kol_2_count,
                'kol_3' => $user->visits_kol_3_count,
                'kol_4' => $user->visits_kol_4_count,
                'kol_5' => $user->visits_kol_5_count,
                'kol_1_paid' => ($user->direct_paid_kol_1_sum ?? 0) + ($user->fulfilled_paid_kol_1_sum ?? 0),
                'kol_2_paid' => ($user->direct_paid_kol_2_sum ?? 0) + ($user->fulfilled_paid_kol_2_sum ?? 0),
                'kol_3_paid' => ($user->direct_paid_kol_3_sum ?? 0) + ($user->fulfilled_paid_kol_3_sum ?? 0),
                'kol_4_paid' => ($user->direct_paid_kol_4_sum ?? 0) + ($user->fulfilled_paid_kol_4_sum ?? 0),
                'kol_5_paid' => ($user->direct_paid_kol_5_sum ?? 0) + ($user->fulfilled_paid_kol_5_sum ?? 0),
                'total_visits' => $user->visits_count,
                'total_paid' => ($user->direct_paid_sum ?? 0) + ($user->fulfilled_paid_sum ?? 0),
            ];
        };

        $kabagUsers = $buildSummary('Kabag');
        $aoUsers = $buildSummary('AO');

        $kabagSummary = $kabagUsers->map($mapToSummary)->values()->toArray();

        $aosByBranch = $aoUsers->map($mapToSummary)
            ->groupBy('branch')
            ->sortBy(function ($group, $key) {
                if ($key === 'Kantor Pusat') return 0;
                if ($key === 'Kantor Kas Mojosari') return 1;
                return 2;
            })
            ->toArray();

        $summaryData = $kabagSummary;
        foreach ($aosByBranch as $branchAos) {
            $summaryData = array_merge($summaryData, $branchAos);
        }

        $grandTotals = [
            'kol_1' => collect($summaryData)->sum('kol_1'),
            'kol_2' => collect($summaryData)->sum('kol_2'),
            'kol_3' => collect($summaryData)->sum('kol_3'),
            'kol_4' => collect($summaryData)->sum('kol_4'),
            'kol_5' => collect($summaryData)->sum('kol_5'),
            'kol_1_paid' => collect($summaryData)->sum('kol_1_paid'),
            'kol_2_paid' => collect($summaryData)->sum('kol_2_paid'),
            'kol_3_paid' => collect($summaryData)->sum('kol_3_paid'),
            'kol_4_paid' => collect($summaryData)->sum('kol_4_paid'),
            'kol_5_paid' => collect($summaryData)->sum('kol_5_paid'),
            'visits' => collect($summaryData)->sum('total_visits'),
            'total_paid' => collect($summaryData)->sum('total_paid'),
        ];

        return view('reports.performance-summary', [
            'kabagSummary' => $kabagSummary,
            'aosByBranch' => $aosByBranch,
            'summaryData' => $summaryData,
            'grandTotals' => $grandTotals,
            'periodLabel' => $periodLabel,
        ]);
    }

    public function exportXls(Request $request)
    {
        if (auth()->user()->cannot('view performance reports')) {
            abort(403);
        }

        $filter = $request->get('filter', 'monthly');
        $monthStr = $request->get('month', Carbon::now()->format('Y-m'));
        $monthEndStr = $request->get('month_end', Carbon::now()->format('Y-m'));
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
        } elseif ($filter === 'period') {
            try { $dateStart = Carbon::createFromFormat('Y-m', $monthStr); } catch (\Exception $e) { $dateStart = $now; }
            try { $dateEnd = Carbon::createFromFormat('Y-m', $monthEndStr); } catch (\Exception $e) { $dateEnd = $now; }
            $startDate = $dateStart->copy()->startOfMonth();
            $endDate = $dateEnd->copy()->endOfMonth();
            if ($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate->copy()->startOfMonth();
                $endDate = $temp->copy()->endOfMonth();
            }
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
