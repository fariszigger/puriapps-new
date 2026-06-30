<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>Ringkasan Kinerja Semua AO</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/logo-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 10px;
                color: #000;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.4;
            background-color: #f3f4f6;
        }

        .content-container {
            max-width: 297mm;
            margin: 0 auto;
            background: white;
            padding: 8mm;
            min-height: 210mm;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #4b5563;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #4b5563;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .summary-table th {
            background-color: #d1d5db !important;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        .summary-table .section-header {
            background-color: #e5e7eb !important;
            font-weight: bold;
            font-size: 11px;
            text-align: left;
            padding: 6px 10px;
        }

        .summary-table .subtotal-row {
            background-color: #eef2ff !important;
            font-weight: bold;
        }

        .summary-table .grand-total-row {
            background-color: #312e81 !important;
            color: white !important;
            font-weight: bold;
            font-size: 11px;
        }

        .summary-table .grand-total-row td {
            border-color: #312e81;
        }
    </style>
</head>

<body>

    <!-- Print Button Bar -->
    <div class="no-print p-4 bg-gray-100 border-b flex items-center justify-between fixed top-0 w-full z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-gray-700">Ringkasan Kinerja Semua AO</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.performance') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold text-sm">Kembali</a>
            <button onclick="window.print()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold text-sm">Cetak
                Ringkasan</button>
        </div>
    </div>

    <div class="content-container mt-[70px] print:mt-0">

        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('build/assets/logobpr.png') }}" alt="Logo" class="h-10 w-auto object-contain">
            </div>
            <div class="text-right text-[10px] text-gray-500 italic">
                Dicetak: {{ formatIndonesianDate(now()) }} {{ now()->format('H:i') }}
            </div>
        </div>

        <div class="text-center mb-4">
            <h1 class="text-lg font-bold uppercase tracking-wider">RINGKASAN KINERJA ACCOUNT OFFICER</h1>
            <p class="text-sm font-semibold text-gray-700 mt-1">Periode: {{ $periodLabel }}</p>
            
            <div class="mt-2 flex items-center justify-center gap-4">
                <span class="text-xs text-gray-500 font-bold uppercase tracking-tight">Total Kunjungan: <span class="text-indigo-700 text-sm">{{ $grandTotals['visits'] }}</span></span>
                <span class="text-xs text-gray-500 font-bold uppercase tracking-tight border-l border-gray-300 pl-4">Total Bayar: <span class="text-green-700 text-sm">Rp {{ number_format($grandTotals['total_paid'], 0, ',', '.') }}</span></span>
                <div class="flex gap-2 text-[9px] uppercase font-bold tracking-tighter border-l border-gray-300 pl-4">
                    <span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded">Lancar: {{ $grandTotals['kol_1'] }}</span>
                    <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded">DPK: {{ $grandTotals['kol_2'] }}</span>
                    <span class="px-1.5 py-0.5 bg-orange-100 text-orange-800 rounded">KL: {{ $grandTotals['kol_3'] }}</span>
                    <span class="px-1.5 py-0.5 bg-red-100 text-red-800 rounded">D: {{ $grandTotals['kol_4'] }}</span>
                    <span class="px-1.5 py-0.5 bg-red-200 text-red-900 rounded">M: {{ $grandTotals['kol_5'] }}</span>
                </div>
            </div>
        </div>

        @if(count($summaryData) > 0)
            <table class="summary-table">
                <thead>
                    <tr>
                        <th class="w-[30px]">No</th>
                        <th class="text-left" style="min-width: 160px;">Account Officer</th>
                        <th class="text-left" style="min-width: 120px;">Kantor Cabang</th>
                        <th class="w-[80px]" style="background-color:#bbf7d0 !important; color:#166534;">Lancar<br><span class="text-[8px] font-normal">(Kol 1)</span></th>
                        <th class="w-[80px]" style="background-color:#fef08a !important; color:#854d0e;">DPK<br><span class="text-[8px] font-normal">(Kol 2)</span></th>
                        <th class="w-[80px]" style="background-color:#fed7aa !important; color:#9a3412;">KL<br><span class="text-[8px] font-normal">(Kol 3)</span></th>
                        <th class="w-[80px]" style="background-color:#fecaca !important; color:#991b1b;">D<br><span class="text-[8px] font-normal">(Kol 4)</span></th>
                        <th class="w-[80px]" style="background-color:#fda4af !important; color:#881337;">M<br><span class="text-[8px] font-normal">(Kol 5)</span></th>
                        <th class="w-[70px]">Total<br>Kunjungan</th>
                        <th style="min-width: 130px;">Total Bayar (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $globalNo = 1; @endphp

                    {{-- Kabag / Pejabat Eksekutif Section --}}
                    @if(count($kabagSummary) > 0)
                        <tr>
                            <td colspan="10" class="section-header">
                                <div class="flex items-center gap-1">
                                    <span>👤</span> Pejabat Eksekutif
                                </div>
                            </td>
                        </tr>
                        @foreach($kabagSummary as $ao)
                            <tr>
                                <td class="text-center">{{ $globalNo++ }}</td>
                                <td>
                                    <div class="font-semibold">{{ $ao['name'] }}</div>
                                    @if($ao['code'])
                                        <div class="text-[9px] text-gray-500">{{ $ao['code'] }}</div>
                                    @endif
                                </td>
                                <td class="text-[10px]">{{ $ao['branch'] }}</td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_1'] > 0 ? 'font-bold' : 'text-gray-400' }}">{{ $ao['kol_1'] }}</div>
                                    @if($ao['kol_1_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_1_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_2'] > 0 ? 'font-bold' : 'text-gray-400' }}">{{ $ao['kol_2'] }}</div>
                                    @if($ao['kol_2_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_2_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_3'] > 0 ? 'font-bold text-orange-700' : 'text-gray-400' }}">{{ $ao['kol_3'] }}</div>
                                    @if($ao['kol_3_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_3_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_4'] > 0 ? 'font-bold text-red-700' : 'text-gray-400' }}">{{ $ao['kol_4'] }}</div>
                                    @if($ao['kol_4_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_4_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_5'] > 0 ? 'font-bold text-red-800' : 'text-gray-400' }}">{{ $ao['kol_5'] }}</div>
                                    @if($ao['kol_5_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_5_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center font-bold text-indigo-700">{{ $ao['total_visits'] }}</td>
                                <td class="text-right font-bold text-green-700">Rp {{ number_format($ao['total_paid'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        {{-- Kabag Subtotal --}}
                        <tr class="subtotal-row">
                            <td colspan="3" class="text-right text-[10px] uppercase font-bold text-indigo-800">Sub Total Pejabat Eksekutif</td>
                            <td class="text-center">
                                <div>{{ collect($kabagSummary)->sum('kol_1') }}</div>
                                @if(collect($kabagSummary)->sum('kol_1_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($kabagSummary)->sum('kol_1_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($kabagSummary)->sum('kol_2') }}</div>
                                @if(collect($kabagSummary)->sum('kol_2_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($kabagSummary)->sum('kol_2_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($kabagSummary)->sum('kol_3') }}</div>
                                @if(collect($kabagSummary)->sum('kol_3_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($kabagSummary)->sum('kol_3_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($kabagSummary)->sum('kol_4') }}</div>
                                @if(collect($kabagSummary)->sum('kol_4_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($kabagSummary)->sum('kol_4_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($kabagSummary)->sum('kol_5') }}</div>
                                @if(collect($kabagSummary)->sum('kol_5_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($kabagSummary)->sum('kol_5_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center font-bold text-indigo-700">{{ collect($kabagSummary)->sum('total_visits') }}</td>
                            <td class="text-right font-bold text-green-700">Rp {{ number_format(collect($kabagSummary)->sum('total_paid'), 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    {{-- AO per Branch Sections --}}
                    @foreach($aosByBranch as $branch => $branchAos)
                        <tr>
                            <td colspan="10" class="section-header">
                                <div class="flex items-center gap-1">
                                    <span>🏢</span> {{ $branch }}
                                </div>
                            </td>
                        </tr>
                        @foreach($branchAos as $ao)
                            <tr>
                                <td class="text-center">{{ $globalNo++ }}</td>
                                <td>
                                    <div class="font-semibold">{{ $ao['name'] }}</div>
                                    @if($ao['code'])
                                        <div class="text-[9px] text-gray-500">{{ $ao['code'] }}</div>
                                    @endif
                                </td>
                                <td class="text-[10px]">{{ $ao['branch'] }}</td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_1'] > 0 ? 'font-bold' : 'text-gray-400' }}">{{ $ao['kol_1'] }}</div>
                                    @if($ao['kol_1_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_1_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_2'] > 0 ? 'font-bold' : 'text-gray-400' }}">{{ $ao['kol_2'] }}</div>
                                    @if($ao['kol_2_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_2_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_3'] > 0 ? 'font-bold text-orange-700' : 'text-gray-400' }}">{{ $ao['kol_3'] }}</div>
                                    @if($ao['kol_3_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_3_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_4'] > 0 ? 'font-bold text-red-700' : 'text-gray-400' }}">{{ $ao['kol_4'] }}</div>
                                    @if($ao['kol_4_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_4_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center">
                                    <div class="{{ $ao['kol_5'] > 0 ? 'font-bold text-red-800' : 'text-gray-400' }}">{{ $ao['kol_5'] }}</div>
                                    @if($ao['kol_5_paid'] > 0)<div class="text-[8px] text-green-700 font-semibold">{{ number_format($ao['kol_5_paid'], 0, ',', '.') }}</div>@endif
                                </td>
                                <td class="text-center font-bold text-indigo-700">{{ $ao['total_visits'] }}</td>
                                <td class="text-right font-bold text-green-700">Rp {{ number_format($ao['total_paid'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        {{-- Branch Subtotal --}}
                        <tr class="subtotal-row">
                            <td colspan="3" class="text-right text-[10px] uppercase font-bold text-indigo-800">Sub Total {{ $branch }}</td>
                            <td class="text-center">
                                <div>{{ collect($branchAos)->sum('kol_1') }}</div>
                                @if(collect($branchAos)->sum('kol_1_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($branchAos)->sum('kol_1_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($branchAos)->sum('kol_2') }}</div>
                                @if(collect($branchAos)->sum('kol_2_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($branchAos)->sum('kol_2_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($branchAos)->sum('kol_3') }}</div>
                                @if(collect($branchAos)->sum('kol_3_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($branchAos)->sum('kol_3_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($branchAos)->sum('kol_4') }}</div>
                                @if(collect($branchAos)->sum('kol_4_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($branchAos)->sum('kol_4_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center">
                                <div>{{ collect($branchAos)->sum('kol_5') }}</div>
                                @if(collect($branchAos)->sum('kol_5_paid') > 0)<div class="text-[8px] text-green-700">{{ number_format(collect($branchAos)->sum('kol_5_paid'), 0, ',', '.') }}</div>@endif
                            </td>
                            <td class="text-center font-bold text-indigo-700">{{ collect($branchAos)->sum('total_visits') }}</td>
                            <td class="text-right font-bold text-green-700">Rp {{ number_format(collect($branchAos)->sum('total_paid'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Grand Total --}}
                    <tr class="grand-total-row">
                        <td colspan="3" class="text-right uppercase text-[11px]" style="color: white !important;">Grand Total Keseluruhan</td>
                        <td class="text-center" style="color: white !important;">
                            <div>{{ $grandTotals['kol_1'] }}</div>
                            @if($grandTotals['kol_1_paid'] > 0)<div class="text-[8px]" style="color: #86efac !important;">{{ number_format($grandTotals['kol_1_paid'], 0, ',', '.') }}</div>@endif
                        </td>
                        <td class="text-center" style="color: white !important;">
                            <div>{{ $grandTotals['kol_2'] }}</div>
                            @if($grandTotals['kol_2_paid'] > 0)<div class="text-[8px]" style="color: #86efac !important;">{{ number_format($grandTotals['kol_2_paid'], 0, ',', '.') }}</div>@endif
                        </td>
                        <td class="text-center" style="color: white !important;">
                            <div>{{ $grandTotals['kol_3'] }}</div>
                            @if($grandTotals['kol_3_paid'] > 0)<div class="text-[8px]" style="color: #86efac !important;">{{ number_format($grandTotals['kol_3_paid'], 0, ',', '.') }}</div>@endif
                        </td>
                        <td class="text-center" style="color: white !important;">
                            <div>{{ $grandTotals['kol_4'] }}</div>
                            @if($grandTotals['kol_4_paid'] > 0)<div class="text-[8px]" style="color: #86efac !important;">{{ number_format($grandTotals['kol_4_paid'], 0, ',', '.') }}</div>@endif
                        </td>
                        <td class="text-center" style="color: white !important;">
                            <div>{{ $grandTotals['kol_5'] }}</div>
                            @if($grandTotals['kol_5_paid'] > 0)<div class="text-[8px]" style="color: #86efac !important;">{{ number_format($grandTotals['kol_5_paid'], 0, ',', '.') }}</div>@endif
                        </td>
                        <td class="text-center text-[12px]" style="color: #a5b4fc !important;">{{ $grandTotals['visits'] }}</td>
                        <td class="text-right text-[12px]" style="color: #86efac !important;">Rp {{ number_format($grandTotals['total_paid'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="text-center py-12 text-gray-400">
                <p class="text-lg font-semibold">Tidak ada data kunjungan</p>
                <p class="text-sm mt-1">Belum ada kunjungan dari Account Officer manapun untuk periode {{ $periodLabel }}</p>
            </div>
        @endif

        <!-- System Verification Note -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-[10px] text-gray-500 text-center italic">
            Dokumen ini di-generate secara otomatis oleh sistem pada {{ formatIndonesianDate(now()) }} {{ now()->format('H:i') }}.
            <br>
            Tidak memerlukan tanda tangan basah ("Mengetahui" atau "Dibuat Oleh") karena telah diverifikasi dan divalidasi oleh sistem.
        </div>
    </div>

</body>

</html>
