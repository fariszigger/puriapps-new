<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kinerja AO - {{ $aoUser->name }}</title>
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

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #4b5563;
        }

        .recap-table th,
        .recap-table td {
            border: 1px solid #4b5563;
            padding: 4px 6px;
            vertical-align: top;
        }

        .recap-table th {
            background-color: #d1d5db !important;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <!-- Print Button Bar -->
    <div class="no-print p-4 bg-gray-100 border-b flex items-center justify-between fixed top-0 w-full z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-gray-700">Detail Kinerja AO — {{ $aoUser->name }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.performance') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold text-sm">Kembali</a>
            <button onclick="window.print()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold text-sm">Cetak
                Detail</button>
        </div>
    </div>

    <div class="content-container mt-[70px] print:mt-0">

        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('build/assets/logobpr.png') }}" alt="Logo" class="h-10 w-auto object-contain">
            </div>
            <div class="text-right text-[10px] text-gray-500 italic">
                Dicetak: {{ now()->format('d M Y H:i') }}
            </div>
        </div>

        <div class="text-center mb-4">
            <h1 class="text-lg font-bold uppercase tracking-wider">DETAIL KINERJA KUNJUNGAN ACCOUNT OFFICER</h1>
            <p class="text-sm font-semibold text-gray-700 mt-1">Periode: {{ $periodLabel }}</p>
        </div>

        <!-- AO Header -->
        <div class="mb-3 mt-2 bg-gray-100 rounded px-3 py-2 border border-gray-300">
            <span class="font-bold text-sm">Account Officer: {{ $aoUser->name }}</span>
            @if($aoUser->code)
                <span class="text-gray-500 ml-1">({{ $aoUser->code }})</span>
            @endif
            <span class="float-right text-gray-500 text-[10px]">
                Total: {{ $totalVisits }} kunjungan
            </span>
        </div>

        @if($totalVisits > 0)
            <table class="recap-table mb-6">
                <thead>
                    <tr>
                        <th class="w-[30px]">No</th>
                        <th class="w-[90px]">Tanggal</th>
                        <th class="w-[50px]">Jam</th>
                        <th>Nama Nasabah</th>
                        <th class="w-[50px]">Kol</th>
                        <th class="w-[100px]">Ketemu</th>
                        <th class="w-[160px]">Hasil Penagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($dates as $date => $visits)
                        @foreach($visits as $idx => $visit)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        @if($idx === 0)
                                            <td rowspan="{{ count($visits) }}" class="text-center font-semibold align-middle"
                                                style="background-color: #f9fafb !important;">
                                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                                <br>
                                                <span
                                                    class="text-[9px] text-gray-500 font-normal">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</span>
                                            </td>
                                        @endif
                                        <td class="text-center text-gray-600">{{ $visit['time'] }}</td>
                                        <td class="font-semibold">{{ $visit['customer_name'] }}</td>
                                        <td class="text-center">
                                            @php
                                                $kolColors = ['1' => '', '2' => '', '3' => 'color:#c2410c;font-weight:bold', '4' => 'color:#dc2626;font-weight:bold', '5' => 'color:#dc2626;font-weight:bold'];
                                            @endphp
                             <span
                                                style="{{ $kolColors[$visit['kolektibilitas']] ?? '' }}">{{ $visit['kolektibilitas'] }}</span>
                                        </td>
                                        <td>{{ $visit['ketemu_dengan'] }}</td>
                                        <td>
                                            @if($visit['hasil_penagihan'] === 'bayar')
                                                <span style="color:#16a34a;font-weight:bold">Bayar</span>
                                                @if($visit['jumlah_bayar'])
                                                    — Rp {{ number_format($visit['jumlah_bayar'], 0, ',', '.') }}
                                                @endif
                                            @elseif($visit['hasil_penagihan'] === 'janji_bayar')
                                                <span style="color:#ea580c;font-weight:bold">Janji Bayar</span>
                                                @if($visit['tanggal_janji_bayar'])
                                                    — {{ \Carbon\Carbon::parse($visit['tanggal_janji_bayar'])->format('d/m/Y') }}
                                                @endif
                                                @if($visit['jumlah_pembayaran'])
                                                    <br><span class="text-[9px]">Rp
                                                        {{ number_format($visit['jumlah_pembayaran'], 0, ',', '.') }}</span>
                                                @endif
                                                @if($visit['janji_bayar_fulfilled'])
                                                    <br><span style="color:#16a34a;font-weight:bold;font-size:9px">✓ LUNAS</span>
                                                @endif
                                            @else
                                                <span style="color:#9ca3af">-</span>
                                            @endif
                                        </td>
                                    </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12 text-gray-400 border border-gray-300 rounded mb-6">
                <p class="text-lg font-semibold">Tidak ada data kunjungan</p>
                <p class="text-sm mt-1">Belum ada kunjungan yang tercatat untuk {{ $aoUser->name }} pada periode
                    {{ $periodLabel }}</p>
            </div>
        @endif

        <!-- System Verification Note -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-[10px] text-gray-500 text-center italic">
            Dokumen ini di-generate secara otomatis oleh sistem pada {{ now()->format('d F Y H:i') }}.
            <br>
            Tidak memerlukan tanda tangan basah ("Mengetahui" atau "Dibuat Oleh") karena telah diverifikasi dan divalidasi oleh sistem.
        </div>
    </div>

</body>

</html>