<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pencairan Kredit - {{ $filterMonth }}</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/logo-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
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

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            background-color: #f3f4f6;
        }

        .content-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 8mm;
            min-height: 297mm;
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
            padding: 6px 8px;
            vertical-align: top;
        }

        .recap-table th {
            background-color: #d1d5db !important;
            font-weight: bold;
            text-align: left;
        }
    </style>
</head>

<body>

    <!-- Print Button Bar -->
    <div class="no-print p-4 bg-gray-100 border-b flex items-center justify-between fixed top-0 w-full z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-gray-700">Laporan Pencairan Kredit</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold text-sm">Tutup</button>
            <button onclick="window.print()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold text-sm">Cetak
                Laporan</button>
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

        <div class="text-center mb-6">
            <h1 class="text-lg font-bold uppercase tracking-wider">LAPORAN PENCAIRAN KREDIT</h1>
            <p class="text-sm font-semibold text-gray-700 mt-1">Bulan: {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}</p>
            @if($filterAo)
                <p class="text-sm font-semibold text-gray-700 mt-1">Account Officer: {{ $disbursements->first() ? $disbursements->first()->user->name : '-' }}</p>
            @endif
        </div>

        <!-- Summary -->
        <div class="flex justify-between bg-gray-100 rounded px-4 py-3 border border-gray-300 mb-6">
            <div>
                <span class="block text-gray-600 text-xs uppercase font-bold tracking-wider mb-1">Total Target Realisasi</span>
                <span class="text-lg font-black font-mono">Rp {{ number_format($totalTarget, 0, ',', '.') }}</span>
            </div>
            <div class="text-right">
                <span class="block text-gray-600 text-xs uppercase font-bold tracking-wider mb-1">Total Capaian Realisasi</span>
                <span class="text-lg font-black font-mono {{ $totalAmount >= $totalTarget ? 'text-green-700' : 'text-gray-900' }}">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
            <div class="text-right">
                <span class="block text-gray-600 text-xs uppercase font-bold tracking-wider mb-1">Persentase</span>
                <span class="text-lg font-black font-mono {{ $totalAmount >= $totalTarget ? 'text-green-700' : 'text-orange-600' }}">
                    {{ $totalTarget > 0 ? min(100, round(($totalAmount / $totalTarget) * 100, 1)) : 0 }}%
                </span>
            </div>
        </div>

        @if($disbursements->count() > 0)
            <table class="recap-table mb-6 relative">
                <thead>
                    <tr>
                        <th class="w-[30px] text-center">No</th>
                        <th class="w-[100px]">SPK</th>
                        <th class="w-[80px]">Tanggal</th>
                        @if(!$filterAo)
                            <th class="w-[50px]">AO</th>
                        @endif
                        <th>Nama Nasabah & Alamat</th>
                        <th class="w-[100px] text-right">Jumlah (Rp)</th>
                        <th class="w-[60px] text-right">Tenor</th>
                        <th class="w-[60px] text-right">Bunga</th>
                        <th class="w-[100px] text-right">Angsuran</th>
                        <th class="w-[100px]">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($disbursements as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="font-mono text-[9px]">{{ $item->nomor_spk ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->disbursement_date)->format('d/m/Y') }}</td>
                            @if(!$filterAo)
                                <td class="font-bold">{{ $item->user->code ?? '-' }}</td>
                            @endif
                            <td>
                                <div class="font-semibold">{{ $item->customer_name }}</div>
                                @if($item->address)
                                    <div class="text-[9px] text-gray-500 leading-tight">{{ $item->address }}</div>
                                @endif
                            </td>
                            <td class="text-right font-mono">{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $item->jangka_waktu }} bln</td>
                            <td class="text-right">{{ number_format($item->suku_bunga, 2, ',', '.') }}%</td>
                            <td class="text-right font-mono">{{ number_format($item->angsuran, 0, ',', '.') }}</td>
                            <td class="text-[10px] text-gray-700">{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="{{ $filterAo ? 4 : 5 }}" class="text-right py-3 pr-4 uppercase text-sm">Total Realisasi:</th>
                        <th class="text-right py-3 text-sm font-mono whitespace-nowrap bg-gray-50 !important">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                        <th colspan="4" class="bg-gray-50 !important"></th>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="text-center py-12 text-gray-400 border border-gray-300 rounded mb-6">
                <p class="text-lg font-semibold">Tidak ada data pencairan</p>
                <p class="text-sm mt-1">Belum ada pencairan yang tercatat pada kriteria ini.</p>
            </div>
        @endif

        <!-- System Verification Note -->
        <div class="mt-12 pt-4 border-t border-gray-300 text-[10px] text-gray-500 text-center italic">
            Dokumen ini di-generate secara otomatis oleh sistem pencatatan pencairan kredit pada {{ now()->format('d F Y H:i') }}.
            <br>
            Tidak memerlukan tanda tangan basah karena telah diverifikasi dan divalidasi oleh sistem.
        </div>
    </div>

</body>
</html>
