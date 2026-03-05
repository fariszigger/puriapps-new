<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerVisit;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAO = !$user->can('view all data');

        $dobEvents = $this->getDobEvents($isAO, $user);
        $visitEvents = $this->getVisitEvents($isAO, $user);
        $janjiBayarEvents = $this->getJanjiBayarEvents($isAO, $user);

        $today = Carbon::today();
        $allEvents = collect(array_merge($dobEvents, $visitEvents, $janjiBayarEvents));

        $thisMonthEvents = $this->filterThisMonthEvents($allEvents, $today);
        $next7Events = $this->filterNext7DayEvents($allEvents, $today);
        $recapVisits = $this->getRecapVisits($isAO, $user, $today);

        return view('calendar.index', [
            'dobEvents' => $dobEvents,
            'visitEvents' => $visitEvents,
            'janjiBayarEvents' => $janjiBayarEvents,
            'thisMonthEvents' => $thisMonthEvents,
            'next7Events' => $next7Events,
            'todayDate' => $today->format('Y-m-d'),
            'recapVisits' => $recapVisits,
            'isAO' => $isAO,
        ]);
    }

    private function getDobEvents(bool $isAO, $user): array
    {
        $query = Customer::select('id', 'name', 'dob', 'spouse_name', 'spouse_dob', 'user_id')
            ->whereNotNull('dob');

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $customers = $query->get();
        $events = [];

        foreach ($customers as $customer) {
            if ($customer->dob) {
                $events[] = [
                    'id' => 'dob-' . $customer->id,
                    'type' => 'dob',
                    'date' => $customer->dob,
                    'month_day' => Carbon::parse($customer->dob)->format('m-d'),
                    'name' => $customer->name,
                    'label' => 'Ulang Tahun - ' . $customer->name,
                    'customer_id' => $customer->id,
                ];
            }
            if ($customer->spouse_dob) {
                $events[] = [
                    'id' => 'dob-spouse-' . $customer->id,
                    'type' => 'dob',
                    'date' => $customer->spouse_dob,
                    'month_day' => Carbon::parse($customer->spouse_dob)->format('m-d'),
                    'name' => $customer->spouse_name ?? 'Pasangan ' . $customer->name,
                    'label' => 'Ulang Tahun Pasangan - ' . ($customer->spouse_name ?? $customer->name),
                    'customer_id' => $customer->id,
                ];
            }
        }

        return $events;
    }

    private function getVisitEvents(bool $isAO, $user): array
    {
        $query = CustomerVisit::with('customer:id,name')
            ->select('id', 'customer_id', 'user_id', 'created_at', 'kolektibilitas', 'ketemu_dengan');

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $visits = $query->get();
        $events = [];

        foreach ($visits as $visit) {
            $events[] = [
                'id' => 'visit-' . $visit->id,
                'type' => 'visit',
                'date' => $visit->created_at->format('Y-m-d'),
                'name' => $visit->customer->name ?? '-',
                'label' => 'Kunjungan - ' . ($visit->customer->name ?? '-'),
                'kolektibilitas' => $visit->kolektibilitas,
                'visit_id' => $visit->id,
            ];
        }

        return $events;
    }

    private function getJanjiBayarEvents(bool $isAO, $user): array
    {
        $query = CustomerVisit::with('customer:id,name')
            ->select('id', 'customer_id', 'user_id', 'tanggal_janji_bayar', 'jumlah_bayar', 'jumlah_pembayaran', 'janji_bayar_fulfilled', 'created_at')
            ->whereNotNull('tanggal_janji_bayar')
            ->where('janji_bayar_fulfilled', false);

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $janjiBayarVisits = $query->get();
        $events = [];

        foreach ($janjiBayarVisits as $jb) {
            $events[] = [
                'id' => 'janji-' . $jb->id,
                'type' => 'janji_bayar',
                'date' => $jb->tanggal_janji_bayar,
                'name' => $jb->customer->name ?? '-',
                'label' => 'Janji Bayar - ' . ($jb->customer->name ?? '-'),
                'jumlah' => $jb->jumlah_pembayaran ?? $jb->jumlah_bayar,
                'visit_id' => $jb->id,
            ];
        }

        return $events;
    }

    private function filterThisMonthEvents($allEvents, Carbon $today)
    {
        $currentMonth = $today->format('m');

        return $allEvents->filter(function ($event) use ($today, $currentMonth) {
            if ($event['type'] === 'dob') {
                return Carbon::parse($event['date'])->format('m') === $currentMonth;
            }
            $eventDate = Carbon::parse($event['date']);
            return $eventDate->month === $today->month && $eventDate->year === $today->year;
        })->sortBy(function ($event) {
            return Carbon::parse($event['date'])->format('d');
        })->values();
    }

    private function filterNext7DayEvents($allEvents, Carbon $today)
    {
        $next7 = $today->copy()->addDays(7);

        return $allEvents->filter(function ($event) use ($today, $next7) {
            if ($event['type'] === 'dob') {
                $dobThisYear = Carbon::parse($event['date'])->setYear($today->year);
                return $dobThisYear->between($today, $next7);
            }
            $eventDate = Carbon::parse($event['date']);
            return $eventDate->between($today, $next7);
        })->sortBy(function ($event) use ($today) {
            if ($event['type'] === 'dob') {
                return Carbon::parse($event['date'])->setYear($today->year)->format('Y-m-d');
            }
            return $event['date'];
        })->values();
    }

    private function getRecapVisits(bool $isAO, $user, Carbon $today)
    {
        $query = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year);

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function togglePromise(CustomerVisit $customerVisit)
    {
        $fulfilled = !$customerVisit->janji_bayar_fulfilled;

        $updateData = [
            'janji_bayar_fulfilled' => $fulfilled,
            'janji_bayar_fulfilled_at' => $fulfilled ? now() : null,
        ];

        // Save jumlah_pembayaran if provided during fulfillment
        if ($fulfilled && request()->has('jumlah_pembayaran')) {
            $updateData['jumlah_pembayaran'] = request()->jumlah_pembayaran;
        }

        $customerVisit->update($updateData);

        return response()->json([
            'success' => true,
            'fulfilled' => $fulfilled,
            'message' => $fulfilled ? 'Janji bayar ditandai lunas.' : 'Janji bayar dibatalkan.',
        ]);
    }

    public function recap()
    {
        $user = auth()->user();
        $isAO = !$user->can('view all data');

        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $query = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $visits = $query->orderBy('created_at', 'asc')->get();

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
        });

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('calendar.recap', [
            'recapData' => $recapData,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
            'isAO' => $isAO,
        ]);
    }
}
