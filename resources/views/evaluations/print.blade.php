<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak MPK - {{ $evaluation->customer->name }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            /* Margin removed so the browser's native Headers/Footers (Page numbers) can render. 
               Setting custom @page margins overrides and hides Chrome's native page numbering! */
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #000;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }

            thead.print-header {
                display: table-header-group;
            }

            tfoot.print-footer {
                display: none !important;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            background-color: #f3f4f6;
            /* Tailwind gray-100 */
        }

        .mpk-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #4b5563;
            /* Gray-600 */
        }

        .mpk-table th,
        .mpk-table td {
            border: 1px solid #4b5563;
            /* Gray-600 */
            padding: 3px 6px;
            vertical-align: top;
        }

        .nested-table td {
            border: none !important;
        }

        .mpk-header-bg {
            background-color: #9ca3af !important;
            /* Tailwind gray-400 */
            font-weight: bold;
        }

        .mpk-subheader-bg {
            background-color: #d1d5db !important;
            /* Tailwind gray-300 */
            font-weight: bold;
        }

        .content-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 10mm;
            min-height: 297mm;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }

        /* Screen-visible page break indicator */
        @media screen {
            .page-break {
                border-top: 2px dashed #9ca3af;
                margin: 20px 0;
                position: relative;
                height: 0;
            }

            .page-break::after {
                content: ' Batas Halaman ';
                position: absolute;
                top: -10px;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 0 12px;
                font-size: 10px;
                color: #9ca3af;
                font-style: italic;
            }
        }

        /* DRAFT Watermark */
        .draft-watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .draft-watermark-text {
            font-size: 120px;
            font-weight: 900;
            color: rgba(255, 0, 0, 0.12);
            transform: rotate(-45deg);
            white-space: nowrap;
            letter-spacing: 30px;
            text-transform: uppercase;
            user-select: none;
            font-family: Arial, Helvetica, sans-serif;
        }

        @media print {
            .draft-watermark {
                position: fixed;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    @if(($evaluation->approval_status ?? 'draft') === 'draft')
        <div class="draft-watermark">
            <span class="draft-watermark-text">DRAFT</span>
        </div>
    @elseif(($evaluation->approval_status ?? 'pending') === 'pending')
        <div class="draft-watermark">
            <span class="draft-watermark-text">PENDING</span>
        </div>
    @endif

    <!-- Print Button (Hidden on actual print) -->
    <div class="no-print p-4 bg-gray-100 border-b flex items-center justify-between fixed top-0 w-full z-50 shadow-sm">
        <div class="flex items-center gap-2 text-[11px] text-gray-500 italic">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Aktifkan <b>"Headers and footers"</b> di dialog print (More Settings) untuk nomor halaman
                otomatis</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('evaluations.index') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold">Kembali</a>
            <button onclick="window.print()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold">Cetak Dokumen</button>
        </div>
    </div>

    <div class="content-container mt-[70px] print:mt-0">
        <table class="w-full select-auto">
            <!-- HEADER (Repeats on every printed page) -->
            <thead class="print-header">
                <tr>
                    <td class="pt-4 print:pt-0">
                        <!-- Document Header Area -->
                        <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4 mt-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('build/assets/logobpr.png') }}" alt="BPR Puri Logo"
                                    class="h-10 w-auto object-contain">
                            </div>
                            <div class="text-[11px] text-right italic font-normal text-gray-500">
                                {{ $evaluation->application_id ?? '' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4 relative">
                            <div class="w-full text-center">
                                <h1 class="text-lg font-bold uppercase tracking-wider">MEMORANDUM PENGAJUAN KREDIT (MPK)
                                </h1>
                            </div>
                            @if($evaluation->office_branch)
                                <div class="absolute right-0 top-0 text-[9px] text-right leading-tight">
                                    <div class="border border-gray-800 px-2 py-1 rounded">
                                        <div class="text-[7px] text-gray-500 uppercase tracking-wider"></div>
                                        <div class="font-bold text-[10px]">{{ $evaluation->office_branch }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            </thead>

            <!-- MAIN CONTENT -->
            <tbody>
                <tr>
                    <td>
                        <!-- Info To/From -->
                        <div class="mb-4 text-xs flex justify-between items-start">
                            <div class="flex-1">
                                <table class="w-full ml-10">
                                    <tr>
                                        <td class="font-bold w-48 pb-1">Kepada</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1">Komite Kredit</td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Dari</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1"><span
                                                class="font-bold">{{ $evaluation->user->name ?? ' ' }}</span> (Account
                                            Officer)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Perihal</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1">Pengajuan Kredit Atas Nama <span
                                                class="font-bold">{{ $evaluation->customer->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Register Evaluasi</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1"><b>{{ $evaluation->application_id ?? 'NULL' }}</b></td>
                                    </tr>
                                </table>

                                <p class="ml-10 mt-1 text-[12px] text-justify">Sehubungan dengan Pengajuan kredit diatas
                                    dan setelah kami lakukan peninjauan lapangan serta analisa dari berbagai aspek, kami
                                    mohon dilakukan penilaian untuk selanjutnya mohon persetujuan.</p>
                            </div>

                            <!-- Foto Nasabah -->
                            @if($evaluation->customer->photo_path)
                                <div class="flex-shrink-0 ml-4 border border-gray-400 bg-white p-[2px]"
                                    style="width: 3cm; height: 4cm; margin-right: 1.5rem;">
                                    <img src="{{ route('media.customers', ['type' => 'photos', 'filename' => basename($evaluation->customer->photo_path)]) }}"
                                        alt="Foto Nasabah" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>

                        <!-- SECTION A -->
                        <h2 class="font-bold text-lg mb-2">A. IDENTITAS DEBITUR</h2>

                        <!-- Tbl Identitas -->
                        <table class="mpk-table mb-0 border-b-0 text-[10px]">
                            <tr>
                                <td class="mpk-header-bg w-1/4 text-xs uppercase" style="width: 25%;">IDENTITAS DIRI
                                </td>
                                <td class="mpk-header-bg text-xs uppercase border-l-2 border-r-2 border-black"
                                    style="width: 25%;"></td>
                                <td class="mpk-header-bg text-xs uppercase" style="width: 25%;">GOLONGAN DEBITUR</td>
                                <td class="mpk-header-bg font-bold text-xs border-l-0" style="width: 25%;">:
                                    {{ $evaluation->non_bank_third_party_code ?? '9000' }}
                                </td>
                            </tr>
                        </table>

                        <table class="mpk-table text-[10px] border-t-0">
                            <tr>
                                <td class="w-[25%] border-r-0 pb-2">Nama Calon Debitur</td>
                                <td class="w-[25%] pb-2">: {{ $evaluation->customer->name }}</td>

                                @php
                                    $birthDate = $evaluation->customer->dob ? \Carbon\Carbon::parse($evaluation->customer->dob) : null;
                                    $age = $birthDate ? $birthDate->age : 0;
                                @endphp

                                <td class="w-[25%] border-r-0 border-l border-gray-600 pb-2 pl-2">Usia</td>
                                <td class="w-[25%] pb-2">: {{ $age }} Tahun</td>
                            </tr>

                            <tr>
                                <td class="border-r-0 pb-1">Tempat, Tanggal Lahir</td>
                                <td class="pb-1">: {{ $evaluation->customer->pob }},
                                    {{ $birthDate ? $birthDate->format('d-M-Y') : '-' }}
                                </td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Pendidikan Terakhir</td>
                                <td class="pb-1">: {{ $evaluation->customer->education ?? ' ' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Nomor KTP</td>
                                <td class="pb-1">: {{ $evaluation->customer->identity_number }}</td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Status Perkawinan</td>
                                <td class="pb-1">: {{ $evaluation->customer->marital_status ?? ' ' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Nomor Telepon</td>
                                <td class="pb-1">: {{ $evaluation->customer->phone_number }}</td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Jumlah Tanggungan</td>
                                <td class="pb-1">: {{ $evaluation->customer_dependents ?? '-' }} Orang</td>
                            </tr>
                            <tr>
                            <tr>
                                <td class="border-r-0 pb-1">Kontak Darurat</td>
                                <td class="pb-1">: {{ $evaluation->customer->emergency_contact ?? '-' }}</td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Jenis Kelamin</td>
                                <td class="pb-1">: {{ $evaluation->customer->gender ?? 'Laki-Laki' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Pekerjaan</td>
                                <td class="pb-1">: {{ $evaluation->customer->job ?? '-' }}</td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Nama Ibu Kandung</td>
                                <td class="pb-1">: {{ $evaluation->customer->mother_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Keterangan</td>
                                <td colspan="3" class="pb-1">: {{ $evaluation->customer_profile ?? '-' }}</td>
                            </tr>
                            @if($evaluation->customer->marital_status == 'Menikah')
                                <!-- DATA ISTRI -->
                                <tr>
                                    <td colspan="4"
                                        class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-b-2 border-gray-600">
                                        DATA @if($evaluation->customer->gender == 'Laki-Laki') SUAMI @else ISTRI @endif</td>
                                </tr>

                                @php
                                    $spouseBirthDate = $evaluation->customer->spouse_dob ? \Carbon\Carbon::parse($evaluation->customer->spouse_dob) : null;
                                    $spouseAge = $spouseBirthDate ? $spouseBirthDate->age : 0;
                                @endphp

                                <tr>
                                    <td class="border-r-0 pb-1">Nama @if($evaluation->customer->gender == 'Laki-Laki') Suami
                                    @else Istri @endif</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_name ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Usia</td>
                                    <td class="pb-1">: {{ $spouseAge ? $spouseAge . ' Tahun' : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Tempat, Tanggal Lahir</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_pob ?? '-' }},
                                        {{ $spouseBirthDate ? $spouseBirthDate->format('d-M-Y') : '-' }}
                                    </td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Nomor KTP</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_identity_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Pendidikan Terakhir</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_education ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Nomor Telepon</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_notelp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Pekerjaan</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_job ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Keterangan</td>
                                    <td class="pb-1">: {{ $evaluation->customer->spouse_description ?? '-' }}</td>
                                </tr>
                            @else

                            @endif
                            <!-- FASILITAS KREDIT DIMOHON -->
                            <tr>
                                <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                    FASILITAS KREDIT DIMOHON</td>
                            </tr>
                            @php
                                // Inputs
                                $P = $evaluation->loan_amount ?? 0; // principal
                                $n = $evaluation->loan_term_months ?? 0; // term in months
                                $annualRate = $evaluation->loan_interest_rate ?? 0; // percent per year

                                // Normalize type (case-insensitive)
                                $typeRaw = $evaluation->loan_type ?? '';
                                $type = strtolower(trim($typeRaw));

                                // Convert annual percent to monthly decimal
                                $monthlyRate = ($annualRate / 100) / 12;
                                $formula = 0;

                                if ($P > 0 && $n > 0) {
                                    if ($type === 'pinjaman angsuran') {
                                        // Annuity payment: A = P * r / (1 - (1+r)^-n)
                                        if ($monthlyRate > 0) {
                                            $formula = ($P * $monthlyRate) + ($P / $n);
                                        } else {
                                            // Zero interest -> equal principal installments
                                            $formula = $P / $n;
                                        }
                                    } elseif ($type === 'pinjaman musiman') {
                                        // Interest-only per month: pay only interest each period
                                        $formula = $P * $monthlyRate;
                                    } else {
                                        // Shouldn't happen given your values, but keep a safe fallback
                                        $formula = 'Rumus Ada yang Salah';
                                    }
                                }
                            @endphp
                            <tr>
                                <td colspan="4" class="p-0 border-0">
                                    <table class="w-full text-[10px] m-0 border-0 border-collapse nested-table">
                                        <tr>
                                            <td class="w-[25%] border-b-0 border-l-0 border-t-0 border-r-0 py-2 pl-2">
                                                Jumlah Kredit</td>
                                            <td class="w-[25%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">: <span
                                                    class="font-bold">Rp
                                                    {{ number_format($evaluation->loan_amount ?? 0, 2, ',', '.') }}</span>
                                            </td>
                                            <td class="w-[20%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">Jangka
                                                Waktu / Bunga</td>
                                            <td class="w-[30%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">: <span
                                                    class="font-bold">{{ $evaluation->loan_term_months ?? 0 }} Bulan /
                                                    {{ $evaluation->loan_interest_rate ?? 0 }} % (p.a)</span></td>
                                        </tr>
                                        <tr>
                                            <td class="w-[25%] border-0 py-1 pl-2">Angsuran</td>
                                            <td class="border-0 py-1">: <span class="font-bold">Rp
                                                    {{ number_format($formula ?? 0, 2, ',', '.') }}</span></td>
                                            <td class="border-0 py-1">Sifat Pinjaman</td>
                                            <td class="border-0 py-1">: {{ $evaluation->loan_type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="w-[25%] border-0 py-1 pl-2 pb-2">Tujuan
                                                {{$evaluation->loan_scheme ?? '-'}}
                                            </td>
                                            <td class="border-0 py-1 pb-2">: {{ $evaluation->loan_purpose ?? '-' }}</td>
                                            @if($evaluation->loan_type === 'pinjaman musiman')
                                                <td class="border-0 py-1">Sumber Pelunasan</td>
                                                <td class="border-0 py-1">: {{ $evaluation->loan_repayment_source ?? '-' }}
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            @if($evaluation->customer_status === 'Nasabah Lama')
                                <!-- FASILITAS KREDIT YANG PERNAH DITERIMA -->
                                <tr>
                                    <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-gray-600">FASILITAS
                                        KREDIT YANG PERNAH DITERIMA</td>
                                </tr>
                                @php
                                    // Inputs
                                    $P = $evaluation->old_loan_amount ?? 0; // principal
                                    $n = $evaluation->old_loan_term_months ?? 0; // term in months
                                    $annualRate = $evaluation->old_loan_interest_rate ?? 0; // percent per year

                                    // Normalize type (case-insensitive)
                                    $typeRaw = $evaluation->old_loan_type ?? '';
                                    $type = strtolower(trim($typeRaw));

                                    // Convert annual percent to monthly decimal
                                    $monthlyRate = ($annualRate / 100) / 12;
                                    $formula_old_loan = 0;

                                    if ($P > 0 && $n > 0) {
                                        if ($type === 'pinjaman angsuran') {
                                            // Annuity payment: A = P * r / (1 - (1+r)^-n)
                                            if ($monthlyRate > 0) {
                                                $formula_old_loan = ($P * $monthlyRate) + ($P / $n);
                                            } else {
                                                // Zero interest -> equal principal installments
                                                $formula_old_loan = $P / $n;
                                            }
                                        } elseif ($type === 'pinjaman musiman') {
                                            // Interest-only per month: pay only interest each period
                                            $formula_old_loan = $P * $monthlyRate;
                                        } else {
                                            // Shouldn't happen given your values, but keep a safe fallback
                                            $formula_old_loan = 'Rumus Ada yang Salah';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td colspan="4" class="p-0 border-0">
                                        <table class="w-full text-[10px] m-0 border-0 border-collapse nested-table">
                                            <tr>
                                                <td class="w-[25%] border-b-0 border-l-0 border-t-0 border-r-0 py-2 pl-2">
                                                    Pinjaman Sebelumnya</td>
                                                <td class="w-[25%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">: <span
                                                        class="font-bold">Rp
                                                        {{ number_format($evaluation->old_loan_amount ?? 0, 2, ',', '.') }}</span>
                                                </td>
                                                <td class="w-[20%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">Jangka
                                                    Waktu / Bunga</td>
                                                <td class="w-[30%] border-b-0 border-l-0 border-t-0 border-r-0 py-2">: <span
                                                        class="font-bold">{{ $evaluation->old_loan_term_months ?? '-' }}
                                                        Bulan / {{ $evaluation->old_loan_interest_rate ?? '-' }} %
                                                        (p.a)</span></td>
                                            </tr>
                                            <tr>
                                                <td class="w-[25%] border-0 py-1 pl-2">Angsuran</td>
                                                <td class="w-[25%] border-0 py-1">: <span class="font-bold">Rp
                                                        {{ number_format($formula_old_loan ?? 0, 2, ',', '.') }}</span></td>
                                                <td class="w-[20%] border-0 py-1">Sifat Pinjaman</td>
                                                <td class="w-[30%] border-0 py-1">: {{ $evaluation->old_loan_type ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="w-[25%] border-0 py-1 pl-2 pb-2">Tujuan Penggunaan</td>
                                                <td class="border-0 py-1 pb-2">: {{ $evaluation->old_loan_purpose ?? '-' }}
                                                </td>
                                                @if($evaluation->old_loan_type === 'pinjaman musiman')
                                                    <td class="border-0 py-1">Sumber Pelunasan</td>
                                                    <td class="border-0 py-1">:
                                                        {{ $evaluation->old_loan_repayment_source ?? '-' }}
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endif

                            <!-- LOKASI USAHA / RUMAH -->
                            @if($evaluation->customer->location_image_path || ($evaluation->customer->latitude && $evaluation->customer->longitude) || $evaluation->customer->address)
                                <tr>
                                    <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                        LOKASI RUMAH</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-2 border-0">
                                        <div class="flex gap-4 items-start w-full">
                                            <!-- Map Details -->
                                            <div class="flex-1 text-[10px]">
                                                <table class="w-full text-left nested-table m-0">
                                                    @if($evaluation->customer->address)
                                                        <tr>
                                                            <td class="w-[30%] py-1 pl-2 font-bold align-top">Alamat</td>
                                                            <td class="py-1">: {{ $evaluation->customer->address }}<br>
                                                                &nbsp; Desa/Kel: {{ $evaluation->customer->village }}, Kec:
                                                                {{ $evaluation->customer->district }}<br>
                                                                &nbsp; Kab/Kota: {{ $evaluation->customer->regency }}, Prov:
                                                                {{ $evaluation->customer->province }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="w-[30%] py-1 pl-2 font-bold">Koordinat</td>
                                                        <td class="py-1">: {{ $evaluation->customer->latitude ?? '-' }},
                                                            {{ $evaluation->customer->longitude ?? '-' }} (Jarak dari
                                                            {{ $evaluation->office_branch ?? 'Kantor Pusat' }} :
                                                            {{ $evaluation->customer->path_distance ?? '-' }}km)
                                                        </td>
                                                    </tr>
                                                    @if($evaluation->customer->latitude && $evaluation->customer->longitude)
                                                        <tr>
                                                            <td class="w-[30%] py-1 pl-2 font-bold align-top">Tautan Peta</td>
                                                            <td class="py-1">: <span
                                                                    class="text-blue-600 break-all text-[8px]">https://www.google.com/maps/search/?api=1&query={{ $evaluation->customer->latitude }},{{ $evaluation->customer->longitude }}</span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>

                                            <!-- Map Image & QR Code (Compact) -->
                                            <div class="flex gap-2">
                                                @if($evaluation->customer->location_image_path)
                                                    <div
                                                        class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden relative">
                                                        <img src="{{ route('media.customers', ['type' => 'map', 'filename' => basename($evaluation->customer->location_image_path)]) }}"
                                                            alt="Peta Lokasi" class="w-full h-full object-cover">
                                                    </div>
                                                @endif

                                                @if($evaluation->customer->latitude && $evaluation->customer->longitude)
                                                    <div class="border border-gray-400 p-[2px] bg-white w-24 h-24">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $evaluation->customer->latitude . ',' . $evaluation->customer->longitude) }}"
                                                            alt="QR Code Lokasi" class="w-full h-full object-contain">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <!-- PINJAMAN DI BANK LAIN (SLIK) -->
                            <tr>
                                <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-gray-600">PINJAMAN DI
                                    BANK LAIN</td>
                            </tr>
                        </table>

                        <table class="mpk-table text-[9px] text-center border-t-0 mt-0">
                            <tr class="bg-gray-50 font-bold">
                                <td class="py-1">No.</td>
                                <td class="py-1">Nama Bank</td>
                                <td class="py-1">Kol</td>
                                <td class="py-1">Baki Debet</td>
                                <td class="py-1">Realisasi</td>
                                <td class="py-1">Jatuh Tempo</td>
                                <td class="py-1">Angsuran</td>
                            </tr>

                            @php
                                $totalBakiDebet = 0;
                                $totalAngsuran = 0;
                            @endphp

                            @forelse ($evaluation->externalLoans ?? [] as $index => $loan)
                                @php
                                    $totalBakiDebet += (float) $loan->outstanding_balance;
                                    $totalAngsuran += (float) $loan->installment_amount;

                                    $realDate = $loan->realization_date ? \Carbon\Carbon::parse($loan->realization_date)->format('d M Y') : '-';
                                    $matDate = $loan->maturity_date ? \Carbon\Carbon::parse($loan->maturity_date)->format('d M Y') : '-';

                                    $kolClass = '';
                                    $kol = 'L'; // default
                                    if (stripos($loan->collectibility, 'Macet') !== false || stripos($loan->collectibility, 'Diragukan') !== false || stripos($loan->collectibility, 'Kurang Lancar') !== false) {
                                        $kol = 'M';
                                        $kolClass = 'text-red-500 font-bold';
                                    } elseif (stripos($loan->collectibility, 'Lancar') !== false) {
                                        $kol = 'L';
                                    } else {
                                        $kol = substr($loan->collectibility, 0, 1) ?: '-';
                                    }
                                @endphp
                                <tr>
                                    <td class="py-1">{{ $index + 1 }}</td>
                                    <td class="py-1 text-left">{{ $loan->bank_name ?? '-' }}</td>
                                    <td class="py-1 {{ $kolClass }}">{{ $kol }}</td>
                                    <td class="py-1 text-right">Rp
                                        {{ number_format($loan->outstanding_balance ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="py-1">{{ $realDate }}</td>
                                    <td class="py-1">{{ $matDate }}</td>
                                    <td class="py-1 text-right">Rp
                                        {{ number_format($loan->installment_amount ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500 italic">Tidak ada data pinjaman di
                                        bank lain.</td>
                                </tr>
                            @endforelse

                            <tr class="font-bold bg-white">
                                <td colspan="3" class="py-2 text-center">Total Bakidebet</td>
                                <td class="py-2 text-right">Rp {{ number_format($totalBakiDebet, 2, ',', '.') }}</td>
                                <td colspan="2" class="py-2 text-center">Total Angsuran</td>
                                <td class="py-2 text-right">Rp {{ number_format($totalAngsuran, 2, ',', '.') }}</td>
                            </tr>
                        </table>

                        <!-- PAGE BREAK after Pinjaman Bank Lain -->
                        <div class="page-break"></div>

                        <!-- DATA USAHA (Entrepreneurs Data) -->
                        <h2 class="font-bold text-lg mb-2 mt-4">B. DATA PEKERJAAN & USAHA</h2>

                        <table class="mpk-table text-[10px] mb-0">
                            <tr>
                                <td colspan="4" class="mpk-header-bg text-xs uppercase py-1">DATA PEKERJAAN</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Status Usaha</td>
                                <td class="w-[25%] pb-1">: {{ $evaluation->customer_entrepreneurship_status ?? '-' }}
                                </td>
                                <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">Status Kepegawaian</td>
                                <td class="w-[25%] pb-1">: {{ $evaluation->customer_employment_status ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Profil Singkat</td>
                                <td colspan="3" class="pb-1 text-justify">: {{ $evaluation->customer_profile ?? '-' }}
                                </td>
                            </tr>

                            @if($evaluation->customer_entrepreneurship_status === 'Wirausaha')
                                <!-- LEGALITAS & DETAIL USAHA -->
                                <tr>
                                    <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                        LEGALITAS & DETAIL USAHA</td>
                                </tr>
                                <tr>
                                    <td class="w-[25%] border-r-0 pb-1">Legalitas</td>
                                    <td class="w-[25%] pb-1">: {{ $evaluation->customer_entreprenuership_legality ?? '-' }}
                                    </td>
                                    <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">Kepemilikan</td>
                                    <td class="w-[25%] pb-1">: {{ $evaluation->customer_entreprenuership_ownership ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Nama Usaha</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_name ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Jenis Usaha</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Tahun Berdiri</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_year ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Produk yang Dijual</td>
                                    <td class="pb-1 text-justify">:
                                        {{ $evaluation->customer_entreprenuership_products ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Jumlah Karyawan</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_employee_count ?? '-' }}
                                    </td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Status Tempat</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_place_status ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Nomor Telepon</td>
                                    <td class="pb-1">: {{ $evaluation->customer_entreprenuership_phone ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Deskripsi Usaha</td>
                                    <td class="pb-1 text-justify">:
                                        {{ $evaluation->customer_entreprenuership_description ?? '-' }}
                                    </td>
                                </tr>

                                @if($evaluation->customer_entreprenuership_legality === 'Berbadan Usaha')
                                    <tr>
                                        <td class="border-r-0 pb-1">NPWP</td>
                                        <td class="pb-1">: {{ $evaluation->customer_entreprenuership_tax_id ?? '-' }}</td>
                                        <td class="border-r-0 border-l border-gray-600 pb-1">Surat Izin Usaha</td>
                                        <td class="pb-1">: {{ $evaluation->customer_entreprenuership_legality_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border-r-0 pb-1">Tanda Daftar Usaha</td>
                                        <td colspan="3" class="pb-1">:
                                            {{ $evaluation->customer_entreprenuership_legality_register_id ?? '-' }}
                                        </td>
                                    </tr>
                                @endif

                                <!-- FOTO USAHA -->
                                @if($evaluation->business_legality_path || $evaluation->business_detail_1_path || $evaluation->business_detail_2_path)
                                    <tr>
                                        <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                            FOTO USAHA</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="p-2">
                                            <div class="flex gap-3 justify-center">
                                                @if($evaluation->business_legality_path)
                                                    <div class="text-center">
                                                        <div
                                                            class="border border-gray-400 p-[2px] bg-white w-48 h-32 overflow-hidden">
                                                            <img src="{{ route('media.evaluations', ['type' => 'photos', 'filename' => $evaluation->business_legality_path]) }}"
                                                                alt="Foto Legalitas Usaha" class="w-full h-full object-cover">
                                                        </div>
                                                        <p class="text-[8px] text-gray-500 mt-1">Foto Usaha 1</p>
                                                    </div>
                                                @endif
                                                @if($evaluation->business_detail_1_path)
                                                    <div class="text-center">
                                                        <div
                                                            class="border border-gray-400 p-[2px] bg-white w-48 h-32 overflow-hidden">
                                                            <img src="{{ route('media.evaluations', ['type' => 'photos', 'filename' => $evaluation->business_detail_1_path]) }}"
                                                                alt="Foto Detail Usaha 1" class="w-full h-full object-cover">
                                                        </div>
                                                        <p class="text-[8px] text-gray-500 mt-1">Foto Usaha 2</p>
                                                    </div>
                                                @endif
                                                @if($evaluation->business_detail_2_path)
                                                    <div class="text-center">
                                                        <div
                                                            class="border border-gray-400 p-[2px] bg-white w-48 h-32 overflow-hidden">
                                                            <img src="{{ route('media.evaluations', ['type' => 'photos', 'filename' => $evaluation->business_detail_2_path]) }}"
                                                                alt="Foto Detail Usaha 2" class="w-full h-full object-cover">
                                                        </div>
                                                        <p class="text-[8px] text-gray-500 mt-1">Foto Usaha 3</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                <!-- LOKASI USAHA -->
                                @if($evaluation->business_location_image_path || ($evaluation->business_latitude && $evaluation->business_longitude))
                                    <tr>
                                        <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                            LOKASI TEMPAT USAHA</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="p-2 border-0">
                                            <div class="flex gap-4 items-start w-full">
                                                <div class="flex-1 text-[10px]">
                                                    <table class="w-full text-left nested-table m-0">
                                                        <tr>
                                                            <td class="w-[30%] py-1 pl-2 font-bold">Koordinat</td>
                                                            <td class="py-1">: {{ $evaluation->business_latitude ?? '-' }},
                                                                {{ $evaluation->business_longitude ?? '-' }} (Jarak dari
                                                                {{ $evaluation->office_branch ?? 'Kantor Pusat' }} :
                                                                {{ $evaluation->path_distance ?? '-' }}km)
                                                            </td>
                                                        </tr>
                                                        @if($evaluation->business_village || $evaluation->business_district || $evaluation->business_regency || $evaluation->business_province)
                                                            <tr>
                                                                <td class="w-[30%] py-1 pl-2 font-bold align-top">Lokasi</td>
                                                                <td class="py-1">:
                                                                    Desa/Kel: {{ $evaluation->business_village ?? '-' }}, Kec:
                                                                    {{ $evaluation->business_district ?? '-' }}<br>
                                                                    &nbsp; Kab/Kota: {{ $evaluation->business_regency ?? '-' }},
                                                                    Prov: {{ $evaluation->business_province ?? '-' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if($evaluation->business_latitude && $evaluation->business_longitude)
                                                            <tr>
                                                                <td class="w-[30%] py-1 pl-2 font-bold align-top">Tautan Peta</td>
                                                                <td class="py-1">: <span
                                                                        class="text-blue-600 break-all text-[8px]">https://www.google.com/maps/search/?api=1&query={{ $evaluation->business_latitude }},{{ $evaluation->business_longitude }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </div>

                                                <div class="flex gap-2">
                                                    @if($evaluation->business_location_image_path)
                                                        <div
                                                            class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden relative">
                                                            <img src="{{ route('media.evaluations', ['type' => 'map', 'filename' => $evaluation->business_location_image_path]) }}"
                                                                alt="Peta Lokasi Usaha" class="w-full h-full object-cover">
                                                        </div>
                                                    @endif

                                                    @if($evaluation->business_latitude && $evaluation->business_longitude)
                                                        <div class="border border-gray-400 p-[2px] bg-white w-24 h-24">
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $evaluation->business_latitude . ',' . $evaluation->business_longitude) }}"
                                                                alt="QR Code Lokasi Usaha" class="w-full h-full object-contain">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endif

                            <!-- DATA KEPEGAWAIAN (Employment Information) -->
                            @if($evaluation->customer_employment_status && $evaluation->customer_employment_status !== 'Bukan Karyawan')
                                <tr>
                                    <td colspan="4" class="mpk-header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                        DATA KEPEGAWAIAN / INSTANSI</td>
                                </tr>
                                <tr>
                                    <td class="w-[25%] border-r-0 pb-1">Nama Instansi/Perusahaan</td>
                                    <td colspan="3" class="pb-1">: {{ $evaluation->customer_company_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Alamat Instansi/Perusahaan</td>
                                    <td colspan="3" class="pb-1">: {{ $evaluation->customer_company_address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Jabatan</td>
                                    <td class="pb-1">: {{ $evaluation->customer_company_position ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Lama Bekerja</td>
                                    <td class="pb-1">: {{ $evaluation->customer_company_years ?? '-' }} Tahun</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Status Kepegawaian</td>
                                    <td class="pb-1">: {{ $evaluation->customer_employee_status ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Jumlah Karyawan</td>
                                    <td class="pb-1">: {{ $evaluation->customer_company_employee_count ?? '-' }} Orang</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Sektor Usaha</td>
                                    <td class="pb-1 text-justify">: {{ $evaluation->customer_company_sector ?? '-' }}</td>
                                    <td class="border-r-0 border-l border-gray-600 pb-1">Nomor Telepon Kantor</td>
                                    <td class="pb-1">: {{ $evaluation->customer_company_phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border-r-0 pb-1">Tanggal Gajian</td>
                                    <td colspan="3" class="pb-1">:
                                        {{ $evaluation->customer_company_salary_frequency ?? '-' }}
                                        @if ($evaluation->customer_company_salary_frequency === 'Harian')

                                        @else
                                            setiap tanggal
                                            {{ $evaluation->customer_company_payday ? \Carbon\Carbon::parse($evaluation->customer_company_payday)->format('d') : '-' }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>

                        <!-- PAGE BREAK before Cashflow -->
                        <div class="page-break"></div>

                        <!-- SECTION C: CASHFLOW -->
                        <h2 class="font-bold text-lg mb-2 mt-4">C. ARUS KAS (CASHFLOW)</h2>

                        @php
                            // Opening Balance
                            $openingCash = $evaluation->kas_usaha ?? 0;
                            $openingSavings = $evaluation->piutang_usaha ?? 0;
                            $openingGiro = $evaluation->persediaan ?? 0;
                            $openingBalance = $openingCash + $openingSavings + $openingGiro;

                            // Saldo Awal Operasional
                            $opOpeningBefore = $evaluation->op_opening_balance_before ?? 0;
                            $opOpeningAfter = $evaluation->op_opening_balance_after ?? 0;

                            // Cash In
                            $salaryBefore = $evaluation->cash_in_salary_before ?? 0;
                            $salaryAfter = $evaluation->cash_in_salary_after ?? 0;
                            $businessInBefore = $evaluation->cash_in_business_before ?? 0;
                            $businessInAfter = $evaluation->cash_in_business_after ?? 0;
                            $otherInBefore = $evaluation->cash_in_other_before ?? 0;
                            $otherInAfter = $evaluation->cash_in_other_after ?? 0;
                            $capitalInjection = $evaluation->capital_injection_amount ?? 0;

                            // Other incomes detail (JSON)
                            $otherIncomesData = $evaluation->cash_in_other_details ? json_decode($evaluation->cash_in_other_details, true) : [];

                            $cashInTotalBefore = $salaryBefore + $businessInBefore + $otherInBefore;
                            $cashInTotalAfter = $salaryAfter + $businessInAfter + $otherInAfter + $capitalInjection;

                            // Household Expenses
                            $hhFields = [
                                'Biaya Hidup' => 'hh_living',
                                'Listrik & Air' => 'hh_utilities',
                                'Pendidikan' => 'hh_education',
                                'Telekomunikasi' => 'hh_telecom',
                                'Transportasi' => 'hh_transport',
                                'Hiburan' => 'hh_entertainment',
                                'Sewa' => 'hh_rent',
                                'Lainnya' => 'hh_other',
                            ];
                            $hhTotalBefore = 0;
                            $hhTotalAfter = 0;
                            foreach ($hhFields as $label => $field) {
                                $hhTotalBefore += $evaluation->{$field . '_before'} ?? 0;
                                $hhTotalAfter += $evaluation->{$field . '_after'} ?? 0;
                            }

                            // Business Expenses
                            $bizFields = [
                                'HPP / Bahan Baku' => 'biz_hpp',
                                'Tenaga Kerja' => 'biz_labor',
                                'Telekomunikasi' => 'biz_telecom',
                                'Transportasi' => 'biz_transport',
                                'Listrik & Air' => 'biz_utilities',
                                'Sewa' => 'biz_rent',
                                'Lainnya' => 'biz_other',
                            ];
                            $bizTotalBefore = 0;
                            $bizTotalAfter = 0;
                            foreach ($bizFields as $label => $field) {
                                $bizTotalBefore += $evaluation->{$field . '_before'} ?? 0;
                                $bizTotalAfter += $evaluation->{$field . '_after'} ?? 0;
                            }

                            $otherExpBefore = $evaluation->other_expenses_before ?? 0;
                            $otherExpAfter = $evaluation->other_expenses_after ?? 0;

                            // Bank installments from external loans
                            $bankInstallmentsBefore = $totalAngsuran ?? 0;
                            $bankInstallmentsAfter = $totalAngsuran ?? 0;

                            $cashOutTotalBefore = $bankInstallmentsBefore + $hhTotalBefore + $bizTotalBefore + $otherExpBefore;
                            $cashOutTotalAfter = $bankInstallmentsAfter + $hhTotalAfter + $bizTotalAfter + $otherExpAfter;

                            // Net Cash Flow
                            $netCashFlowBefore = $cashInTotalBefore - $cashOutTotalBefore;
                            $netCashFlowAfter = $cashInTotalAfter - $cashOutTotalAfter;

                            // Ending Balance
                            $endOpBalanceBefore = $opOpeningBefore + $netCashFlowBefore;
                            $endOpBalanceAfter = $opOpeningAfter + $netCashFlowAfter;

                            // RPC
                            $rpcRatio = $evaluation->rpc_ratio ?? 0;
                            $rpcTotalBefore = $rpcRatio > 0 ? round($netCashFlowBefore * $rpcRatio / 100) : 0;
                        @endphp

                        <table class="mpk-table text-[9px] mb-0">
                            <!-- Header -->
                            <tr>
                                <td class="mpk-header-bg text-xs py-1" style="width: 40%;">Keterangan Arus Kas</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Sebelum Pencairan
                                </td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Setelah Pencairan
                                </td>
                            </tr>

                            <!-- SALDO & KAS USAHA -->
                            <tr class="font-bold bg-gray-50">
                                <td class="py-1 pl-2">SALDO & KAS USAHA</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($openingBalance, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2"></td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Kas Usaha</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($openingCash, 0, ',', '.') }}</td>
                                <td class="py-1"></td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Piutang Usaha</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($openingSavings, 0, ',', '.') }}
                                </td>
                                <td class="py-1"></td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Persediaan</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($openingGiro, 0, ',', '.') }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- a. Saldo Awal Operasional -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1 pl-2">a. Saldo Awal Operasional</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($opOpeningBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($opOpeningAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- b. Arus Kas Masuk -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1 pl-2">b. Arus Kas Masuk (Income)</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($cashInTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($cashInTotalAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Gaji</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($salaryBefore, 0, ',', '.') }}</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($salaryAfter, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Pendapatan Usaha</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($businessInBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($businessInAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if(!empty($otherIncomesData))
                                @foreach($otherIncomesData as $oi)
                                    <tr>
                                        <td class="py-1 pl-6">{{ $oi['name'] ?? 'Pendapatan Lain' }}</td>
                                        <td class="py-1 text-right pr-2">Rp {{ number_format($oi['before'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-1 text-right pr-2">Rp {{ number_format($oi['after'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td class="py-1 pl-6">Suntikan Modal</td>
                                <td class="py-1 text-right pr-2"></td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($capitalInjection, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- c. Arus Kas Keluar -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1 pl-2">c. Arus Kas Keluar</td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($cashOutTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($cashOutTotalAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Angsuran Bank Lain -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Angsuran Bank Lain</td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($bankInstallmentsBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($bankInstallmentsAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Beban Rumah Tangga -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Beban Rumah Tangga</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($hhTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($hhTotalAfter, 0, ',', '.') }}</td>
                            </tr>
                            @foreach($hhFields as $label => $field)
                                <tr>
                                    <td class="py-1 pl-8">{{ $label }}</td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_before'} ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_after'} ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Beban Usaha -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Beban Usaha</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($bizTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($bizTotalAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            @foreach($bizFields as $label => $field)
                                <tr>
                                    <td class="py-1 pl-8">{{ $label }}</td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_before'} ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_after'} ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Beban Lainnya -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Beban Lainnya</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherExpBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherExpAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- d. Arus Kas Bersih -->
                            <tr class="font-bold" style="background-color: #1f2937 !important; color: white;">
                                <td class="py-1.5 pl-2">d. Arus Kas Bersih (b - c)</td>
                                <td class="py-1.5 text-right pr-2 {{ $netCashFlowBefore < 0 ? 'text-red-300' : '' }}">
                                    Rp
                                    {{ $netCashFlowBefore < 0 ? '(' . number_format(abs($netCashFlowBefore), 0, ',', '.') . ')' : number_format($netCashFlowBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2 {{ $netCashFlowAfter < 0 ? 'text-red-300' : '' }}">
                                    Rp
                                    {{ $netCashFlowAfter < 0 ? '(' . number_format(abs($netCashFlowAfter), 0, ',', '.') . ')' : number_format($netCashFlowAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- e. Saldo Akhir Operasional -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1.5 pl-2">e. Saldo Akhir Operasional (a + d)</td>
                                <td class="py-1.5 text-right pr-2 {{ $endOpBalanceBefore < 0 ? 'text-red-600' : '' }}">
                                    Rp
                                    {{ $endOpBalanceBefore < 0 ? '(' . number_format(abs($endOpBalanceBefore), 0, ',', '.') . ')' : number_format($endOpBalanceBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2 {{ $endOpBalanceAfter < 0 ? 'text-red-600' : '' }}">
                                    Rp
                                    {{ $endOpBalanceAfter < 0 ? '(' . number_format(abs($endOpBalanceAfter), 0, ',', '.') . ')' : number_format($endOpBalanceAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- RPC -->
                            <tr class="font-bold">
                                <td class="py-1.5 pl-2">RPC (Repayment Capacity) {{ $rpcRatio }}%</td>
                                <td class="py-1.5 text-right pr-2">Rp {{ number_format($rpcTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2"></td>
                            </tr>
                        </table>

                        <!-- LABA / RUGI (Income Statement) -->
                        @php
                            // a. Total Pendapatan
                            $totalPendapatanBefore = $cashInTotalBefore;
                            $totalPendapatanAfter = $cashInTotalAfter;

                            // Realization cost (f1) - only before
                            $loanTotalCostPrint = $evaluation->loan_total_cost ?? 0;
                            // Installment (f3) - only after
                            $installmentPrint = $evaluation->installment_proposed_total ?? 0;

                            // b. Total Beban
                            $totalBebanBefore = $bizTotalBefore + $hhTotalBefore + $loanTotalCostPrint + 0 + $bankInstallmentsBefore + $otherExpBefore;
                            $totalBebanAfter = $bizTotalAfter + $hhTotalAfter + 0 + $installmentPrint + $bankInstallmentsAfter + $otherExpAfter;

                            // c. Laba Berjalan
                            $labaBerjalanBefore = $totalPendapatanBefore - $totalBebanBefore;
                            $labaBerjalanAfter = $totalPendapatanAfter - $totalBebanAfter;
                        @endphp

                        <h2 class="font-bold text-lg mb-2 mt-6">D. LAPORAN LABA / RUGI</h2>

                        <table class="mpk-table text-[9px] mb-0">
                            <!-- Header -->
                            <tr>
                                <td class="mpk-header-bg text-xs py-1" style="width: 40%;">Keterangan Neraca</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Sebelum Pencairan
                                </td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Setelah Pencairan
                                </td>
                            </tr>

                            <!-- a. Total Pendapatan -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1 pl-2">a. Total Pendapatan</td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($totalPendapatanBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($totalPendapatanAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Gaji</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($salaryBefore, 0, ',', '.') }}</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($salaryAfter, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Pendapatan Usaha (Omzet Penjualan)</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($businessInBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($businessInAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Pendapatan Lainnya</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherInBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherInAfter, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-6">Pendapatan Lainnya yang Diamortisasi</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($capitalInjection, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- b. Total Beban -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1 pl-2">b. Total Beban</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($totalBebanBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($totalBebanAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Beban Usaha -->
                            <tr class="font-bold">
                                <td class="py-1 pl-4">Beban Usaha</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($bizTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($bizTotalAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                            @foreach($bizFields as $label => $field)
                                <tr>
                                    <td class="py-1 pl-8">Beban {{ $label }}</td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_before'} ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_after'} ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="py-1 pl-8">Beban Depresiasi Aset Tetap / Inventaris</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Beban Lainnya yang Diamortisasi</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                            </tr>

                            <!-- Beban Rumah Tangga -->
                            <tr class="font-bold">
                                <td class="py-1 pl-4">Beban Rumah Tangga</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($hhTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($hhTotalAfter, 0, ',', '.') }}</td>
                            </tr>
                            @foreach($hhFields as $label => $field)
                                <tr>
                                    <td class="py-1 pl-8">Beban {{ $label }}</td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_before'} ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1 text-right pr-2">Rp
                                        {{ number_format($evaluation->{$field . '_after'} ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Beban Realisasi Kredit -->
                            <tr style="background-color: #f3e8ff;">
                                <td class="py-1 pl-4 font-bold">Beban Realisasi Kredit / Pembiayaan (Arus Kas f1)</td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($loanTotalCostPrint, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                            </tr>

                            <!-- Beban Angsuran Kredit -->
                            <tr style="background-color: #fce7f3;">
                                <td class="py-1 pl-4 font-bold">Beban Angsuran Kredit / Pembiayaan (Arus Kas f3)</td>
                                <td class="py-1 text-right pr-2">Rp 0</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($installmentPrint, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Beban Angsuran Hutang Bank Lain -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Beban Angsuran Hutang Bank Lain</td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($bankInstallmentsBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($bankInstallmentsAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- Beban Lainnya -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">Beban Lainnya</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherExpBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($otherExpAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- c. Laba Berjalan -->
                            <tr class="font-bold" style="background-color: #fef3c7;">
                                <td class="py-1.5 pl-2">c. Laba Berjalan / Selisih Pendapatan dan Beban (a - b)</td>
                                <td class="py-1.5 text-right pr-2 {{ $labaBerjalanBefore < 0 ? 'text-red-600' : '' }}">
                                    Rp
                                    {{ $labaBerjalanBefore < 0 ? '(' . number_format(abs($labaBerjalanBefore), 0, ',', '.') . ')' : number_format($labaBerjalanBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2 {{ $labaBerjalanAfter < 0 ? 'text-red-600' : '' }}">
                                    Rp
                                    {{ $labaBerjalanAfter < 0 ? '(' . number_format(abs($labaBerjalanAfter), 0, ',', '.') . ')' : number_format($labaBerjalanAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>

                        <!-- NERACA (Balance Sheet) - Only for Wirausaha -->
                        @if($evaluation->customer_entrepreneurship_status === 'Wirausaha')
                            @php
                                $kasUsaha = $evaluation->kas_usaha ?? 0;
                                $piutangUsaha = $evaluation->piutang_usaha ?? 0;
                                $persediaan = $evaluation->persediaan ?? 0;
                                $totalAktivaLancar = $kasUsaha + $piutangUsaha + $persediaan;
                                $pendapatanUsaha = $evaluation->cash_in_business_before ?? 0;
                                $bebanUsaha = $evaluation->biz_total_before ?? 0;


                                $totalLabaBerjalan = $pendapatanUsaha - $bebanUsaha;
                                $pinjamanBankLain = $totalBakiDebet ?? 0;
                                $kewajibanLancar = $evaluation->kewajiban_lancar ?? 0;
                                $totalKewajibanLancarNeraca = $pinjamanBankLain + $kewajibanLancar;

                                // Custom Assets
                                $customAssets = $evaluation->customAssets ?? collect();
                                $totalCustomAssets = $customAssets->sum(function ($a) {
                                    return (int) $a->estimated_price;
                                });

                                $totalAssetNeraca = $totalAktivaLancar + $totalCustomAssets;

                                // Modal side
                                $kewajibanJangkaPanjang = $evaluation->kewajiban_jangka_panjang ?? 0;
                                $labaBerjalan = $totalLabaBerjalan ?? 0;
                                $modalUsahaNeraca = $evaluation->modal_usaha ?? 0;
                                $totalKewajibanDanModal = $totalKewajibanLancarNeraca + $kewajibanJangkaPanjang + $labaBerjalan + $modalUsahaNeraca;
                            @endphp

                            <h2 class="font-bold text-lg mb-2 mt-6">E. NERACA</h2>

                            <table class="mpk-table text-[9px] mb-0">
                                <!-- AKTIVA LANCAR & KEWAJIBAN Headers -->
                                <tr>
                                    <td class="mpk-header-bg font-bold py-1 pl-2" style="width: 35%;">AKTIVA LANCAR</td>
                                    <td class="mpk-header-bg py-1" style="width: 15%;"></td>
                                    <td class="mpk-header-bg font-bold py-1 pl-2" style="width: 35%;">KEWAJIBAN</td>
                                    <td class="mpk-header-bg py-1" style="width: 15%;"></td>
                                </tr>

                                <!-- Row 1: Kas Usaha | Pinjaman Bank Lain -->
                                <tr>
                                    <td class="py-1 pl-2">Kas Usaha</td>
                                    <td class="py-1 text-right pr-2">Rp {{ number_format($kasUsaha, 0, ',', '.') }}</td>
                                    <td class="py-1 pl-2">Pinjaman di Bank Lain / Pihak Lain</td>
                                    <td class="py-1 text-right pr-2">Rp {{ number_format($pinjamanBankLain, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Row 2: Piutang Usaha | Kewajiban Lancar -->
                                <tr>
                                    <td class="py-1 pl-2">Piutang Usaha</td>
                                    <td class="py-1 text-right pr-2">Rp {{ number_format($piutangUsaha, 0, ',', '.') }}</td>
                                    <td class="py-1 pl-2">Kewajiban Lancar / Hutang Dagang</td>
                                    <td class="py-1 text-right pr-2">Rp {{ number_format($kewajibanLancar, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- Row 3: Persediaan -->
                                <tr>
                                    <td class="py-1 pl-2">Persediaan</td>
                                    <td class="py-1 text-right pr-2">Rp {{ number_format($persediaan, 0, ',', '.') }}</td>
                                    <td class="py-1 pl-2"></td>
                                    <td class="py-1 pr-2"></td>
                                </tr>

                                <!-- TOTAL AKTIVA LANCAR | TOTAL KEWAJIBAN LANCAR -->
                                <tr class="font-bold bg-gray-50">
                                    <td class="py-1.5 pl-2 text-center">TOTAL AKTIVA LANCAR</td>
                                    <td class="py-1.5 text-right pr-2">Rp
                                        {{ number_format($totalAktivaLancar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1.5 pl-2 text-center">TOTAL KEWAJIBAN LANCAR</td>
                                    <td class="py-1.5 text-right pr-2">Rp
                                        {{ number_format($totalKewajibanLancarNeraca, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- ASET & MODAL Headers -->
                                <tr>
                                    <td class="mpk-subheader-bg font-bold py-1 pl-2">ASET</td>
                                    <td class="mpk-subheader-bg py-1"></td>
                                    <td class="mpk-subheader-bg font-bold py-1 pl-2">MODAL</td>
                                    <td class="mpk-subheader-bg py-1"></td>
                                </tr>

                                <!-- Assets & Modal Rows -->
                                @php $assetIndex = 0; @endphp
                                @foreach($customAssets as $asset)
                                    <tr>
                                        <td class="py-1 pl-2">{{ $asset->name ?? 'Aset ' . ($assetIndex + 1) }}</td>
                                        <td class="py-1 text-right pr-2">Rp
                                            {{ number_format($asset->estimated_price ?? 0, 0, ',', '.') }}
                                        </td>
                                        @if($assetIndex === 0)
                                            <td class="py-1 pl-2">Kewajiban Jangka Panjang</td>
                                            <td class="py-1 text-right pr-2">Rp
                                                {{ number_format($kewajibanJangkaPanjang, 0, ',', '.') }}
                                            </td>
                                        @elseif($assetIndex === 1)
                                            <td class="py-1 pl-2">Laba Berjalan</td>
                                            <td class="py-1 text-right pr-2">Rp {{ number_format($labaBerjalan, 0, ',', '.') }}</td>
                                        @elseif($assetIndex === 2)
                                            <td class="py-1 pl-2">Modal Usaha</td>
                                            <td class="py-1 text-right pr-2">Rp {{ number_format($modalUsahaNeraca, 0, ',', '.') }}
                                            </td>
                                        @else
                                            <td class="py-1 pl-2"></td>
                                            <td class="py-1 pr-2"></td>
                                        @endif
                                    </tr>
                                    @php $assetIndex++; @endphp
                                @endforeach

                                {{-- If fewer than 3 assets, show remaining modal rows --}}
                                @if($customAssets->count() < 1)
                                    <tr>
                                        <td class="py-1 pl-2"></td>
                                        <td class="py-1 pr-2"></td>
                                        <td class="py-1 pl-2">Kewajiban Jangka Panjang</td>
                                        <td class="py-1 text-right pr-2">Rp
                                            {{ number_format($kewajibanJangkaPanjang, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                @if($customAssets->count() < 2)
                                    <tr>
                                        <td class="py-1 pl-2"></td>
                                        <td class="py-1 pr-2"></td>
                                        <td class="py-1 pl-2">Laba Berjalan</td>
                                        <td class="py-1 text-right pr-2">Rp {{ number_format($labaBerjalan, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if($customAssets->count() < 3)
                                    <tr>
                                        <td class="py-1 pl-2"></td>
                                        <td class="py-1 pr-2"></td>
                                        <td class="py-1 pl-2">Modal Usaha</td>
                                        <td class="py-1 text-right pr-2">Rp {{ number_format($modalUsahaNeraca, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif

                                <!-- TOTAL ASSET | TOTAL KEWAJIBAN DAN MODAL -->
                                <tr class="font-bold bg-gray-50">
                                    <td class="py-1.5 pl-2 text-center">TOTAL AKTIVA LANCAR DAN ASET</td>
                                    <td class="py-1.5 text-right pr-2">Rp
                                        {{ number_format($totalAssetNeraca, 0, ',', '.') }}
                                    </td>
                                    <td class="py-1.5 pl-2 text-center">TOTAL KEWAJIBAN DAN MODAL</td>
                                    <td class="py-1.5 text-right pr-2">Rp
                                        {{ number_format($totalKewajibanDanModal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- SECTION E: DATA AGUNAN (Collateral) -->
                        @if($evaluation->collaterals && $evaluation->collaterals->count() > 0)
                            <div class="page-break"></div>

                            <h2 class="font-bold text-lg mb-2 mt-4">F. DATA AGUNAN</h2>

                            @php
                                $totalMarketValue = $evaluation->collaterals->sum('market_value');
                                $totalBankValue = $evaluation->collaterals->sum('bank_value');
                            @endphp

                            @foreach($evaluation->collaterals as $colIndex => $collateral)
                                @if($colIndex > 0)
                                    <div class="page-break"></div>
                                @endif
                                <table class="mpk-table text-[9px] mb-3">
                                    <tr>
                                        <td colspan="4" class="mpk-header-bg text-xs uppercase py-1">
                                            AGUNAN #{{ $colIndex + 1 }}
                                            {{ $collateral->type === 'certificate' ? 'SERTIFIKAT (TANAH/BANGUNAN)' : 'KENDARAAN BERMOTOR (BPKB)' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-[25%] border-r-0 pb-1">Nama Pemilik</td>
                                        <td class="w-[25%] pb-1">: {{ $collateral->owner_name ?? '-' }}</td>
                                        <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">No. KTP Pemilik</td>
                                        <td class="w-[25%] pb-1">: {{ $collateral->owner_ktp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border-r-0 pb-1">Jenis Bukti</td>
                                        <td class="pb-1">: {{ $collateral->proof_type ?? '-' }}</td>
                                        <td class="border-r-0 border-l border-gray-600 pb-1">No.
                                            {{ $collateral->type === 'certificate' ? 'SHM/SHGB' : 'BPKB' }}
                                        </td>
                                        <td class="pb-1">: {{ $collateral->proof_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border-r-0 pb-1">Nilai Pasar (Estimasi)</td>
                                        <td class="pb-1 font-bold">: Rp
                                            {{ number_format($collateral->market_value ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="border-r-0 border-l border-gray-600 pb-1">Nilai Taksasi Bank</td>
                                        <td class="pb-1 font-bold">: Rp
                                            {{ number_format($collateral->bank_value ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>

                                    @if($collateral->type === 'certificate')
                                        {{-- Property-specific fields --}}
                                        <tr>
                                            <td colspan="4"
                                                class="mpk-header-bg text-[9px] uppercase py-1 border-t border-gray-600">KONDISI
                                                TANAH DAN BANGUNAN</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">Luas Tanah</td>
                                            <td class="pb-1">: {{ $collateral->property_surface_area ?? '-' }} mÂ²</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Luas Bangunan</td>
                                            <td class="pb-1">: {{ $collateral->property_building_area ?? '-' }} mÂ²</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">Peruntukan Tanah</td>
                                            <td class="pb-1">: {{ $collateral->peruntukan_tanah ?? '-' }}</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Lebar Jalan</td>
                                            <td class="pb-1">: {{ $collateral->lebar_jalan ?? '-' }} m</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">Kondisi Bangunan</td>
                                            <td class="pb-1">: {{ $collateral->kondisi_bangunan ?? '-' }}</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Material Pondasi</td>
                                            <td class="pb-1">: {{ $collateral->material_pondasi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">Material Tembok</td>
                                            <td class="pb-1">: {{ $collateral->material_tembok ?? '-' }}</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Material Atap</td>
                                            <td class="pb-1">: {{ $collateral->material_atap ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">Material Kusen</td>
                                            <td class="pb-1">: {{ $collateral->material_kusen ?? '-' }}</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Mat. Daun Pintu</td>
                                            <td class="pb-1">: {{ $collateral->material_daun_pintu ?? '-' }}</td>
                                        </tr>
                                    @else
                                        {{-- Vehicle-specific fields --}}
                                        <tr>
                                            <td class="border-r-0 pb-1">Merk / Model</td>
                                            <td class="pb-1">: {{ $collateral->vehicle_brand ?? '-' }}
                                                {{ $collateral->vehicle_model ?? '' }}
                                            </td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">Tahun / Warna</td>
                                            <td class="pb-1">: {{ $collateral->vehicle_year ?? '-' }} /
                                                {{ $collateral->vehicle_color ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">No. Polisi</td>
                                            <td class="pb-1">: {{ $collateral->vehicle_plate_number ?? '-' }}</td>
                                            <td class="border-r-0 border-l border-gray-600 pb-1">No. Rangka</td>
                                            <td class="pb-1">: {{ $collateral->vehicle_frame_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border-r-0 pb-1">No. Mesin</td>
                                            <td colspan="3" class="pb-1">: {{ $collateral->vehicle_engine_number ?? '-' }}</td>
                                        </tr>
                                    @endif

                                    @if($collateral->location_address || $collateral->village)
                                        <tr>
                                            <td class="border-r-0 pb-1">Alamat Agunan</td>
                                            <td colspan="3" class="pb-1">: {{ $collateral->location_address ?? '-' }}
                                                @if($collateral->village || $collateral->district)
                                                    <br>&nbsp; Desa/Kel: {{ $collateral->village ?? '-' }}, Kec:
                                                    {{ $collateral->district ?? '-' }}, Kab: {{ $collateral->regency ?? '-' }}, Prov:
                                                    {{ $collateral->province ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Lokasi Agunan -->
                                    @if($collateral->latitude && $collateral->longitude)
                                        <tr>
                                            <td colspan="4"
                                                class="mpk-header-bg text-[9px] uppercase py-1 border-t border-gray-600">LOKASI
                                                AGUNAN</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="p-2">
                                                <div class="flex gap-4 items-start w-full">
                                                    <div class="flex-1 text-[9px]">
                                                        <table class="w-full text-left nested-table m-0">
                                                            <tr>
                                                                <td class="w-[30%] py-1 pl-2 font-bold">Koordinat</td>
                                                                <td class="py-1">: {{ $collateral->latitude }},
                                                                    {{ $collateral->longitude }}
                                                                </td>
                                                            </tr>
                                                            @if($collateral->village || $collateral->district)
                                                                <tr>
                                                                    <td class="w-[30%] py-1 pl-2 font-bold align-top">Lokasi</td>
                                                                    <td class="py-1">:
                                                                        Desa/Kel: {{ $collateral->village ?? '-' }}, Kec:
                                                                        {{ $collateral->district ?? '-' }}<br>
                                                                        &nbsp; Kab/Kota: {{ $collateral->regency ?? '-' }}, Prov:
                                                                        {{ $collateral->province ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td class="w-[30%] py-1 pl-2 font-bold">Google Maps</td>
                                                                <td class="py-1">: <span
                                                                        class="text-blue-600 break-all text-[8px]">https://www.google.com/maps/search/?api=1&query={{ $collateral->latitude }},{{ $collateral->longitude }}</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        @if($collateral->type === 'certificate' && $collateral->property_image_4)
                                                            <div
                                                                class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden relative">
                                                                <img src="{{ route('media.evaluations', ['type' => 'collaterals', 'filename' => $collateral->property_image_4]) }}"
                                                                    alt="Foto Lokasi" class="w-full h-full object-cover">
                                                            </div>
                                                        @elseif($collateral->type === 'vehicle' && $collateral->vehicle_image_4)
                                                            <div
                                                                class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden relative">
                                                                <img src="{{ route('media.evaluations', ['type' => 'collaterals', 'filename' => $collateral->vehicle_image_4]) }}"
                                                                    alt="Foto Lokasi Kendaraan" class="w-full h-full object-cover">
                                                            </div>
                                                        @endif
                                                        @if($collateral->path_distance)
                                                            <div class="text-[8px] text-center">
                                                                <div class="font-bold">Jarak dari
                                                                    {{ $evaluation->office_branch ?? 'Kantor Pusat' }}</div>
                                                                <div>{{ number_format($collateral->path_distance, 1) }} km</div>
                                                            </div>
                                                        @endif
                                                        <div class="border border-gray-400 p-[2px] bg-white w-24 h-24">
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $collateral->latitude . ',' . $collateral->longitude) }}"
                                                                alt="QR Code Lokasi" class="w-full h-full object-contain">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Foto Dokumen Agunan -->
                                    @php
                                        $hasPhotos = false;
                                        if ($collateral->type === 'certificate') {
                                            $hasPhotos = $collateral->property_image_1 || $collateral->property_image_2 || $collateral->property_image_3;
                                        } else {
                                            $hasPhotos = $collateral->vehicle_image_1 || $collateral->vehicle_image_2 || $collateral->vehicle_image_3;
                                        }
                                    @endphp
                                    @if($hasPhotos)
                                        <tr>
                                            <td colspan="4"
                                                class="mpk-header-bg text-[9px] uppercase py-1 border-t border-gray-600">FOTO
                                                DOKUMEN AGUNAN</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="p-2">
                                                <div class="flex gap-3 justify-center flex-wrap">
                                                    @if($collateral->type === 'certificate')
                                                        @php
                                                            $certImages = [
                                                                ['field' => 'property_image_1', 'label' => 'Foto Jaminan 1'],
                                                                ['field' => 'property_image_2', 'label' => 'Foto Jaminan 2'],
                                                                ['field' => 'property_image_3', 'label' => 'Foto Jaminan 3'],
                                                            ];
                                                        @endphp
                                                        @foreach($certImages as $img)
                                                            @if($collateral->{$img['field']})
                                                                <div class="text-center">
                                                                    <div
                                                                        class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden">
                                                                        <img src="{{ route('media.evaluations', ['type' => 'collaterals', 'filename' => $collateral->{$img['field']}]) }}"
                                                                            alt="{{ $img['label'] }}" class="w-full h-full object-cover">
                                                                    </div>
                                                                    <p class="text-[8px] text-gray-500 mt-1">{{ $img['label'] }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $vehImages = [
                                                                ['field' => 'vehicle_image_1', 'label' => 'Foto Depan Tampak Plat'],
                                                                ['field' => 'vehicle_image_2', 'label' => 'Foto Samping'],
                                                                ['field' => 'vehicle_image_3', 'label' => 'Foto Belakang '],
                                                            ];
                                                        @endphp
                                                        @foreach($vehImages as $img)
                                                            @if($collateral->{$img['field']})
                                                                <div class="text-center">
                                                                    <div
                                                                        class="border border-gray-400 p-[2px] bg-white w-52 h-36 overflow-hidden">
                                                                        <img src="{{ route('media.evaluations', ['type' => 'collaterals', 'filename' => $collateral->{$img['field']}]) }}"
                                                                            alt="{{ $img['label'] }}" class="w-full h-full object-cover">
                                                                    </div>
                                                                    <p class="text-[8px] text-gray-500 mt-1">{{ $img['label'] }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </table>

                                <!-- Signature block for AO and Kabag -->
                                <div class="mt-8 flex justify-between px-20 text-[10px] w-full"
                                    style="page-break-inside: avoid;">
                                    <div class="text-center">
                                        <p class="mb-16">Mengetahui,</p>
                                        <p class="font-bold">(Moch. Arif Priyadi)</p>
                                        <p>Kabag. Kredit</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="mb-16">Dibuat Oleh,</p>
                                        <p class="font-bold">({{ $evaluation->user->name }})</p>
                                        <p>Account Officer (AO)</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Total Agunan -->
                            <table class="mpk-table text-[9px] mb-0 mt-6">
                                <tr class="font-bold bg-gray-100">
                                    <td class="py-1.5 pl-2" style="width: 50%;">Total Nilai Pasar</td>
                                    <td class="py-1.5 text-right pr-2" style="width: 50%;">Rp
                                        {{ number_format($totalMarketValue, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="font-bold bg-gray-100">
                                    <td class="py-1.5 pl-2">Total Nilai Taksasi Bank</td>
                                    <td class="py-1.5 text-right pr-2">Rp {{ number_format($totalBankValue, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- PAGE BREAK before Realisasi Kredit -->
                        <div class="page-break"></div>

                        <!-- SECTION F: REALISASI KREDIT / PEMBIAYAAN -->
                        <h2 class="font-bold text-lg mb-2 mt-4">G. REALISASI KREDIT / PEMBIAYAAN</h2>

                        @php
                            // Loan details
                            $loanAmount = $evaluation->loan_amount ?? 0;
                            $interestRate = $evaluation->loan_interest_rate ?? 0;
                            $tenor = $evaluation->loan_term_months ?? 0;

                            // Installments
                            $monthlyInterest = $evaluation->installment_proposed_interest ?? 0;
                            $monthlyPrincipal = $evaluation->installment_proposed_principal ?? 0;
                            $monthlyInstallment = $evaluation->installment_proposed_total ?? 0;

                            // Rekomendasi max
                            $netIncome = max(0, ($evaluation->cash_in_total_before ?? 0) - ($evaluation->cash_out_total_before ?? 0));
                            $rpcRatioVal = ($evaluation->rpc_ratio ?? 0) / 100;
                            $incomeRpcLimit = ($evaluation->net_cash_flow_before ?? 0) * $rpcRatioVal;

                            if ($netIncome > $incomeRpcLimit) {
                                $numerator = $incomeRpcLimit;
                            } else {
                                $numerator = $netIncome;
                            }

                            $ratePerYear = $interestRate;
                            $ratePerMonth = $ratePerYear / 1200; // (% / 1200 because percentage)

                            $limit = 0;
                            if ($tenor > 0) {
                                if ($evaluation->loan_type === 'Pinjaman Anuitas') {
                                    if ($ratePerMonth == 0) {
                                        $limit = $numerator * $tenor;
                                    } else {
                                        // numerator x (1 - ((1 + (loan_term_months%/12))^-loan_tenor)/(loan_term_months%/12))
                                        $pvFactor = (1 - pow(1 + $ratePerMonth, -$tenor)) / $ratePerMonth;
                                        $limit = $numerator * $pvFactor;
                                    }
                                } elseif ($evaluation->loan_type === 'Pinjaman Musiman') {
                                    // numerator / (loan_term_months%/12)
                                    if ($ratePerMonth > 0) {
                                        $limit = $numerator / $ratePerMonth;
                                    } else {
                                        $limit = 0;
                                    }
                                } else {
                                    // Default to 'Pinjaman Angsuran'
                                    // (numerator x loan_tenor) / (1 + ((loan_term_months%/12) x loan_tenor))
                                    $limit = ($numerator * $tenor) / (1 + ($ratePerMonth * $tenor));
                                }
                            }
                            $maxLoanLimit = floor($limit / 1000000) * 1000000;

                            // Rekomendasi installment
                            $recMonthlyInterest = round(($maxLoanLimit * ($interestRate / 100)) / 12);
                            if ($evaluation->loan_type === 'Pinjaman Musiman') {
                                $recMonthlyPrincipal = 0;
                            } else {
                                $recMonthlyPrincipal = round($maxLoanLimit / ($tenor > 0 ? $tenor : 1));
                            }
                            $recMonthlyInstallment = round($recMonthlyInterest + $recMonthlyPrincipal);

                            // Costs
                            $loanProvisionCost = $evaluation->loan_provision_cost ?? 0;
                            $loanAdminCost = $evaluation->loan_administration_cost ?? 0;
                            $loanStampDuty = $evaluation->loan_duty_stamp_cost ?? 0;
                            $loanNotary = $evaluation->loan_notary_public_cost ?? 0;
                            $loanInsurance = $evaluation->loan_insurance_cost ?? 0;
                            $loanOtherCost = $evaluation->loan_other_cost ?? 0;
                            $loanTotalCost = $evaluation->loan_total_cost ?? ($loanProvisionCost + $loanAdminCost + $loanStampDuty + $loanNotary + $loanInsurance + $loanOtherCost);

                            $provisionRate = $evaluation->loan_provision_rate ?? 0;
                            $adminRate = $evaluation->loan_admin_rate ?? 0;

                            // Rekomendasi costs
                            $rekProvision = $evaluation->rekomendasi_loan_provision_cost ?? 0;
                            $rekAdmin = $evaluation->rekomendasi_loan_administration_cost ?? 0;
                            $rekStamp = $evaluation->rekomendasi_loan_duty_stamp_cost ?? 0;
                            $rekNotary = $evaluation->rekomendasi_loan_notary_public_cost ?? 0;
                            $rekInsurance = $evaluation->rekomendasi_loan_insurance_cost ?? 0;
                            $rekOther = $evaluation->rekomendasi_loan_other_cost ?? 0;
                            $rekTotalCost = $rekProvision + $rekAdmin + $rekStamp + $rekNotary + $rekInsurance + $rekOther;

                            // Final balances
                            $cashBankTotalBefore = $evaluation->cash_bank_total_before ?? 0;
                            $cashBankTotalAfter = $evaluation->cash_bank_total_after ?? 0;
                            $loanRemBefore = $evaluation->loan_rem_balance_before ?? 0;
                            $loanRemAfter = $evaluation->loan_rem_balance_after ?? 0;
                        @endphp

                        <table class="mpk-table text-[9px] mb-0">
                            <!-- Header -->
                            <tr>
                                <td class="mpk-header-bg text-xs py-1" style="width: 40%;">Realisasi Kredit / Pembiayaan
                                </td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Kredit yang
                                    Diusulkan</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Rekomendasi Maks.
                                    Pencairan</td>
                            </tr>

                            <!-- f. Realisasi BPR -->
                            <tr class="font-bold bg-amber-50">
                                <td colspan="3" class="py-1 pl-2">f. Realisasi Kredit / Pembiayaan dari BPR</td>
                            </tr>

                            <!-- f1 Plafond -->
                            <tr class="bg-green-50">
                                <td class="py-1 pl-4 font-bold">f1. Plafond Realisasi</td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($loanAmount, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($maxLoanLimit, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- f2 Beban Realisasi -->
                            <tr>
                                <td class="py-1 pl-4 font-bold">f2. Beban Realisasi</td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($loanTotalCost, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($rekTotalCost, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Beban Provisi ({{ $provisionRate }}%)</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanProvisionCost, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekProvision, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Beban Administrasi ({{ $adminRate }}%)</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanAdminCost, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekAdmin, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Biaya Materai</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanStampDuty, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekStamp, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Biaya Notaris</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanNotary, 0, ',', '.') }}</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekNotary, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Biaya Asuransi</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanInsurance, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekInsurance, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Biaya Lainnya</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($loanOtherCost, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($rekOther, 0, ',', '.') }}</td>
                            </tr>

                            <!-- f3 Beban Cicilan -->
                            <tr class="bg-orange-50">
                                <td class="py-1 pl-4 font-bold">f3. Beban Cicilan</td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($monthlyInstallment, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2 font-bold">Rp
                                    {{ number_format($recMonthlyInstallment, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Bunga Flat p.a ({{ $interestRate }}%)</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($monthlyInterest, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($recMonthlyInterest, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Cicilan Pokok</td>
                                <td class="py-1 text-right pr-2">Rp {{ number_format($monthlyPrincipal, 0, ',', '.') }}
                                </td>
                                <td class="py-1 text-right pr-2">Rp
                                    {{ number_format($recMonthlyPrincipal, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 pl-8">Tenor (Bulan)</td>
                                <td colspan="2" class="py-1 text-center font-bold">{{ $tenor }} Bulan</td>
                            </tr>
                        </table>

                        <!-- Saldo Akhir -->
                        <table class="mpk-table text-[9px] mb-0 mt-3">
                            <tr>
                                <td class="mpk-header-bg text-xs py-1" style="width: 40%;">Keterangan Saldo Akhir</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Sebelum Pencairan
                                </td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 30%;">Setelah Pencairan
                                </td>
                            </tr>

                            <!-- g. Saldo Akhir Kas & Bank -->
                            <tr class="font-bold" style="background-color: #0f172a !important; color: white;">
                                <td class="py-1.5 pl-2">g. Saldo Akhir Kas & Bank (e - f1 + f2 - f3)</td>
                                <td class="py-1.5 text-right pr-2 {{ $cashBankTotalBefore < 0 ? 'text-red-300' : '' }}">
                                    Rp
                                    {{ $cashBankTotalBefore < 0 ? '(' . number_format(abs($cashBankTotalBefore), 0, ',', '.') . ')' : number_format($cashBankTotalBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2 {{ $cashBankTotalAfter < 0 ? 'text-red-300' : '' }}">
                                    Rp
                                    {{ $cashBankTotalAfter < 0 ? '(' . number_format(abs($cashBankTotalAfter), 0, ',', '.') . ')' : number_format($cashBankTotalAfter, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- h. Saldo Baki Debet -->
                            <tr class="font-bold bg-gray-100">
                                <td class="py-1.5 pl-2">h. Saldo Baki Debet</td>
                                <td class="py-1.5 text-right pr-2">Rp {{ number_format($loanRemBefore, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 text-right pr-2">Rp {{ number_format($loanRemAfter, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>

                        <!-- Rasio Keuangan -->
                        @php
                            // Calculate Ratios
                            $totalAssetsPrint = 0;
                            if (isset($evaluation->customAssets)) {
                                foreach ($evaluation->customAssets as $asset) {
                                    $totalAssetsPrint += floatval(preg_replace('/[^0-9]/', '', $asset->estimated_price) ?: 0);
                                }
                            }

                            $totalExtLoansPrint = 0;
                            $extInstallmentsPrint = 0;
                            if (isset($evaluation->externalLoans)) {
                                foreach ($evaluation->externalLoans as $loanExt) {
                                    $totalExtLoansPrint += floatval($loanExt->outstanding_balance ?? 0);
                                    $extInstallmentsPrint += floatval($loanExt->installment_amount ?? 0);
                                }
                            }

                            $installmentTotPrint = $evaluation->installment_proposed_total ?? 0;
                            $netCashFlowPrint = $evaluation->net_cash_flow_before ?? 0;
                            $bankInstPrint = $evaluation->other_bank_installments_before ?? 0;

                            $dsrRatioPrint = $netCashFlowPrint > 0 ? ((($installmentTotPrint + $bankInstPrint) / $netCashFlowPrint) * 100) : 0;
                            $darRatioPrint = $totalAssetsPrint > 0 ? (($totalExtLoansPrint / $totalAssetsPrint) * 100) : 0;

                            $totalEquityPrint = $evaluation->modal_usaha ?? 0;
                            if ($evaluation->customer_entrepreneurship_status !== 'Wirausaha') {
                                $derRatioPrint = 0;
                            } else {
                                $derRatioPrint = $totalEquityPrint > 0 ? (($totalExtLoansPrint / $totalEquityPrint) * 100) : 0;
                            }

                            $totalIncomePrint = $evaluation->cash_in_total_before ?? 0;
                            $dtiRatioPrint = $totalIncomePrint > 0 ? (($extInstallmentsPrint / $totalIncomePrint) * 100) : 0;
                            $rpcRatioPrint = $evaluation->rpc_ratio ?? 0;
                        @endphp

                        <table class="mpk-table text-[9px] mb-0 mt-3">
                            <tr>
                                <td class="mpk-header-bg text-xs py-1 text-center font-bold" colspan="5">Rasio Keuangan
                                </td>
                            </tr>
                            <tr class="font-bold bg-gray-100 text-center">
                                <td class="py-1.5" style="width: 20%;">DSR</td>
                                <td class="py-1.5" style="width: 20%;">DAR</td>
                                <td class="py-1.5" style="width: 20%;">DER
                                    {{ $evaluation->customer_entrepreneurship_status !== 'Wirausaha' ? '(N/A)' : '' }}
                                </td>
                                <td class="py-1.5" style="width: 20%;">DTI</td>
                                <td class="py-1.5" style="width: 20%;">RCP</td>
                            </tr>
                            <tr class="text-center font-bold text-[10px]">
                                <td
                                    class="py-2 {{ $dsrRatioPrint > 50 ? 'text-red-600' : ($dsrRatioPrint > 35 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($dsrRatioPrint, 2, ',', '.') }}%
                                </td>
                                <td class="py-2 {{ $darRatioPrint < 50 ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ number_format($darRatioPrint, 2, ',', '.') }}%
                                </td>
                                <td
                                    class="py-2 {{ $evaluation->customer_entrepreneurship_status !== 'Wirausaha' ? 'text-gray-400' : ($derRatioPrint < 100 ? 'text-green-600' : ($derRatioPrint < 200 ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ $evaluation->customer_entrepreneurship_status !== 'Wirausaha' ? '-' : number_format($derRatioPrint, 2, ',', '.') . '%' }}
                                </td>
                                <td
                                    class="py-2 {{ $dtiRatioPrint > 50 ? 'text-red-600' : ($dtiRatioPrint > 40 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($dtiRatioPrint, 2, ',', '.') }}%
                                </td>
                                <td class="py-2 text-blue-600">{{ number_format($rpcRatioPrint, 2, ',', '.') }}%</td>
                            </tr>
                        </table>

                        <!-- Analisis Kredit Skoring -->
                        @php
                            // Assign sub-item variables for display
                            $charBureau = $evaluation->char_credit_bureau ?? 0;
                            $charInfo = $evaluation->char_info_consistency ?? 0;
                            $charRel = $evaluation->char_relationship ?? 0;
                            $charStab = $evaluation->char_stability ?? 0;
                            $charRep = $evaluation->char_reputation ?? 0;

                            $capRpc = $evaluation->cap_rpc ?? 0;
                            $capLama = $evaluation->cap_lama_usaha ?? 0;
                            $capUsia = $evaluation->cap_usia ?? 0;
                            $capPeng = $evaluation->cap_pengelolaan ?? 0;

                            $capitalDar = $evaluation->capital_dar ?? 0;
                            $capitalDer = $evaluation->capital_der ?? 0;

                            $condLokasi = $evaluation->cond_lokasi ?? 0;
                            $condProfit = $evaluation->cond_profit ?? 0;
                            $condDscr = $evaluation->cond_dscr ?? 0;

                            $colKep = $evaluation->col_kepemilikan ?? 0;
                            $colPer = $evaluation->col_peruntukan ?? 0;
                            $colJalan = $evaluation->col_lebar_jalan ?? 0;
                            $colCov = $evaluation->col_coverage ?? 0;
                            $colMark = $evaluation->col_marketable ?? 0;

                            // Load totals/scales/status directly from backend CreditScoringService
                            $charNilai = $scoringResult['character']['nilai'];
                            $charSkala = $scoringResult['character']['skala'];
                            $charTotal100 = $scoringResult['character']['total100'];
                            $charStatus = $scoringResult['character']['status'];

                            $capNilai = $scoringResult['capacity']['nilai'];
                            $capSkala = $scoringResult['capacity']['skala'];
                            $capTotal100 = $scoringResult['capacity']['total100'];
                            $capStatus = $scoringResult['capacity']['status'];

                            $capitalNilai = $scoringResult['capital']['nilai'];
                            $capitalSkala = $scoringResult['capital']['skala'];
                            $capitalTotal100 = $scoringResult['capital']['total100'];
                            $capitalStatus = $scoringResult['capital']['status'];

                            $condNilai = $scoringResult['condition']['nilai'];
                            $condSkala = $scoringResult['condition']['skala'];
                            $condTotal100 = $scoringResult['condition']['total100'];
                            $condStatus = $scoringResult['condition']['status'];

                            $colNilai = $scoringResult['collateral']['nilai'];
                            $colSkala = $scoringResult['collateral']['skala'];
                            $colTotal100 = $scoringResult['collateral']['total100'];
                            $colStatus = $scoringResult['collateral']['status'];

                            $printFinalScore = $scoringResult['final_score'];
                            $kelayakan = $scoringResult['kelayakan'];
                        @endphp

                        <h2 class="font-bold text-lg mb-2 mt-6">G. Hasil Penilaian dan Aspek Analisis Kredit</h2>

                        <table class="mpk-table text-[9px] mb-0">
                            <tr>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 5%;">#</td>
                                <td class="mpk-header-bg text-xs py-1" style="width: 35%;">Indikator Penilaian</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 10%;">Bobot</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 10%;">Nilai</td>
                                <td class="mpk-header-bg text-xs py-1 text-center" style="width: 10%;">Skala</td>
                                <td class="mpk-header-bg text-xs py-1" style="width: 30%;">Keterangan</td>
                            </tr>

                            <!-- 1. Character -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="py-1 text-center">1</td>
                                <td class="py-1 pl-2" colspan="5">Character (Watak)</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">1.1</td>
                                <td class="py-1 pl-4">SLIK</td>
                                <td class="py-1 text-center">25%</td>
                                <td class="py-1 text-center">{{ $charBureau }}</td>
                                <td class="py-1 text-center">{{ number_format($charBureau / 5 * 25 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if(!$evaluation->externalLoans || $evaluation->externalLoans->isEmpty())
                                        Debitur tidak memiliki pinjaman
                                    @else
                                        @if($charBureau == 5) Lancar
                                        @elseif($charBureau == 4) Dalam Perhatian Khusus (DPK)
                                        @elseif($charBureau == 3) Kurang Lancar
                                        @elseif($charBureau == 2) Diragukan
                                        @elseif($charBureau == 1) Macet
                                        @else -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">1.2</td>
                                <td class="py-1 pl-4">Keterbukaan</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $charInfo }}</td>
                                <td class="py-1 text-center">{{ number_format($charInfo / 4 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($charInfo == 4) Terbuka terhadap setiap informasi yang dibutuhkan bank
                                    @elseif($charInfo == 3) Cukup terbuka terhadap informasi yang dibutuhkan bank
                                    @elseif($charInfo == 2) Tidak terbuka terhadap informasi yang dibutuhkan bank
                                    @elseif($charInfo == 1) Tertutup terhadap informasi yang dibutuhkan bank
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">1.3</td>
                                <td class="py-1 pl-4">Nasabah Baru / Nasabah Lama</td>
                                <td class="py-1 text-center">10%</td>
                                <td class="py-1 text-center">{{ $charRel }}</td>
                                <td class="py-1 text-center">{{ number_format($charRel / 2 * 10 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($charRel == 2 && $evaluation->old_loan_amount)
                                        Nasabah Lama — Pinjaman Sebelumnya: Rp
                                        {{ number_format($evaluation->old_loan_amount, 0, ',', '.') }}, Tenor:
                                        {{ $evaluation->old_loan_term_months ?? '-' }} Bln, Bunga:
                                        {{ $evaluation->old_loan_interest_rate ?? '-' }}%
                                    @elseif($charRel == 2)
                                        Nasabah Lama
                                    @elseif($charRel == 1)
                                        Nasabah Baru
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">1.4</td>
                                <td class="py-1 pl-4">Lama Domisili di Tempat Saat ini</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $charStab }}</td>
                                <td class="py-1 text-center">{{ number_format($charStab / 4 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($evaluation->customer->marital_status === 'Menikah' && $evaluation->customer->spouse_name)
                                        {{ $evaluation->customer->marital_status }} —
                                        {{ $evaluation->customer->spouse_name }}, Tanggungan:
                                        {{ $evaluation->customer_dependents ?? 0 }} orang
                                    @else
                                        @if($charStab == 4) Di atas 6 tahun
                                        @elseif($charStab == 3) Antara 4 - 6 tahun
                                        @elseif($charStab == 2) Antara 2 - 4 tahun
                                        @elseif($charStab == 1) Kurang dari 2 tahun
                                        @else {{ $evaluation->customer->marital_status ?? '-' }}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">1.5</td>
                                <td class="py-1 pl-4">Reputasi Debitur di Sekitar Tempat Tinggal</td>
                                <td class="py-1 text-center">25%</td>
                                <td class="py-1 text-center">{{ $charRep }}</td>
                                <td class="py-1 text-center">{{ number_format($charRep / 5 * 25 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($charRep == 5) Sangat Bagus
                                    @elseif($charRep == 4) Bagus
                                    @elseif($charRep == 3) Cukup Bagus
                                    @elseif($charRep == 2) Kurang Bagus
                                    @elseif($charRep == 1) Buruk
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr class="font-bold" style="background-color: #eef2ff;">
                                <td class="py-1" colspan="2"><em class="pl-4">Nilai Character (Watak)</em></td>
                                <td class="py-1 text-center font-bold">30%</td>
                                <td class="py-1 text-center">{{ number_format($charNilai, 1) }}<br><em
                                        class="text-[7px]">{{ $charStatus }}</em></td>
                                <td class="py-1 text-center">{{ number_format($charSkala, 2) }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- 2. Capacity -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="py-1 text-center">2</td>
                                <td class="py-1 pl-2" colspan="5">Capacity (Kemampuan)</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">2.1</td>
                                <td class="py-1 pl-4">Repayment Capacity (RPC)</td>
                                <td class="py-1 text-center">40%</td>
                                <td class="py-1 text-center">{{ $capRpc }}</td>
                                <td class="py-1 text-center">{{ number_format($capRpc / 5 * 40 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">Rasio: {{ $rpcRatio }}%</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">2.2</td>
                                <td class="py-1 pl-4">
                                    {{ $evaluation->customer_entrepreneurship_status === 'Wirausaha' ? 'Lama usaha / bekerja' : 'Masa Kerja' }}
                                </td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $capLama }}</td>
                                <td class="py-1 text-center">{{ number_format($capLama / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($evaluation->customer_entrepreneurship_status === 'Wirausaha' && $evaluation->customer_entreprenuership_year)
                                        {{ now()->year - $evaluation->customer_entreprenuership_year }} Tahun
                                    @else
                                        {{ $evaluation->customer_company_years ?? 0 }} Tahun
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">2.3</td>
                                <td class="py-1 pl-4">Rekening simpanan</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $capUsia }}</td>
                                <td class="py-1 text-center">{{ number_format($capUsia / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @php
                                        $customerDob = $evaluation->customer->dob ? \Carbon\Carbon::parse($evaluation->customer->dob) : null;
                                        $customerAge = $customerDob ? $customerDob->age : 0;
                                        $loanTermYears = ceil(($evaluation->loan_term_months ?? 0) / 12);
                                        $ageAtMaturity = $customerAge + $loanTermYears;
                                    @endphp
                                    Usia saat jatuh tempo: {{ $ageAtMaturity }} Tahun
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">2.4</td>
                                <td class="py-1 pl-4">
                                    {{ $evaluation->customer_entrepreneurship_status === 'Wirausaha' ? 'Pengelolaan Usaha (Keterlibatan Keluarga)' : 'Status Kepegawaian' }}
                                </td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $capPeng }}</td>
                                <td class="py-1 text-center">{{ number_format($capPeng / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($evaluation->customer_entrepreneurship_status === 'Wirausaha')
                                        @if($capPeng == 5) Sangat Baik (Melibatkan Pasangan)
                                        @elseif($capPeng == 4) Baik (Melibatkan Anak)
                                        @elseif($capPeng == 3) Cukup Baik (Melibatkan Keluarga)
                                        @elseif($capPeng == 2) Kurang Baik (Melibatkan Orang Kepercayaan)
                                        @elseif($capPeng == 1) Tidak Baik (Tidak Melibatkan Siapapun)
                                        @else -
                                        @endif
                                    @else
                                        @if($capPeng == 5) PNS
                                        @elseif($capPeng == 4) TNI/Polri
                                        @elseif($capPeng == 3) BUMN
                                        @elseif($capPeng == 2) Swasta
                                        @else -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr class="font-bold" style="background-color: #ecfdf5;">
                                <td class="py-1" colspan="2"><em class="pl-4">Nilai Capacity (Kemampuan)</em></td>
                                <td class="py-1 text-center font-bold">20%</td>
                                <td class="py-1 text-center">{{ number_format($capNilai, 1) }}<br><em
                                        class="text-[7px]">{{ $capStatus }}</em></td>
                                <td class="py-1 text-center">{{ number_format($capSkala, 2) }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- 3. Capital -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="py-1 text-center">3</td>
                                <td class="py-1 pl-2" colspan="5">Capital (Modal)</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">3.1</td>
                                <td class="py-1 pl-4">Rasio hutang dibandingkan aset (DAR)</td>
                                <td class="py-1 text-center">40%</td>
                                <td class="py-1 text-center">{{ $capitalDar }}</td>
                                <td class="py-1 text-center">{{ number_format($capitalDar / 5 * 40 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600"></td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">3.2</td>
                                <td class="py-1 pl-4">Rasio hutang dibandingkan modal (DER)</td>
                                <td class="py-1 text-center">60%</td>
                                <td class="py-1 text-center">{{ $capitalDer }}</td>
                                <td class="py-1 text-center">{{ number_format($capitalDer / 5 * 60 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600"></td>
                            </tr>
                            <tr class="font-bold" style="background-color: #eff6ff;">
                                <td class="py-1" colspan="2"><em class="pl-4">Nilai Capital (Modal)</em></td>
                                <td class="py-1 text-center font-bold">20%</td>
                                <td class="py-1 text-center">{{ number_format($capitalNilai, 1) }}<br><em
                                        class="text-[7px]">{{ $capitalStatus }}</em></td>
                                <td class="py-1 text-center">{{ number_format($capitalSkala, 2) }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- 4. Condition -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="py-1 text-center">4</td>
                                <td class="py-1 pl-2" colspan="5">Condition of Economic (Prospek Usaha Debitur)</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">4.1</td>
                                <td class="py-1 pl-4">
                                    {{ $evaluation->customer_entrepreneurship_status === 'Wirausaha' ? 'Lokasi usaha' : 'Stabilitas Penghasilan' }}
                                </td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $condLokasi }}</td>
                                <td class="py-1 text-center">{{ number_format($condLokasi / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($evaluation->path_distance)
                                        Jarak dari {{ $evaluation->office_branch ?? 'Kantor Pusat' }}:
                                        {{ number_format($evaluation->path_distance, 2, ',', '.') }} km
                                        —
                                        @if($evaluation->path_distance < 14)
                                            Dekat
                                        @elseif($evaluation->path_distance <= 40)
                                            Cukup Dekat
                                        @else
                                            Cukup Jauh
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">4.2</td>
                                <td class="py-1 pl-4">
                                    {{ $evaluation->customer_entrepreneurship_status === 'Wirausaha' ? 'Rasio Laba Kotor (Profit Margin atas Penjualan)' : 'Jaminan Penghasilan' }}
                                </td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $condProfit }}</td>
                                <td class="py-1 text-center">{{ number_format($condProfit / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600"></td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">4.3</td>
                                <td class="py-1 pl-4">Rasio Debt Service (DSR)</td>
                                <td class="py-1 text-center">60%</td>
                                <td class="py-1 text-center">{{ $condDscr }}</td>
                                <td class="py-1 text-center">{{ number_format($condDscr / 5 * 60 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    DSCR: {{ number_format($dsrRatioPrint, 2, ',', '.') }}%
                                </td>
                            </tr>
                            <tr class="font-bold" style="background-color: #faf5ff;">
                                <td class="py-1" colspan="2"><em class="pl-4">Nilai Condition of Economic (Prospek Usaha
                                        Debitur)</em></td>
                                <td class="py-1 text-center font-bold">10%</td>
                                <td class="py-1 text-center">{{ number_format($condNilai, 1) }}<br><em
                                        class="text-[7px]">{{ $condStatus }}</em></td>
                                <td class="py-1 text-center">{{ number_format($condSkala, 2) }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- 5. Collateral -->
                            <tr class="bg-gray-100 font-bold">
                                <td class="py-1 text-center">5</td>
                                <td class="py-1 pl-2" colspan="5">Collateral (Agunan)</td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">5.1</td>
                                <td class="py-1 pl-4">Kepemilikan Agunan</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $colKep }}</td>
                                <td class="py-1 text-center">{{ number_format($colKep / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @php
                                        $isOwnCollateral = false;
                                        $firstOwnerName = '';
                                        if (isset($evaluation->collaterals) && $evaluation->collaterals->count() > 0) {
                                            $firstCol = $evaluation->collaterals->first();
                                            $firstOwnerName = $firstCol->owner_name ?? '';
                                            $customerName = strtolower(trim($evaluation->customer->name ?? ''));
                                            $spouseName = strtolower(trim($evaluation->customer->spouse_name ?? ''));
                                            $ownerLower = strtolower(trim($firstOwnerName));
                                            if ($ownerLower === $customerName || ($spouseName && $ownerLower === $spouseName)) {
                                                $isOwnCollateral = true;
                                            }
                                        }
                                    @endphp
                                    @if($isOwnCollateral)
                                        Jaminan milik sendiri
                                    @elseif($firstOwnerName)
                                        Jaminan milik orang lain atas nama {{ $firstOwnerName }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">5.2</td>
                                <td class="py-1 pl-4">Peruntukan</td>
                                <td class="py-1 text-center">10%</td>
                                <td class="py-1 text-center">{{ $colPer }}</td>
                                <td class="py-1 text-center">{{ number_format($colPer / 5 * 10 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($colPer == 5) Tempat Usaha Aktif & Rumah Tinggal
                                    @elseif($colPer == 4) Tempat Usaha / Ruko
                                    @elseif($colPer == 3) Rumah Tinggal Aktif
                                    @elseif($colPer == 2) Tanah Kosong Produktif
                                    @elseif($colPer == 1) Tanah Kosong Non-Produktif
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">5.3</td>
                                <td class="py-1 pl-4">Lebar jalan</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $colJalan }}</td>
                                <td class="py-1 text-center">{{ number_format($colJalan / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($colJalan == 5) Jalan Nasional / Raya Lebar (Bisa 2 Truk)
                                    @elseif($colJalan == 4) Jalan Aspal/Paving Lebar (Bisa 2 Mobil)
                                    @elseif($colJalan == 3) Jalan Aspal/Paving Sedang (1 Mobil)
                                    @elseif($colJalan == 2) Gang Lebar (Tidak bisa masuk mobil)
                                    @elseif($colJalan == 1) Gang Sempit / Susah Akses
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">5.4</td>
                                <td class="py-1 pl-4">Nilai Agunan (Collateral Coverage)</td>
                                <td class="py-1 text-center">30%</td>
                                <td class="py-1 text-center">{{ $colCov }}</td>
                                <td class="py-1 text-center">{{ number_format($colCov / 5 * 30 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @php
                                        $totalColBankValue = isset($evaluation->collaterals) ? $evaluation->collaterals->sum('bank_value') : 0;
                                        $loanAmt = $evaluation->loan_amount ?? 0;
                                        $coverageRatio = $loanAmt > 0 ? ($totalColBankValue / $loanAmt) * 100 : 0;
                                    @endphp
                                    Coverage: {{ number_format($coverageRatio, 2, ',', '.') }}%
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1 text-center text-gray-400">5.5</td>
                                <td class="py-1 pl-4">Marketable?</td>
                                <td class="py-1 text-center">20%</td>
                                <td class="py-1 text-center">{{ $colMark }}</td>
                                <td class="py-1 text-center">{{ number_format($colMark / 5 * 20 / 100 * 5, 1) }}</td>
                                <td class="py-1 pl-2 text-gray-600">
                                    @if($colMark == 5) Sangat Mudah Dijual
                                    @elseif($colMark == 4) Mudah Dijual
                                    @elseif($colMark == 3) Cukup Mudah Dijual
                                    @elseif($colMark == 2) Agak Sulit Dijual
                                    @elseif($colMark == 1) Sangat Sulit Dijual
                                    @else -
                                    @endif
                                </td>
                            </tr>
                            <tr class="font-bold" style="background-color: #fff7ed;">
                                <td class="py-1" colspan="2"><em class="pl-4">Nilai Collateral (Agunan)</em></td>
                                <td class="py-1 text-center font-bold">20%</td>
                                <td class="py-1 text-center">{{ number_format($colNilai, 1) }}<br><em
                                        class="text-[7px]">{{ $colStatus }}</em></td>
                                <td class="py-1 text-center">{{ number_format($colSkala, 2) }}</td>
                                <td class="py-1"></td>
                            </tr>

                            <!-- Kesimpulan Nilai -->
                            <tr class="font-bold" style="background-color: #fef3c7; border-top: 2px solid #9ca3af;">
                                <td class="py-1.5" colspan="3"><strong class="pl-2 text-[10px]">Kesimpulan
                                        Nilai</strong></td>
                                <td class="py-1.5 text-center text-[11px]" colspan="2">
                                    {{ number_format($printFinalScore, 2) }}<br><strong
                                        class="text-[8px]">({{ $kelayakan }})</strong>
                                </td>
                                <td class="py-1.5"></td>
                            </tr>
                        </table>

                        <!-- Rentang Skala Nilai Kelayakan -->
                        <h4 class="font-bold text-[9px] mt-3 mb-1">Rentang Skala Nilai Kelayakan</h4>
                        <table class="mpk-table text-[8px] mb-0" style="width: 250px;">
                            <tr style="background-color: #fde68a;">
                                <td class="py-0.5 text-center font-bold" style="width: 15%;">#</td>
                                <td class="py-0.5 font-bold" style="width: 45%;">Kelayakan</td>
                                <td class="py-0.5 font-bold" style="width: 40%;">Rentang Nilai</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 text-center">1</td>
                                <td class="py-0.5" style="color: #15803d;">Sangat Layak</td>
                                <td class="py-0.5">4.61 s/d 5</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 text-center">2</td>
                                <td class="py-0.5" style="color: #1d4ed8;">Layak</td>
                                <td class="py-0.5">3.6 s/d 4.6</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 text-center">3</td>
                                <td class="py-0.5" style="color: #a16207;">Cukup Layak</td>
                                <td class="py-0.5">2.81 s/d 3.6</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 text-center">4</td>
                                <td class="py-0.5" style="color: #c2410c;">Kurang Layak</td>
                                <td class="py-0.5">1.81 s/d 2.8</td>
                            </tr>
                            <tr>
                                <td class="py-0.5 text-center">5</td>
                                <td class="py-0.5" style="color: #dc2626;">Tidak Layak</td>
                                <td class="py-0.5">0 s/d 1.8</td>
                            </tr>
                        </table>


                        <!-- PAGE BREAK before Usulan -->
                        <div class="page-break"></div>

                        <!-- SECTION D: USULAN DAN PERSETUJUAN KREDIT -->
                        <h2 class="font-bold text-lg mb-2 mt-4">H. USULAN KREDIT</h2>

                        @php
                            // Determine collateral type label and binding type
                            $collateralTypeLabel = '-';
                            $collateralBindingType = '-';
                            if (isset($evaluation->collaterals) && $evaluation->collaterals->count() > 0) {
                                $certCount = 0;
                                $bpkbCount = 0;

                                foreach ($evaluation->collaterals as $col) {
                                    if ($col->type === 'certificate') {
                                        $certCount++;
                                    } else {
                                        // Assuming non-certificate is vehicle/BPKB
                                        $bpkbCount++;
                                    }
                                }

                                $labels = [];
                                if ($certCount > 0) {
                                    $labels[] = ($certCount == 1 ? 'Sertifikat' : $certCount . ' Sertifikat');
                                    $collateralBindingType = 'APHT';
                                }
                                if ($bpkbCount > 0) {
                                    $labels[] = ($bpkbCount == 1 ? 'BPKB' : $bpkbCount . ' BPKB');
                                    if ($collateralBindingType === '-') {
                                        $collateralBindingType = 'Fidusia';
                                    } else {
                                        $collateralBindingType = 'APHT dan Fidusia';
                                    }
                                }

                                if (count($labels) > 0) {
                                    $collateralTypeLabel = implode(' dan ', $labels);
                                }
                            }

                            // Proposed Limit based on rule
                            $proposedLimit = ($maxLoanLimit > $loanAmount) ? $loanAmount : $maxLoanLimit;

                            // Terbilang helper (pure PHP, no intl extension needed)
                            $terbilangAmount = '';
                            $amt = (int) ($proposedLimit ?? 0);
                            if ($amt > 0) {
                                $terbilangFn = function ($n) use (&$terbilangFn) {
                                    $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                                    if ($n < 12)
                                        return $words[$n];
                                    if ($n < 20)
                                        return $terbilangFn($n - 10) . ' Belas';
                                    if ($n < 100)
                                        return $terbilangFn(intdiv($n, 10)) . ' Puluh' . ($n % 10 ? ' ' . $terbilangFn($n % 10) : '');
                                    if ($n < 200)
                                        return 'Seratus' . ($n - 100 ? ' ' . $terbilangFn($n - 100) : '');
                                    if ($n < 1000)
                                        return $terbilangFn(intdiv($n, 100)) . ' Ratus' . ($n % 100 ? ' ' . $terbilangFn($n % 100) : '');
                                    if ($n < 2000)
                                        return 'Seribu' . ($n - 1000 ? ' ' . $terbilangFn($n - 1000) : '');
                                    if ($n < 1000000)
                                        return $terbilangFn(intdiv($n, 1000)) . ' Ribu' . ($n % 1000 ? ' ' . $terbilangFn($n % 1000) : '');
                                    if ($n < 1000000000)
                                        return $terbilangFn(intdiv($n, 1000000)) . ' Juta' . ($n % 1000000 ? ' ' . $terbilangFn($n % 1000000) : '');
                                    if ($n < 1000000000000)
                                        return $terbilangFn(intdiv($n, 1000000000)) . ' Miliar' . ($n % 1000000000 ? ' ' . $terbilangFn($n % 1000000000) : '');
                                    return $terbilangFn(intdiv($n, 1000000000000)) . ' Triliun' . ($n % 1000000000000 ? ' ' . $terbilangFn($n % 1000000000000) : '');
                                };
                                $terbilangAmount = $terbilangFn($amt) . ' Rupiah';
                            }

                            // Maturity date
                            $maturityDate = null;
                            if ($evaluation->evaluation_date && $evaluation->loan_term_months) {
                                $maturityDate = \Carbon\Carbon::parse($evaluation->evaluation_date)
                                    ->addMonths($evaluation->loan_term_months)
                                    ->translatedFormat('d-M-Y');
                            }
                            // Calculate new installment recommendation based on the proposed limit
                            $calcMonthlyInterest = round(($proposedLimit * ($interestRate / 100)) / 12);
                            if ($evaluation->loan_type === 'Pinjaman Musiman') {
                                $calcMonthlyPrincipal = 0;
                            } else {
                                $calcMonthlyPrincipal = round($proposedLimit / ($tenor > 0 ? $tenor : 1));
                            }
                            $calcMonthlyInstallment = round($calcMonthlyInterest + $calcMonthlyPrincipal);
                        @endphp

                        <p class="mt-1 text-[11px] text-justify">Berdasarkan data diatas, Permohonan Kredit atas Nama
                            <b>{{ $evaluation->customer->name ?? '-' }}</b> di Usulkan untuk dapat dipertimbangkan
                            dengan fasilitas sebagai berikut :
                        </p>

                        <table class="mpk-table text-[10px] mb-0 mt-2">
                            <tr>
                                <td class="pb-1 border-r-0" style="width:25%;">Maksimum Pinjaman</td>
                                <td class="pb-1" style="width:25%;">: Rp
                                    {{ number_format($proposedLimit, 0, ',', '.') }}
                                </td>
                                <td class="pb-1 border-r-0 border-l border-gray-600 font-bold italic" colspan="2">
                                    {{ $terbilangAmount }}
                                </td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Tujuan Penggunaan</td>
                                <td class="pb-1">: {{ $evaluation->loan_purpose ?? '-' }}</td>
                                <td class="pb-1 border-r-0 border-l border-gray-600" style="width:25%;">Bunga</td>
                                <td class="pb-1 text-right" style="width:25%;">
                                    {{ number_format($evaluation->loan_interest_rate ?? 0, 2) }}%
                                </td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Jenis Kredit</td>
                                <td class="pb-1">: {{ $evaluation->loan_type ?? '-' }}</td>
                                <td class="pb-1 border-r-0 border-l border-gray-600">Denda Keterlambatan</td>
                                <td class="pb-1 text-right">0,001%</td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Angsuran Perbulan</td>
                                <td colspan="3" class="pb-1">: Rp
                                    {{ number_format($calcMonthlyInstallment, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Sektor Ekonomi</td>
                                <td class="pb-1">: {{ $evaluation->economic_sector ?? '-' }}
                                    ({{ $evaluation->economic_sector_code ?? '-' }})</td>
                                <td class="pb-1 border-r-0 border-l border-gray-600">Jenis Agunan</td>
                                <td class="pb-1">: {{ $collateralTypeLabel }}</td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Jangka Waktu</td>
                                <td class="pb-1">: {{ $evaluation->loan_term_months ?? '-' }} Bulan</td>
                                <td class="pb-1 border-r-0 border-l border-gray-600">Jatuh Tempo</td>
                                <td class="pb-1">: {{ $maturityDate ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Sumber Pelunasan Jatuh Tempo</td>
                                <td class="pb-1" colspan="3">: {{ $evaluation->seasonal_loan_repayment_source ?? '-' }}
                                </td>
                            </tr>
                        </table>

                        <!-- Pertimbangan -->
                        <p class="mt-3 text-[11px] text-justify">
                            Beberapa pertimbangan yang mendasari pengusulan permohonan kredit Atas Nama
                            <b>{{ $evaluation->customer->name ?? '-' }}</b>, dengan <b>Nomor KTP
                                {{ $evaluation->customer->identity_number ?? '-' }}</b>,
                            dengan Alamat {{ $evaluation->customer->address ?? '-' }}
                            @if($evaluation->customer->village), Desa/Kelurahan
                            {{ $evaluation->customer->village }}@endif
                            @if($evaluation->customer->district), Kecamatan {{ $evaluation->customer->district }}@endif
                            @if($evaluation->customer->regency), Kabupaten {{ $evaluation->customer->regency }}@endif
                            @if($evaluation->customer->province), Provinsi {{ $evaluation->customer->province }}@endif
                            @if($evaluation->customer->postal_code), Kodepos
                            {{ $evaluation->customer->postal_code }}@endif
                            adalah sebagai berikut :
                        </p>

                        <table class="mpk-table text-[10px] mb-0 mt-2">
                            <tr>
                                <td class="pb-1 border-r-0" style="width:20%;">Kelayakan</td>
                                <td class="pb-1" style="width:30%;">: {{ number_format($printFinalScore, 2) }}
                                    ({{ $kelayakan }})</td>
                                <td class="pb-1 border-r-0 border-l border-gray-600" style="width:20%;">Kondisi Usaha
                                </td>
                                <td class="pb-1" style="width:30%;">: {{ $condStatus }}</td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Kemampuan Bayar</td>
                                <td class="pb-1">: Rp {{ number_format($rpcTotalBefore, 0, ',', '.') }} / Bulan
                                    ({{ $rpcTotalBefore < $calcMonthlyInstallment ? 'Tidak Memadai' : 'Memadai' }})
                                </td>
                                <td class="pb-1 border-r-0 border-l border-gray-600">Permodalan</td>
                                <td class="pb-1">: Modal Cukup Menunjang</td>
                            </tr>
                            <tr>
                                <td class="pb-1 border-r-0">Nilai Agunan</td>
                                @php
                                    $totalCollateralValue = isset($evaluation->collaterals) ? $evaluation->collaterals->sum('bank_value') : 0;
                                @endphp
                                <td class="pb-1">: Rp {{ number_format($totalCollateralValue, 0, ',', '.') }}
                                    ({{ $totalCollateralValue < $evaluation->loan_amount ? 'Tidak Mengcover' : 'Mengcover' }})
                                </td>
                                <td class="pb-1 border-r-0 border-l border-gray-600">Pengikatan</td>
                                <td class="pb-1">: {{ $collateralBindingType }}</td>
                            </tr>
                        </table>

                        <!-- Biaya -->
                        <table class="mpk-table text-[10px] mb-0 mt-3">
                            <tr>
                                <td class="mpk-subheader-bg font-bold py-1" style="width:50%;">Biaya di Potong BPR
                                    Puriseger Sentosa</td>
                                <td class="mpk-subheader-bg font-bold py-1 border-l border-gray-600" style="width:50%;">
                                    Biaya di Bebankan kepada debitur</td>
                            </tr>
                            <tr>
                                <td class="pb-1">Biaya Provisi
                                    <span class="float-right">: Rp
                                        {{ number_format($evaluation->loan_provision_cost ?? 0, 2, ',', '.') }}</span>
                                </td>
                                <td class="pb-1 border-l border-gray-600">Biaya Asuransi Jiwa
                                    <span class="float-right">: Sesuai Tarif Asuransi</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="pb-1">Biaya Administrasi
                                    <span class="float-right">: Rp
                                        {{ number_format($evaluation->loan_administration_cost ?? 0, 2, ',', '.') }}</span>
                                </td>
                                <td class="pb-1 border-l border-gray-600">Biaya Pengikatan dan Notaris
                                    <span class="float-right">: Sesuai Tarif Notaris</span>
                                </td>
                            </tr>
                        </table>

                        <!-- Customer & Guarantor Signature Table -->
                        <div class="mt-4" style="page-break-inside: avoid;">
                            <p class="font-bold text-[10px] mb-2">Nama Calon Debitur & Pasangan / Penjamin:</p>
                            @php
                                $signers = [];
                                // 1. Customer (Pemohon)
                                $signers[] = ['name' => $evaluation->customer->name ?? '-', 'role' => 'Pemohon'];
                                // 2. Spouse (if married)
                                if ($evaluation->customer->marital_status === 'Menikah' && $evaluation->customer->spouse_name) {
                                    $signers[] = ['name' => $evaluation->customer->spouse_name, 'role' => 'Suami / Istri'];
                                }
                                // 3. Guarantors (Penjamin)
                                if (isset($evaluation->guarantors) && $evaluation->guarantors->count() > 0) {
                                    foreach ($evaluation->guarantors as $guarantor) {
                                        $signers[] = ['name' => $guarantor->name, 'role' => 'Penjamin (' . ($guarantor->relationship ?? '-') . ')'];
                                    }
                                }
                            @endphp
                            <table class="mpk-table text-[9px] mb-0">
                                <tr>
                                    <td class="mpk-header-bg text-center font-bold py-1" style="width: 5%;">#</td>
                                    <td class="mpk-header-bg font-bold py-1" style="width: 55%;">Nama</td>
                                    <td class="mpk-header-bg font-bold py-1" style="width: 40%;">Sebagai</td>
                                </tr>
                                @foreach($signers as $idx => $signer)
                                    <tr>
                                        <td class="py-1 text-center">{{ $idx + 1 }}</td>
                                        <td class="py-1 pl-2 font-bold">{{ $signer['name'] }}</td>
                                        <td class="py-1 pl-2">{{ $signer['role'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <!-- AO Signature -->
                        <div class="mt-8 flex justify-center text-[10px]" style="page-break-inside: avoid;">
                            <div class="text-center">
                                <p class="mb-2 font-bold">Diajukan oleh</p>
                                <div
                                    style="width: 180px; height: 100px; #4b5563; display: flex; align-items: flex-start; justify-content: center; padding-top: 4px;">
                                    <span class="text-[9px] text-gray-500">Mojokerto,
                                        {{ \Carbon\Carbon::parse($evaluation->evaluation_date)->translatedFormat('d F Y') }}</span>
                                </div>
                                <p class="mt-2 font-bold">{{ $evaluation->user->name }}</p>
                                <p class="font-bold">Account Officer (AO)</p>
                            </div>
                        </div>

                        <!-- PAGE BREAK before Form Acceptance -->
                        <div class="page-break"></div>

                        <h2 class="font-bold text-lg mb-2 mt-4">I. Tanggapan dan Keputusan</h2>

                        <!-- Komite 1 (Kabag Kredit) -->
                        <div class="border border-gray-600 p-2 mb-2 h-[160px] relative text-[10px] w-full">
                            <p class="font-bold">Hasil Review dan Tanggapan Kepala Bagian Kredit:</p>
                            @if(in_array($evaluation->approval_status, ['approved', 'rejected']))
                                <div class="mt-1 text-[9px] text-justify max-w-[80%]">
                                    <span
                                        class="font-bold text-{{ $evaluation->approval_status === 'approved' ? 'green' : 'red' }}-600">
                                        {{ $evaluation->approval_status === 'approved' ? 'DISETUJUI' : 'DITOLAK' }}
                                    </span>
                                    @if($evaluation->approval_status === 'approved' && $evaluation->approved_amount)
                                        - Plafon: Rp {{ number_format($evaluation->approved_amount, 0, ',', '.') }},
                                        Tenor: {{ $evaluation->approved_tenor }} Bulan,
                                        Bunga: {{ $evaluation->approved_interest_rate }}%
                                    @endif
                                    <br>
                                    <span class="text-gray-700 italic">
                                        Catatan: {!! strip_tags($evaluation->approval_note) ?? '-' !!}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-8 text-center" style="width: 160px;">
                                <div class="border-b border-dashed border-gray-500 mb-2 h-8"></div>
                                @if($evaluation->approvalUser)
                                    <p class="font-bold text-[10px]">{{ $evaluation->approvalUser->name }}</p>
                                @else
                                    <p class="text-[9px]"></p>
                                @endif
                                <p class="text-[9px] text-gray-600">Kepala Bagian Kredit</p>
                            </div>
                        </div>

                        <!-- Komite 2 -->
                        <div class="border border-gray-600 p-2 mb-2 h-[160px] relative text-[10px] w-full">
                            <p class="font-bold">Hasil Review dan Tanggapan Direktur:</p>
                            <div class="absolute bottom-3 right-8 text-center" style="width: 160px;">
                                <div class="border-b border-dashed border-gray-500 mb-2 h-8"></div>
                                <p class="text-[9px]">Direktur</p>
                            </div>
                        </div>

                        @if($proposedLimit > 100000000)
                            <!-- Komite 3 -->
                            <div class="border border-gray-600 p-2 mb-2 h-[160px] relative text-[10px] w-full">
                                <p class="font-bold">Hasil Review dan Tanggapan Komisaris:</p>
                                <div class="absolute bottom-3 right-8 text-center" style="width: 160px;">
                                    <div class="border-b border-dashed border-gray-500 mb-2 h-8"></div>
                                    <p class="text-[9px]">Komisaris</p>
                                </div>
                            </div>
                        @else

                        @endif

                        <h2 class="font-bold text-lg mb-2 mt-4">KEPUTUSAN KOMITE</h2>

                        <table class="mpk-table text-[10px] mb-0 w-full border border-black"
                            style="border-collapse: collapse;">
                            <thead>
                                <tr class="bg-gray-400">
                                    <th class="border border-black py-1 px-2 font-bold w-1/4">Komite Kredit</th>
                                    <th class="border border-black py-1 px-2 font-bold w-1/4">Komite I</th>
                                    <th class="border border-black py-1 px-2 font-bold w-1/4">Komite II</th>
                                    <th class="border border-black py-1 px-2 font-bold w-1/4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-black py-1 px-2">Tanggal Persetujuan</td>
                                    <td class="border border-black py-1 px-2 text-left text-[9px]">
                                        {{ in_array($evaluation->approval_status, ['approved', 'rejected']) && $evaluation->updated_at ? \Carbon\Carbon::parse($evaluation->updated_at)->translatedFormat('d-m-Y') : '' }}
                                    </td>
                                    <td class="border border-black py-1 px-2"></td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr>
                                    <td class="border border-black py-1 px-2">Maksimum Kredit</td>
                                    <td class="border border-black py-1 px-2 text-left text-[9px]">
                                        @if($evaluation->approval_status === 'approved' && $evaluation->approved_amount)
                                            Rp {{ number_format($evaluation->approved_amount, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="border border-black py-1 px-2">Rp</td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr>
                                    <td class="border border-black py-1 px-2">Jangka Waktu</td>
                                    <td class="border border-black py-1 px-2 text-left text-[9px]">
                                        @if($evaluation->approval_status === 'approved' && $evaluation->approved_tenor)
                                            {{ $evaluation->approved_tenor }} Bulan
                                        @else
                                            Bulan
                                        @endif
                                    </td>
                                    <td class="border border-black py-1 px-2">Bulan</td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr>
                                    <td class="border border-black py-1 px-2">Bunga pm</td>
                                    <td class="border border-black py-1 px-2 text-left text-[9px]">
                                        @if($evaluation->approval_status === 'approved' && $evaluation->approved_interest_rate)
                                            {{ $evaluation->approved_interest_rate }} % Flat/Efektif *)
                                        @else
                                            % Flat/Efektif *)
                                        @endif
                                    </td>
                                    <td class="border border-black py-1 px-2">% Flat/Efektif *)</td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr>
                                    <td class="border border-black py-1 px-2">Denda</td>
                                    <td class="border border-black py-1 px-2 text-left text-[9px]">‰ dari tunggakan</td>
                                    <td class="border border-black py-1 px-2"> ‰ dari tunggakan</td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr>
                                    <td class="border border-black py-1 px-2">Dicairkan pada Tanggal</td>
                                    <td class="border border-black py-1 px-2"></td>
                                    <td class="border border-black py-1 px-2"></td>
                                    <td class="border border-black py-1 px-2"></td>
                                </tr>
                                <tr class="bg-gray-400">
                                    <th class="border border-black py-1 px-2 font-bold text-center">Menyetujui</th>
                                    <th class="border border-black py-1 px-2 font-bold text-center">Kabag. Kredit</th>
                                    <th class="border border-black py-1 px-2 font-bold text-center">Direktur</th>
                                    <th class="border border-black py-1 px-2 font-bold text-center"></th>
                                </tr>
                                <tr>
                                    <td class="border border-black h-20 p-0">
                                        <div class="flex justify-center h-full w-full py-1 px-2">
                                            Tanda Tangan
                                        </div>
                                    </td>
                                    <td class="border border-black h-20 p-0">
                                        <div class="flex items-end justify-center h-full w-full py-1 px-2">
                                            {{ $evaluation->approvalUser ? $evaluation->approvalUser->name : '' }}
                                        </div>
                                    </td>
                                    <td class="border border-black h-20 p-0">
                                        <div class="flex items-end justify-center h-full w-full py-1 px-2">
                                            H. Sucipto
                                        </div>
                                    </td>
                                    <td class="border border-black h-20 p-0">
                                        <div class="flex items-end justify-center h-full w-full py-1 px-2">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

    </div>
    </div>

    <script>
        // Use Alpine or Vanilla JS if needed. For print, often basic HTML is best.
        document.addEventListener("DOMContentLoaded", function (event) {
            // Estimate total pages for the screen preview
            const container = document.querySelector('.content-container');
            if (container) {
                // Approximate A4 outer height in pixels at 96dpi including margins
                const pageHeightInPixels = 1040;
                const totalHeight = container.scrollHeight;
                const totalPages = Math.ceil(totalHeight / pageHeightInPixels);

                const screenTotalPagesEl = document.getElementById('screen-total-pages');
                if (screenTotalPagesEl) {
                    screenTotalPagesEl.textContent = totalPages > 0 ? totalPages : 1;
                }
            }
        });
    </script>
</body>

</html>