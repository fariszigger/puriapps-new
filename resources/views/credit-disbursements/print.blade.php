<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pencairan Kredit {{ $viewMode === 'yearly' ? 'Tahunan' : 'Bulanan' }} - {{ $filterMonth }}</title>
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
            <h1 class="text-lg font-bold uppercase tracking-wider underline">LAPORAN PENCAIRAN KREDIT {{ $viewMode === 'yearly' ? 'TAHUNAN' : 'BULANAN' }}</h1>
            <p class="text-sm font-semibold text-gray-700 mt-1">
                @if($viewMode === 'yearly')
                    Tahun: {{ date('Y', strtotime($filterMonth)) }}
                @else
                    Bulan: {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}
                @endif
            </p>
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

        @php $globalNo = 1; @endphp
        @forelse($groupedDisbursements as $branchName => $aoGroups)
            <div class="mb-8 border-l-[10px] border-emerald-600 pl-4 bg-emerald-50/30 py-2">
                <h2 class="text-base font-black uppercase tracking-[0.1em] text-emerald-900 leading-none">Pencairan: Kantor {{ $branchName }}</h2>
            </div>

            @foreach($aoGroups as $aoCode => $items)
                <div class="mb-4 mt-8 flex items-center justify-between border-b border-gray-300 pb-1">
                    <h3 class="text-sm font-bold text-gray-800">Account Officer: <span class="bg-gray-800 text-white px-2 py-0.5 rounded text-[10px] ml-1 mr-1">{{ $aoCode }}</span> — {{ $items->first()->user->name ?? '-' }}</h3>
                    <span class="text-[10px] text-gray-500 font-medium italic">{{ $items->count() }} Transaksi</span>
                </div>

                <table class="recap-table mb-2 relative">
                    <thead>
                        <tr>
                            <th class="w-[30px] text-center">No</th>
                            <th class="w-[100px]">SPK</th>
                            <th class="w-[80px]">Tanggal</th>
                            <th>Nama Nasabah & Alamat</th>
                            <th class="w-[100px] text-right">Jumlah (Rp)</th>
                            <th class="w-[60px] text-right">Tenor</th>
                            <th class="w-[60px] text-right">Bunga</th>
                            <th class="w-[100px] text-right">Angsuran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="text-center">{{ $globalNo++ }}</td>
                                <td class="font-mono text-[9px]">{{ $item->nomor_spk ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->disbursement_date)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="font-semibold">{{ $item->customer_name }}</div>
                                    @if($item->address)
                                        <div class="text-[9px] text-gray-500 leading-tight">{{ $item->address }}</div>
                                    @endif
                                </td>
                                <td class="text-right font-mono font-semibold">{{ number_format($item->amount, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $item->jangka_waktu }} bln</td>
                                <td class="text-right">{{ number_format($item->suku_bunga, 2, ',', '.') }}%</td>
                                <td class="text-right font-mono">{{ number_format($item->angsuran, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php 
                            $baseTarget = $items->first()->user->disbursement_target ?? 400000000;
                            $aoTarget = $viewMode === 'yearly' ? $baseTarget * 12 : $baseTarget;
                            $realization = $items->sum('amount');
                            $diff = $aoTarget - $realization;
                        @endphp
                        <tr class="bg-gray-100/80">
                            <th colspan="4" class="text-right py-2 pr-4 uppercase text-[9px] font-black text-gray-700">RINGKASAN ({{ $aoCode }}):</th>
                            <th class="text-right py-2 text-[10px] font-black font-mono border-x border-gray-400">
                                <span class="block text-[8px] text-gray-500 font-normal">REALISASI</span>
                                Rp {{ number_format($realization, 0, ',', '.') }}
                            </th>
                            <th colspan="2" class="text-center py-2 text-[10px] font-black font-mono border-x border-gray-400">
                                <span class="block text-[8px] text-gray-500 font-normal">{{ $viewMode === 'yearly' ? 'LIMIT TAHUNAN' : 'LIMIT BULANAN' }}</span>
                                Rp {{ number_format($aoTarget, 0, ',', '.') }}
                            </th>
                            <th class="text-right py-2 text-[10px] font-black font-mono border-l border-gray-400 {{ $diff > 0 ? 'text-red-700 bg-red-50' : 'text-emerald-700 bg-emerald-50' }}">
                                <span class="block text-[8px] text-gray-500 font-normal uppercase">{{ $diff > 0 ? 'KEKURANGAN' : 'SURPLUS' }}</span>
                                Rp {{ number_format(abs($diff), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            @endforeach
        @empty
            <div class="text-center py-20 text-gray-400 border border-dashed border-gray-300 rounded mb-6">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-bold text-gray-600">Tidak ada data pencairan</p>
                <p class="text-sm mt-1 max-w-xs mx-auto">Belum ada pencairan yang tercatat pada kriteria pencarian Anda.</p>
            </div>
        @endforelse

        {{-- Final Grand Total Summary --}}
        @if($disbursements->count() > 0)
            <div class="mt-8 pt-4 border-t-2 border-black">
                <div class="flex justify-end gap-x-12">
                    <div class="text-right">
                        <span class="text-xs text-gray-500 uppercase font-bold pr-2">Total Transaksi:</span>
                        <span class="text-sm font-black">{{ $disbursements->count() }} Pencairan</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 uppercase font-bold pr-2">Total Seluruh Realisasi:</span>
                        <span class="text-base font-black font-mono bg-gray-900 text-white px-3 py-1 rounded">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
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
