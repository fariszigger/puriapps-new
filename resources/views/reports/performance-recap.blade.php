<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>Rekap Singkat Semua AO</title>
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
            <span class="text-sm font-bold text-gray-700">Rekap Kunjungan Semua AO</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.performance') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold text-sm">Kembali</a>
            <button onclick="window.print()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold text-sm">Cetak
                Rekap</button>
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
            <h1 class="text-lg font-bold uppercase tracking-wider">REKAP KUNJUNGAN SEMUA ACCOUNT OFFICER</h1>
            <p class="text-sm font-semibold text-gray-700 mt-1">Periode: {{ $periodLabel }}</p>
            
            <div class="mt-2 flex items-center justify-center gap-4">
                <span class="text-xs text-gray-500 font-bold uppercase tracking-tight">Total Keseluruhan: <span class="text-indigo-700 text-sm">{{ $totalVisitsOverall }}</span></span>
                <span class="text-xs text-gray-500 font-bold uppercase tracking-tight border-l border-gray-300 pl-4">Total Bayar: <span class="text-green-700 text-sm">Rp {{ number_format($totals['total_paid'] ?? 0, 0, ',', '.') }}</span></span>
                <div class="flex gap-2 text-[9px] uppercase font-bold tracking-tighter border-l border-gray-300 pl-4">
                    <span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded">Lancar: {{ $totals['kol_1'] }}</span>
                    <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded">DPK: {{ $totals['kol_2'] }}</span>
                    <span class="px-1.5 py-0.5 bg-orange-100 text-orange-800 rounded">KL: {{ $totals['kol_3'] }}</span>
                    <span class="px-1.5 py-0.5 bg-red-100 text-red-800 rounded">D: {{ $totals['kol_4'] }}</span>
                    <span class="px-1.5 py-0.5 bg-red-200 text-red-900 rounded">M: {{ $totals['kol_5'] }}</span>
                </div>
            </div>
        </div>

        @forelse($recapData as $aoData)
            @php $aoUser = $aoData['user']; @endphp

            @if(!$loop->first)
                <div class="page-break"></div>
            @endif

            <!-- AO Header -->
            <div class="mb-3 mt-2 bg-gray-100 rounded px-4 py-3 border border-gray-300 flex items-center justify-between">
                <div>
                    <span class="font-bold text-sm">Account Officer: {{ $aoUser->name ?? '-' }}</span>
                    @if($aoUser->code ?? null)
                        <span class="text-gray-500 ml-1">({{ $aoUser->code }})</span>
                    @endif
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex gap-2 text-[9px] uppercase font-bold tracking-tighter">
                        <span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded">Lancar: {{ $aoData['counts']['kol_1'] }}</span>
                        <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded">DPK: {{ $aoData['counts']['kol_2'] }}</span>
                        <span class="px-1.5 py-0.5 bg-orange-100 text-orange-800 rounded">KL: {{ $aoData['counts']['kol_3'] }}</span>
                        <span class="px-1.5 py-0.5 bg-red-100 text-red-800 rounded">D: {{ $aoData['counts']['kol_4'] }}</span>
                        <span class="px-1.5 py-0.5 bg-red-200 text-red-900 rounded">M: {{ $aoData['counts']['kol_5'] }}</span>
                    </div>
                    <div class="border-l border-gray-300 pl-4">
                        <span class="font-black text-sm text-indigo-700">TOTAL: {{ $aoData['counts']['total'] }}</span>
                    </div>
                </div>
            </div>

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
                    @foreach($aoData['dates'] as $date => $visits)
                        @foreach($visits as $idx => $visit)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    @if($idx === 0)
                                        <td rowspan="{{ count($visits) }}" class="text-center font-semibold align-middle"
                                            style="background-color: #f9fafb !important;">
                                            {{ formatIndonesianDate(\Carbon\Carbon::parse($date)) }}
                                            <br>
                                            <span
                                                class="text-[9px] text-gray-500 font-normal">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd') }}</span>
                                        </td>
                                    @endif
                                    <td class="text-center text-gray-600">{{ $visit['time'] }}</td>
                                    <td class="font-semibold">
                                            <div class="flex items-start gap-0 w-full">
                                                <div class="w-[28%] shrink-0 pr-2">
                                                    <span class="leading-tight">{{ $visit['customer_name'] }}</span>
                                                    @if($visit['address'])
                                                        <br>
                                                        <span class="text-[8px] text-gray-500 font-normal leading-tight inline-block mt-0.5" style="white-space: normal;">{{ $visit['address'] }}</span>
                                                    @endif
                                                </div>

                                                <div class="flex-1 px-2 border-l border-gray-100">
                                                    @if(!empty($visit['kondisi_saat_ini']))
                                                        <div class="mb-1">
                                                            <span class="text-[7px] uppercase font-bold text-gray-400 block leading-none">Kondisi:</span>
                                                            <div class="text-[8px] font-normal leading-tight text-gray-700">{!! Str::limit(strip_tags($visit['kondisi_saat_ini']), 100) !!}</div>
                                                        </div>
                                                    @endif
                                                    @if(!empty($visit['rencana_penyelesaian']))
                                                        <div>
                                                            <span class="text-[7px] uppercase font-bold text-gray-400 block leading-none">Rencana:</span>
                                                            <div class="text-[8px] font-normal leading-tight text-gray-700">{!! Str::limit(strip_tags($visit['rencana_penyelesaian']), 100) !!}</div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="w-[130px] shrink-0 ml-2">
                                                    @php
                                                        $photos = [
                                                            ['path' => $visit['photo_path'], 'label' => 'Lokasi'],
                                                            ['path' => $visit['photo_rumah_path'], 'label' => 'Rumah'],
                                                            ['path' => $visit['photo_orang_path'], 'label' => 'Orang'],
                                                        ];
                                                        $availablePhotos = array_filter($photos, fn($p) => !empty($p['path']));
                                                    @endphp
                                                    @if(count($availablePhotos) > 0)
                                                        <div class="flex gap-1.5 flex-nowrap justify-end">
                                                            @foreach($availablePhotos as $photo)
                                                                @php
                                                                    $pathParts = explode('/', $photo['path']);
                                                                    $type = count($pathParts) >= 2 ? $pathParts[count($pathParts)-2] : 'photos';
                                                                    $filename = end($pathParts);
                                                                @endphp
                                                                <div class="text-center relative group">
                                                                    <img src="{{ route('media.customer-visits', ['type' => $type, 'filename' => $filename]) }}" alt="{{ $photo['label'] }}" class="w-8 h-8 md:w-10 md:h-10 object-cover rounded border border-gray-300">
                                                                    <div class="hidden group-hover:block absolute top-1/2 right-full -translate-y-1/2 mr-2 z-50">
                                                                        <img src="{{ route('media.customer-visits', ['type' => $type, 'filename' => $filename]) }}" alt="{{ $photo['label'] }}" class="max-w-[200px] md:max-w-[300px] w-auto h-auto object-contain rounded-lg shadow-2xl border border-gray-200 bg-white p-1">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                    </td>
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
                                            @if(!empty($visit['is_duplicate_bayar']))
                                                <span style="color:#9ca3af;font-weight:bold;text-decoration:line-through">Bayar</span>
                                                @if($visit['jumlah_bayar'])
                                                    <span style="color:#9ca3af;text-decoration:line-through"> Rp {{ number_format($visit['jumlah_bayar'], 0, ',', '.') }}</span>
                                                @endif
                                                <br><span style="color:#f59e0b;font-size:8px;font-weight:bold">⚠ Tidak dihitung (sudah tercatat sebagai Sudah Bayar)</span>
                                            @else
                                                <span style="color:#16a34a;font-weight:bold">Bayar</span>
                                                @if($visit['jumlah_bayar'])
                                                    — Rp {{ number_format($visit['jumlah_bayar'], 0, ',', '.') }}
                                                @endif
                                            @endif
                                        @elseif($visit['hasil_penagihan'] === 'janji_bayar')
                                            @if(!empty($visit['janji_bayar_tidak_bayar']))
                                                <span style="color:#9ca3af;font-weight:bold;text-decoration:line-through">Janji Bayar</span>
                                                @if($visit['tanggal_janji_bayar'])
                                                    <span style="color:#9ca3af;text-decoration:line-through">— {{ formatIndonesianDate(\Carbon\Carbon::parse($visit['tanggal_janji_bayar'])) }}</span>
                                                @endif
                                                @if($visit['jumlah_pembayaran'])
                                                    <br><span style="color:#9ca3af;text-decoration:line-through;font-size:9px">Rp {{ number_format($visit['jumlah_pembayaran'], 0, ',', '.') }}</span>
                                                @endif
                                                <br><span style="color:#dc2626;font-weight:bold;font-size:9px">✗ TIDAK BAYAR pd. {{ $visit['janji_bayar_tidak_bayar_at'] ? \Carbon\Carbon::parse($visit['janji_bayar_tidak_bayar_at'])->format('d/m/Y') : '-' }}</span>
                                                @if(!empty($visit['janji_bayar_tidak_bayar_reason']))
                                                    <br><span style="color:#6b7280;font-size:8px;font-style:italic">Alasan: {{ $visit['janji_bayar_tidak_bayar_reason'] }}</span>
                                                @endif
                                            @else
                                                <span style="color:#ea580c;font-weight:bold">Janji Bayar</span>
                                                @if($visit['tanggal_janji_bayar'])
                                                    — {{ formatIndonesianDate(\Carbon\Carbon::parse($visit['tanggal_janji_bayar'])) }}
                                                @endif
                                                @if($visit['jumlah_pembayaran'])
                                                    <br><span class="text-[9px]">Rp
                                                        {{ number_format($visit['jumlah_pembayaran'], 0, ',', '.') }}</span>
                                                @endif
                                                @if($visit['janji_bayar_fulfilled'])
                                                    <br><span style="color:#16a34a;font-weight:bold;font-size:9px">✓ SUDAH BAYAR Rp {{ number_format($visit['jumlah_bayar_fulfilled'] ?? 0, 0, ',', '.') }} pd. {{ $visit['janji_bayar_fulfilled_at'] ? \Carbon\Carbon::parse($visit['janji_bayar_fulfilled_at'])->format('d/m/Y') : '-' }}</span>
                                                @endif
                                            @endif
                                        @elseif($visit['hasil_penagihan'] === 'tidak_ada_janji')
                                            <span style="color:#ef4444;font-weight:bold">Tidak Ada Janji</span>
                                        @elseif($visit['hasil_penagihan'] === 'janji_lainnya')
                                            <span style="color:#eab308;font-weight:bold">Janji Lainnya</span>
                                            @if(!empty($visit['janji_lainnya_desc']))
                                                <br><span class="text-[9px] text-gray-700">{{ $visit['janji_lainnya_desc'] }}</span>
                                            @endif
                                        @else
                                            <span style="color:#9ca3af">-</span>
                                        @endif
                                    </td>
                                </tr>
                        @endforeach
                    @endforeach
                    <tr style="background-color: #f3f4f6;">
                        <td colspan="6" class="text-right font-black uppercase text-[10px]">Total Bayar</td>
                        <td class="font-black text-[11px] text-green-700">Rp {{ number_format($aoData['counts']['total_paid'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-lg font-semibold">Tidak ada data kunjungan sama sekali</p>
                <p class="text-sm mt-1">Belum ada kunjungan dari Account Officer manapun untuk periode {{ $periodLabel }}
                </p>
            </div>
        @endforelse

        <!-- System Verification Note -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-[10px] text-gray-500 text-center italic">
            Dokumen ini di-generate secara otomatis oleh sistem pada {{ formatIndonesianDate(now()) }} {{ now()->format('H:i') }}.
            <br>
            Tidak memerlukan tanda tangan basah ("Mengetahui" atau "Dibuat Oleh") karena telah diverifikasi dan divalidasi oleh sistem.
        </div>
    </div>

</body>

</html>