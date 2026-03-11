<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerVisit;
use App\Models\WarningLetter;
use Illuminate\Http\Request;

class WarningLetterController extends Controller
{
    public function index()
    {
        if (auth()->user()->cannot('view warning-letters')) abort(403);
        return view('warning-letters.index');
    }

    public function create(Request $request)
    {
        if (auth()->user()->cannot('create warning-letters')) abort(403);

        $type = $request->get('type', 'sp1');
        $customerId = $request->get('customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;

        // Filter customers by qualification for the selected type
        $customers = $this->getQualifiedCustomers($type);

        $qualification = null;
        $previousLetter = null;

        if ($customer) {
            $qualification = $this->checkQualification($customer, $type);
            
            // For SP2/SP3, fetch the previous letter
            if (in_array($type, ['sp2', 'sp3'])) {
                $prevType = $type === 'sp2' ? 'sp1' : 'sp2';
                $previousLetter = WarningLetter::where('customer_id', $customer->id)
                    ->where('type', $prevType)
                    ->latest()
                    ->first();
            }
        }

        $generatedLetterNumber = WarningLetter::generateLetterNumber();

        return view('warning-letters.create', compact('type', 'customer', 'customers', 'qualification', 'previousLetter', 'generatedLetterNumber'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->cannot('create warning-letters')) abort(403);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:sp1,sp2,sp3,panggilan',
            'letter_number' => 'nullable|string|max:255',
            'letter_date' => 'required|date',
            'credit_agreement_number' => 'nullable|string|max:255',
            'credit_agreement_date' => 'nullable|date',
            'tunggakan_amount' => 'nullable|numeric',
            'tunggakan_date' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'previous_letter_id' => 'nullable|exists:warning_letters,id',
            'previous_letter_number' => 'nullable|string|max:255',
            'previous_letter_date' => 'nullable|date',
            'previous_letter_amount' => 'nullable|numeric',
            'previous_letter_deadline' => 'nullable|date',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        // Validate qualification
        $qualification = $this->checkQualification($customer, $request->type);
        if (!$qualification['qualified']) {
            return back()->withErrors(['qualification' => $qualification['reason']])->withInput();
        }

        // Get latest visit snapshot
        $latestVisit = CustomerVisit::where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->first();

        WarningLetter::create([
            'customer_id' => $customer->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'letter_number' => $request->letter_number ?: WarningLetter::generateLetterNumber(\Carbon\Carbon::parse($request->letter_date)),
            'letter_date' => $request->letter_date,
            'credit_agreement_number' => $request->credit_agreement_number,
            'credit_agreement_date' => $request->credit_agreement_date,
            'tunggakan_amount' => $request->tunggakan_amount,
            'tunggakan_date' => $request->tunggakan_date,
            'deadline_date' => $request->deadline_date,
            'kolektibilitas' => $latestVisit?->kolektibilitas,
            'penagihan_ke' => $latestVisit?->penagihan_ke,
            'notes' => $request->notes,
            'previous_letter_id' => $request->previous_letter_id,
            'previous_letter_number' => $request->previous_letter_number,
            'previous_letter_date' => $request->previous_letter_date,
            'previous_letter_amount' => $request->previous_letter_amount,
            'previous_letter_deadline' => $request->previous_letter_deadline,
        ]);

        return redirect()->route('warning-letters.index')
            ->with('success', 'Surat berhasil dibuat.');
    }

    public function edit($id)
    {
        if (auth()->user()->cannot('create warning-letters')) abort(403);

        $letter = WarningLetter::with('customer')->findOrFail($id);
        $customer = $letter->customer;
        $type = $letter->type;

        return view('warning-letters.edit', compact('letter', 'customer', 'type'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->cannot('create warning-letters')) abort(403);

        $letter = WarningLetter::findOrFail($id);

        $validated = $request->validate([
            'letter_date' => 'required|date',
            'credit_agreement_number' => 'nullable|string|max:255',
            'credit_agreement_date' => 'nullable|date',
            'tunggakan_amount' => 'nullable|numeric',
            'tunggakan_date' => 'nullable|date',
            'deadline_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $letter->update([
            'letter_date' => $request->letter_date,
            'credit_agreement_number' => $request->credit_agreement_number,
            'credit_agreement_date' => $request->credit_agreement_date,
            'tunggakan_amount' => $request->tunggakan_amount,
            'tunggakan_date' => $request->tunggakan_date,
            'deadline_date' => $request->deadline_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('warning-letters.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    public function show($id)
    {
        if (auth()->user()->cannot('view warning-letters')) abort(403);
        $letter = WarningLetter::with(['customer', 'user'])->findOrFail($id);

        // Check in subfolder first as per latest request
        $templatePath = storage_path("app/public/kop-surat/{$letter->type}.docx");
        
        // Fallback to old naming convention (e.g., kop-surat-sp1.docx)
        if (!file_exists($templatePath)) {
            $templatePath = storage_path("app/public/kop-surat-{$letter->type}.docx");
        }

        // Final fallback to default
        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/public/kop-surat.docx');
        }

        if (!file_exists($templatePath)) {
            abort(404, "Template untuk tipa surat '{$letter->type}' tidak ditemukan. Pastikan file .docx ada di storage/app/public/kop-surat/ atau storage/app/public/");
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Replace variables in the template
        $template->setValue('nomor_surat', $letter->letter_number ?? '___/BPR.PURI.KRD/___/____');
        $template->setValue('tanggal_surat_lengkap', formatIndonesianDate($letter->letter_date));
        
        $template->setValue('nama_nasabah', $letter->customer->name ?? '____');
        $template->setValue('alamat_nasabah', $letter->customer->address ?? '____');
        
        $template->setValue('jenis_surat', $letter->type_label);
        
        $template->setValue('nomor_pk', $letter->credit_agreement_number ?? '____');
        $template->setValue('tanggal_pk', formatIndonesianDate($letter->credit_agreement_date));
        
        $template->setValue('tanggal_tunggakan', $letter->tunggakan_date ? $letter->tunggakan_date->format('d-m-Y') : '____');
        $template->setValue('jumlah_tunggakan', $letter->tunggakan_amount ? number_format($letter->tunggakan_amount, 0, ',', '.') : '____');
        $template->setValue('terbilang', $letter->tunggakan_amount ? $this->terbilangRupiah($letter->tunggakan_amount) : '____ rupiah');
        
        $template->setValue('batas_waktu', formatIndonesianDate($letter->deadline_date));

        // Previous letter placeholders
        $template->setValue('nomor_surat_lama', $letter->previous_letter_number ?? '____');
        $template->setValue('tanggal_surat_lama', $letter->previous_letter_date ? formatIndonesianDate($letter->previous_letter_date) : '____');
        $template->setValue('jumlah_surat_lama', $letter->previous_letter_amount ? number_format($letter->previous_letter_amount, 0, ',', '.') : '____');
        $template->setValue('deadline_surat_lama', $letter->previous_letter_deadline ? formatIndonesianDate($letter->previous_letter_deadline) : '____');
        $template->setValue('terbilang_lama', $letter->previous_letter_amount ? $this->terbilangRupiah($letter->previous_letter_amount) : '____ rupiah');

        $fileName = "{$letter->type_short_label} - " . ($letter->customer->name ?? 'Nasabah') . ".docx";
        
        // Use a unique temp file in the system temp directory
        $tempFile = storage_path('app/public/') . uniqid('surat_') . '.docx';
        
        $template->saveAs($tempFile);

        // Ensure no output buffer is active before sending the file
        if (ob_get_length()) {
            ob_end_clean();
        }

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Check if customer qualifies for the given letter type
     */
    private function checkQualification(Customer $customer, string $type): array
    {
        switch ($type) {
            case 'sp1':
                // Must have penagihan > 3 with kolek 3/4/5
                $visits = CustomerVisit::where('customer_id', $customer->id)
                    ->whereIn('kolektibilitas', ['3', '4', '5'])
                    ->count();

                $totalVisits = CustomerVisit::where('customer_id', $customer->id)->count();

                if ($totalVisits < 3) {
                    return ['qualified' => false, 'reason' => 'Nasabah harus memiliki minimal 3 kali penagihan sebelumnya.'];
                }
                if ($visits === 0) {
                    return ['qualified' => false, 'reason' => 'Nasabah harus memiliki kolektibilitas Kurang Lancar, Diragukan, atau Macet.'];
                }
                // Check if SP1 already exists
                $existingSP1 = WarningLetter::where('customer_id', $customer->id)->where('type', 'sp1')->exists();
                if ($existingSP1) {
                    return ['qualified' => false, 'reason' => 'Nasabah sudah memiliki Surat Peringatan I.'];
                }
                return ['qualified' => true, 'reason' => 'Memenuhi syarat SP-1.'];

            case 'sp2':
                $sp1 = WarningLetter::where('customer_id', $customer->id)
                    ->where('type', 'sp1')
                    ->latest('letter_date')
                    ->first();

                if (!$sp1) {
                    return ['qualified' => false, 'reason' => 'Nasabah belum memiliki Surat Peringatan I.'];
                }

                $weeksSinceSP1 = $sp1->letter_date->diffInWeeks(now());
                if ($weeksSinceSP1 < 3) {
                    $daysLeft = 21 - $sp1->letter_date->diffInDays(now());
                    return ['qualified' => false, 'reason' => "Harus menunggu 3 minggu setelah SP-1. Sisa {$daysLeft} hari lagi."];
                }

                $existingSP2 = WarningLetter::where('customer_id', $customer->id)->where('type', 'sp2')->exists();
                if ($existingSP2) {
                    return ['qualified' => false, 'reason' => 'Nasabah sudah memiliki Surat Peringatan II.'];
                }
                return ['qualified' => true, 'reason' => 'Memenuhi syarat SP-2.'];

            case 'sp3':
                $sp2 = WarningLetter::where('customer_id', $customer->id)
                    ->where('type', 'sp2')
                    ->latest('letter_date')
                    ->first();

                if (!$sp2) {
                    return ['qualified' => false, 'reason' => 'Nasabah belum memiliki Surat Peringatan II.'];
                }

                $weeksSinceSP2 = $sp2->letter_date->diffInWeeks(now());
                if ($weeksSinceSP2 < 3) {
                    $daysLeft = 21 - $sp2->letter_date->diffInDays(now());
                    return ['qualified' => false, 'reason' => "Harus menunggu 3 minggu setelah SP-2. Sisa {$daysLeft} hari lagi."];
                }

                $existingSP3 = WarningLetter::where('customer_id', $customer->id)->where('type', 'sp3')->exists();
                if ($existingSP3) {
                    return ['qualified' => false, 'reason' => 'Nasabah sudah memiliki Surat Peringatan III.'];
                }
                return ['qualified' => true, 'reason' => 'Memenuhi syarat SP-3.'];

            case 'panggilan':
                return ['qualified' => true, 'reason' => 'Surat Panggilan (placeholder).'];

            default:
                return ['qualified' => false, 'reason' => 'Tipe surat tidak dikenal.'];
        }
    }

    /**
     * Get customers who qualify for the given letter type
     */
    private function getQualifiedCustomers(string $type)
    {
        switch ($type) {
            case 'sp1':
                // Customers with penagihan >= 3, kolek 3/4/5, and no existing SP1
                $customersWithSP1 = WarningLetter::where('type', 'sp1')->pluck('customer_id')->toArray();

                return Customer::whereHas('visits', function ($q) {
                        $q->whereIn('kolektibilitas', ['3', '4', '5']);
                    })
                    ->whereHas('visits', function ($q) {}, '>=', 3)
                    ->whereNotIn('id', $customersWithSP1)
                    ->orderBy('name')
                    ->get();

            case 'sp2':
                // Customers who have SP1 + 3 weeks passed, no existing SP2
                $customersWithSP2 = WarningLetter::where('type', 'sp2')->pluck('customer_id')->toArray();

                return Customer::whereHas('warningLetters', function ($q) {
                        $q->where('type', 'sp1')
                          ->where('letter_date', '<=', now()->subWeeks(3));
                    })
                    ->whereNotIn('id', $customersWithSP2)
                    ->orderBy('name')
                    ->get();

            case 'sp3':
                // Customers who have SP2 + 3 weeks passed, no existing SP3
                $customersWithSP3 = WarningLetter::where('type', 'sp3')->pluck('customer_id')->toArray();

                return Customer::whereHas('warningLetters', function ($q) {
                        $q->where('type', 'sp2')
                          ->where('letter_date', '<=', now()->subWeeks(3));
                    })
                    ->whereNotIn('id', $customersWithSP3)
                    ->orderBy('name')
                    ->get();

            case 'panggilan':
                return Customer::orderBy('name')->get();

            default:
                return collect();
        }
    }

    /**
     * Helper to convert number to words in Indonesian Rupiah
     */
    private function terbilangRupiah($angka)
    {
        if (!$angka || $angka == 0) return 'nol rupiah';
        return trim($this->terbilang($angka)) . ' rupiah';
    }

    private function terbilang($angka)
    {
        $angka = abs((float)$angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[(int)$angka];
        } else if ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . " puluh" . $this->terbilang(fmod($angka, 10));
        } else if ($angka < 200) {
            $terbilang = " seratus" . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . " ratus" . $this->terbilang(fmod($angka, 100));
        } else if ($angka < 2000) {
            $terbilang = " seribu" . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . " ribu" . $this->terbilang(fmod($angka, 1000));
        } else if ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . " juta" . $this->terbilang(fmod($angka, 1000000));
        } else if ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000) . " milyar" . $this->terbilang(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000000) . " trilyun" . $this->terbilang(fmod($angka, 1000000000000));
        }
        return $terbilang;
    }
}
