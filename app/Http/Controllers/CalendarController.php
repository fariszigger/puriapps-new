<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditDisbursement;
use App\Models\CustomerVisit;
use App\Models\WarningLetter;
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
        $warningLetterEvents = $this->getWarningLetterEvents($isAO, $user);
        $paydayEvents = $this->getPaydayEvents($isAO, $user);

        $today = Carbon::today();
        $allEvents = collect(array_merge($dobEvents, $visitEvents, $janjiBayarEvents, $warningLetterEvents, $paydayEvents));

        $thisMonthEvents = $this->filterThisMonthEvents($allEvents, $today);
        $next7Events = $this->filterNext7DayEvents($allEvents, $today);
        $recapVisits = $this->getRecapVisits($isAO, $user, $today);

        return view('calendar.index', [
            'dobEvents' => $dobEvents,
            'visitEvents' => $visitEvents,
            'janjiBayarEvents' => $janjiBayarEvents,
            'warningLetterEvents' => $warningLetterEvents,
            'paydayEvents' => $paydayEvents,
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
        $query = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
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
                'ao_code' => $visit->user->code ?? $visit->user->name ?? '-',
                'visit_id' => $visit->id,
            ];
        }

        return $events;
    }

    private function getJanjiBayarEvents(bool $isAO, $user): array
    {
        $query = CustomerVisit::with(['customer:id,name', 'user:id,name,code'])
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
                'ao_code' => $jb->user->code ?? $jb->user->name ?? '-',
                'visit_id' => $jb->id,
            ];
        }

        return $events;
    }

    private function getWarningLetterEvents(bool $isAO, $user): array
    {
        $query = WarningLetter::with(['customer:id,name', 'user:id,name,code'])
            ->select('id', 'customer_id', 'user_id', 'letter_date', 'type')
            ->whereIn('type', ['sp1', 'sp2']);

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $letters = $query->get();
        $events = [];

        foreach ($letters as $letter) {
            $followUpDate = $letter->letter_date->addDays(21);
            $typeLabel = $letter->type === 'sp1' ? 'SP-1' : 'SP-2';
            
            $events[] = [
                'id' => 'sp-' . $letter->id,
                'type' => 'sp',
                'date' => $followUpDate->format('Y-m-d'),
                'name' => $letter->customer->name ?? '-',
                'label' => 'Follow up ' . $typeLabel . ' - ' . ($letter->customer->name ?? '-'),
                'ao_code' => $letter->user->code ?? $letter->user->name ?? '-',
                'letter_id' => $letter->id,
                'letter_type' => $letter->type,
            ];
        }

        return $events;
    }

    private function getPaydayEvents(bool $isAO, $user): array
    {
        $query = CreditDisbursement::with('user:id,name,code')
            ->whereIn('status', ['aktif', 'Aktif', 'active', 'Active']);

        if ($isAO) {
            $query->where('user_id', $user->id);
        }

        $disbursements = $query->get();
        $events = [];
        $currentYear = Carbon::now()->year;

        foreach ($disbursements as $d) {
            $payDay = $d->disbursement_date->day;

            // Generate payday events for 12 months around current date
            for ($i = -1; $i <= 12; $i++) {
                $month = Carbon::now()->addMonths($i);
                $actualDay = min($payDay, $month->daysInMonth);
                $paydayDate = $month->copy()->day($actualDay);

                $events[] = [
                    'id' => 'payday-' . $d->id . '-' . $paydayDate->format('Y-m'),
                    'type' => 'payday',
                    'date' => $paydayDate->format('Y-m-d'),
                    'name' => $d->customer_name,
                    'label' => 'Jadwal Bayar - ' . $d->customer_name,
                    'angsuran' => $d->angsuran,
                    'ao_code' => $d->user->code ?? $d->user->name ?? '-',
                    'nomor_spk' => $d->nomor_spk,
                ];
            }
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
        })->sort(function ($a, $b) use ($today) {
            $dateA = $a['type'] === 'dob' ? Carbon::parse($a['date'])->setYear($today->year)->format('Y-m-d') : $a['date'];
            $dateB = $b['type'] === 'dob' ? Carbon::parse($b['date'])->setYear($today->year)->format('Y-m-d') : $b['date'];
            
            if ($dateA != $dateB) {
                return strcmp($dateA, $dateB);
            }
            
            $priority = [
                'janji_bayar' => 1,
                'payday' => 2,
                'sp' => 3,
                'visit' => 4,
                'dob' => 5
            ];
            
            $pA = $priority[$a['type']] ?? 9;
            $pB = $priority[$b['type']] ?? 9;
            
            return $pA <=> $pB;
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
        // If a new date is provided, it's a reschedule, not a fulfillment
        if (request()->has('tanggal_janji_baru') && request()->tanggal_janji_baru) {
            $customerVisit->update([
                'tanggal_janji_bayar' => request()->tanggal_janji_baru,
                'jumlah_pembayaran' => request()->has('jumlah_pembayaran') ? request()->jumlah_pembayaran : $customerVisit->jumlah_pembayaran,
                'janji_bayar_fulfilled' => false,
                'janji_bayar_fulfilled_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'rescheduled' => true,
                'message' => 'Janji bayar berhasil dijadwalkan ulang.',
            ]);
        }

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
                            'janji_bayar_fulfilled_at' => $visit->janji_bayar_fulfilled_at,
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
