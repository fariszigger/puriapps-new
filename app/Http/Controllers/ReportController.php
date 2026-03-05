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
        $now = Carbon::now();

        if ($filter === 'daily') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodLabel = 'Hari Ini (' . $startDate->format('d M Y') . ')';
        } elseif ($filter === 'weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
            $periodLabel = 'Minggu Ini (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
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

        $dates = $visits->groupBy(function ($visit) {
            return $visit->created_at->format('Y-m-d');
        })->map(function ($dateVisits) {
            return $dateVisits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'customer_name' => $visit->customer->name ?? '-',
                    'kolektibilitas' => $visit->kolektibilitas,
                    'ketemu_dengan' => $visit->ketemu_dengan,
                    'hasil_penagihan' => $visit->hasil_penagihan,
                    'jumlah_bayar' => $visit->jumlah_bayar,
                    'tanggal_janji_bayar' => $visit->tanggal_janji_bayar,
                    'jumlah_pembayaran' => $visit->jumlah_pembayaran,
                    'janji_bayar_fulfilled' => $visit->janji_bayar_fulfilled,
                    'time' => $visit->created_at->format('H:i'),
                ];
            });
        });

        return view('reports.performance-detail', [
            'aoUser' => $user,
            'dates' => $dates,
            'periodLabel' => $periodLabel,
            'totalVisits' => $visits->count(),
        ]);
    }

    public function recap(Request $request)
    {
        $this->authorize('view performance reports');

        $filter = $request->query('filter', 'monthly');
        $selectedMonth = $request->query('month');
        $now = Carbon::now();

        if ($filter === 'daily') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodLabel = 'Hari Ini (' . $startDate->format('d M Y') . ')';
        } elseif ($filter === 'weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
            $periodLabel = 'Minggu Ini (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
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
                $query->role('AO'); // Only get visits created by AO users
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by user, then by date
        $recapData = $visits->groupBy(function ($visit) {
            return $visit->user_id;
        })->map(function ($userVisits) {
            $user = $userVisits->first()->user;
            return [
                'user' => $user,
                'dates' => $userVisits->groupBy(function ($visit) {
                    return $visit->created_at->format('Y-m-d');
                })->map(function ($dateVisits) {
                    return $dateVisits->map(function ($visit) {
                        return [
                            'id' => $visit->id,
                            'customer_name' => $visit->customer->name ?? '-',
                            'kolektibilitas' => $visit->kolektibilitas,
                            'ketemu_dengan' => $visit->ketemu_dengan,
                            'hasil_penagihan' => $visit->hasil_penagihan,
                            'jumlah_bayar' => $visit->jumlah_bayar,
                            'tanggal_janji_bayar' => $visit->tanggal_janji_bayar,
                            'jumlah_pembayaran' => $visit->jumlah_pembayaran,
                            'janji_bayar_fulfilled' => $visit->janji_bayar_fulfilled,
                            'time' => $visit->created_at->format('H:i'),
                        ];
                    });
                }),
            ];
        })->values(); // Reset keys for clean looping in view

        return view('reports.performance-recap', [
            'recapData' => $recapData,
            'periodLabel' => $periodLabel,
            'totalVisitsOverall' => $visits->count(),
        ]);
    }
}
