<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditDisbursement;
use App\Models\Evaluation;
use App\Models\CustomerVisit;
use App\Models\WarningLetter;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = $this->getDashboardStats($user);
        $pendingEvaluations = $this->getPendingEvaluations($user);
        $pendingJanjiBayar = $this->getPendingJanjiBayar($user);
        $next7Events = $this->getNext7DaysEvents($user);

        return view('dashboard', array_merge($stats, [
            'pendingEvaluations' => $pendingEvaluations,
            'pendingJanjiBayar' => $pendingJanjiBayar,
            'next7Events' => $next7Events,
        ]));
    }

    private function getDashboardStats($user): array
    {
        $currentMonth = now()->format('Y-m');

        // Stats — AO sees only their own data, other roles see global
        if (!$user->can('view all data')) {
            return [
                'totalCustomers' => Customer::where('user_id', $user->id)->count(),
                'totalEvaluations' => Evaluation::where('user_id', $user->id)->count(),
                'approvedCount' => Evaluation::where('user_id', $user->id)->where('approval_status', 'approved')->count(),
                'rejectedCount' => Evaluation::where('user_id', $user->id)->where('approval_status', 'rejected')->count(),
                'totalVisits' => CustomerVisit::where('user_id', $user->id)->count(),
                'totalDisbursement' => CreditDisbursement::where('user_id', $user->id)
                    ->whereYear('disbursement_date', now()->format('Y'))
                    ->whereMonth('disbursement_date', '<=', now()->format('m'))
                    ->sum('amount'),
                'totalTarget' => ($user->disbursement_target ?? 400000000) * now()->format('n'),
            ];
        }

        // Global target: sum of all active AO targets * current month
        $globalTarget = \App\Models\User::role('AO')->sum('disbursement_target') * now()->format('n');

        return [
            'totalCustomers' => Customer::count(),
            'totalEvaluations' => Evaluation::count(),
            'approvedCount' => Evaluation::where('approval_status', 'approved')->count(),
            'rejectedCount' => Evaluation::where('approval_status', 'rejected')->count(),
            'totalVisits' => CustomerVisit::count(),
            'totalDisbursement' => CreditDisbursement::whereYear('disbursement_date', now()->format('Y'))
                ->whereMonth('disbursement_date', '<=', now()->format('m'))
                ->sum('amount'),
            'totalTarget' => $globalTarget,
        ];
    }

    private function getPendingEvaluations($user)
    {
        // Pending evaluations for kabag/admin
        if ($user->can('approve evaluations')) {
            return Evaluation::with(['customer', 'user'])
                ->where('approval_status', 'pending')
                ->latest()
                ->get();
        }
        
        return collect();
    }

    private function getPendingJanjiBayar($user)
    {
        // Pending Janji Bayar for AO
        if (!$user->can('view all data')) {
            return CustomerVisit::with('customer')
                ->where('user_id', $user->id)
                ->whereNotNull('tanggal_janji_bayar')
                ->where('tanggal_janji_bayar', '<=', now()->addDays(3)->format('Y-m-d'))
                ->where('janji_bayar_fulfilled', false)
                ->orderBy('tanggal_janji_bayar', 'asc')
                ->get();
        }

        return collect();
    }

    private function getNext7DaysEvents($user)
    {
        // Next 7 days calendar events
        $isAO = !$user->can('view all data');
        $today = \Carbon\Carbon::today();
        $next7 = $today->copy()->addDays(7);
        $next7Events = collect();

        // DOB events
        $customerDobQuery = Customer::select('id', 'name', 'dob', 'spouse_name', 'spouse_dob', 'user_id')
            ->whereNotNull('dob');
        
        if ($isAO) {
            $customerDobQuery->where('user_id', $user->id);
        }
        
        $dobCustomers = $customerDobQuery->get();

        foreach ($dobCustomers as $customer) {
            foreach ([['dob', $customer->name], ['spouse_dob', $customer->spouse_name ?? 'Pasangan ' . $customer->name]] as [$field, $name]) {
                if ($customer->$field) {
                    $dobThisYear = \Carbon\Carbon::parse($customer->$field)->setYear($today->year);
                    if ($dobThisYear->between($today, $next7)) {
                        $next7Events->push([
                            'type' => 'dob',
                            'date' => $customer->$field,
                            'display_date' => $dobThisYear->format('d M'),
                            'name' => $name,
                            'age' => $today->year - \Carbon\Carbon::parse($customer->$field)->year,
                        ]);
                    }
                }
            }
        }

        // Janji Bayar events in next 7 days
        $jbQuery = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->select('id', 'customer_id', 'user_id', 'tanggal_janji_bayar', 'jumlah_bayar', 'jumlah_pembayaran', 'janji_bayar_fulfilled')
            ->whereNotNull('tanggal_janji_bayar')
            ->where('janji_bayar_fulfilled', false)
            ->whereBetween('tanggal_janji_bayar', [$today->format('Y-m-d'), $next7->format('Y-m-d')]);
        
        if ($isAO) {
            $jbQuery->where('user_id', $user->id);
        }
        
        foreach ($jbQuery->get() as $jb) {
            $next7Events->push([
                'type' => 'janji_bayar',
                'date' => $jb->tanggal_janji_bayar,
                'display_date' => \Carbon\Carbon::parse($jb->tanggal_janji_bayar)->format('d M'),
                'name' => $jb->customer->name ?? '-',
                'ao_code' => $jb->user->code ?? $jb->user->name ?? '-',
                'jumlah' => $jb->jumlah_pembayaran ?? $jb->jumlah_bayar,
            ]);
        }

        // Visit events in next 7 days (already happened or scheduled)
        $visitQuery2 = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->select('id', 'customer_id', 'user_id', 'created_at', 'kolektibilitas')
            ->whereBetween('created_at', [$today->startOfDay(), $next7->endOfDay()]);
        
        if ($isAO) {
            $visitQuery2->where('user_id', $user->id);
        }
        
        foreach ($visitQuery2->get() as $v) {
            $next7Events->push([
                'type' => 'visit',
                'date' => $v->created_at->format('Y-m-d'),
                'display_date' => $v->created_at->format('d M'),
                'name' => $v->customer->name ?? '-',
                'ao_code' => $v->user->code ?? $v->user->name ?? '-',
            ]);
        }

        // Warning Letter Follow-ups in next 7 days (letter_date + 21 days)
        $spQuery = WarningLetter::with(['customer:id,name', 'user:id,name,code'])
            ->select('id', 'customer_id', 'user_id', 'letter_date', 'type')
            ->whereIn('type', ['sp1', 'sp2']);
        
        if ($isAO) {
            $spQuery->where('user_id', $user->id);
        }

        foreach ($spQuery->get() as $sp) {
            $followUpDate = $sp->letter_date->addDays(21);
            if ($followUpDate->between($today, $next7)) {
                $next7Events->push([
                    'type' => 'sp',
                    'date' => $followUpDate->format('Y-m-d'),
                    'display_date' => $followUpDate->format('d M'),
                    'name' => 'Follow Up SP - ' . ($sp->customer->name ?? '-'),
                    'ao_code' => $sp->user->code ?? $sp->user->name ?? '-',
                ]);
            }
        }

        return $next7Events->sortBy('date')->values();
    }

    public function stats()
    {
        $user = auth()->user();
        $month = request('month'); // e.g. "2026-03" or null for all-time
        $isAo = !$user->can('view all data');

        // Base queries with role scoping
        $customerQuery = Customer::query();
        $evaluationQuery = Evaluation::query();
        $visitQuery = CustomerVisit::query();

        $disbursementQuery = CreditDisbursement::query();

        if ($isAo) {
            $customerQuery->where('user_id', $user->id);
            $evaluationQuery->where('user_id', $user->id);
            $visitQuery->where('user_id', $user->id);
            $disbursementQuery->where('user_id', $user->id);
        }

        // Apply month filter
        if ($month) {
            $yearMonth = explode('-', $month);
            $customerQuery->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
            $evaluationQuery->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
            $visitQuery->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
            
            $disbursementQuery->whereYear('disbursement_date', $yearMonth[0])
                ->whereMonth('disbursement_date', '<=', $yearMonth[1]);
                
            $m = intval($yearMonth[1]);
        } else {
            // Default to current month for disbursements when no filter
            $disbursementQuery->whereYear('disbursement_date', now()->format('Y'))
                ->whereMonth('disbursement_date', '<=', now()->format('m'));
            $m = now()->format('n');
        }
        
        $totalTarget = ($isAo ? ($user->disbursement_target ?? 400000000) : \App\Models\User::role('AO')->sum('disbursement_target')) * $m;

        $stats = [
            'totalCustomers' => (clone $customerQuery)->count(),
            'totalEvaluations' => (clone $evaluationQuery)->count(),
            'approvedCount' => (clone $evaluationQuery)->where('approval_status', 'approved')->count(),
            'rejectedCount' => (clone $evaluationQuery)->where('approval_status', 'rejected')->count(),
            'totalVisits' => (clone $visitQuery)->count(),
            'totalDisbursement' => (clone $disbursementQuery)->sum('amount'),
            'totalTarget' => $totalTarget,
        ];

        // Chart data
        $userId = $isAo ? $user->id : null;
        if ($month) {
            $chartData = $this->getDailyChartData($month, $userId);
        } else {
            $chartData = $this->getMonthlyChartData($userId);
        }

        return response()->json([
            'stats' => $stats,
            'chart' => $chartData,
        ]);
    }

    private function getMonthlyChartData($userId = null)
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $labels = $months->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->translatedFormat('M Y'))->values();

        $buildMonthly = function ($query) use ($months) {
            $raw = $query
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
                ->whereRaw("created_at >= ?", [now()->subMonths(11)->startOfMonth()])
                ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                ->pluck('total', 'month');

            return $months->map(fn($m) => $raw->get($m, 0))->values();
        };

        $cq = Customer::query();
        $eq = Evaluation::query();
        $vq = CustomerVisit::query();
        if ($userId) {
            $cq->where('user_id', $userId);
            $eq->where('user_id', $userId);
            $vq->where('user_id', $userId);
        }

        return [
            'labels' => $labels,
            'customers' => $buildMonthly(clone $cq),
            'evaluations' => $buildMonthly(clone $eq),
            'approved' => $buildMonthly((clone $eq)->where('approval_status', 'approved')),
            'rejected' => $buildMonthly((clone $eq)->where('approval_status', 'rejected')),
            'visits' => $buildMonthly(clone $vq),
        ];
    }

    private function getDailyChartData($month, $userId = null)
    {
        $start = \Carbon\Carbon::parse($month . '-01');
        $daysInMonth = $start->daysInMonth;

        $days = collect();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $days->push($start->copy()->day($d)->format('Y-m-d'));
        }

        $labels = $days->map(fn($d) => \Carbon\Carbon::parse($d)->format('d'))->values();

        $buildDaily = function ($query) use ($days, $month) {
            $raw = $query
                ->selectRaw("DATE(created_at) as day, COUNT(*) as total")
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
                ->groupByRaw("DATE(created_at)")
                ->pluck('total', 'day');

            return $days->map(fn($d) => $raw->get($d, 0))->values();
        };

        $cq = Customer::query();
        $eq = Evaluation::query();
        $vq = CustomerVisit::query();
        if ($userId) {
            $cq->where('user_id', $userId);
            $eq->where('user_id', $userId);
            $vq->where('user_id', $userId);
        }

        return [
            'labels' => $labels,
            'customers' => $buildDaily(clone $cq),
            'evaluations' => $buildDaily(clone $eq),
            'approved' => $buildDaily((clone $eq)->where('approval_status', 'approved')),
            'rejected' => $buildDaily((clone $eq)->where('approval_status', 'rejected')),
            'visits' => $buildDaily(clone $vq),
        ];
    }
}
