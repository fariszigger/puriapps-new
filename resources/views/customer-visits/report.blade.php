<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan - {{ $visit->customer->name ?? 'N/A' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
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
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #4b5563;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #4b5563;
            padding: 3px 6px;
            vertical-align: top;
        }

        .nested-table td {
            border: none !important;
        }

        .header-bg {
            background-color: #9ca3af !important;
            font-weight: bold;
        }

        .subheader-bg {
            background-color: #d1d5db !important;
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
    </style>
</head>

<body>

    <!-- Print Button Bar (Hidden on print) -->
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
            <a href="{{ route('customer-visits.index') }}"
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
                                Kunjungan #{{ $visit->penagihan_ke ?? '-' }} /
                                {{ $visit->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4 relative">
                            <div class="w-full text-center">
                                <h1 class="text-lg font-bold uppercase tracking-wider">LAPORAN KUNJUNGAN NASABAH</h1>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>

            <!-- MAIN CONTENT -->
            <tbody>
                <tr>
                    <td>
                        <!-- Info Memo -->
                        <div class="mb-4 text-xs flex justify-between items-start">
                            <div class="flex-1">
                                <table class="w-full ml-10">
                                    <tr>
                                        <td class="font-bold w-48 pb-1">Account Officer (AO)</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1"><span
                                                class="font-bold">{{ $visit->user->name ?? '-' }}</span>
                                            ({{ $visit->user->code ?? '-' }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Identitas Debitur</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1"><span
                                                class="font-bold">{{ $visit->customer->name ?? '-' }}</span> (NIK : {{ $visit->customer->identity_number ?? '-' }})</td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Tanggal Kunjungan</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1">{{ $visit->created_at->format('d F Y, H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <td class="font-bold pb-1">Penagihan Ke</td>
                                        <td class="w-4 pb-1">:</td>
                                        <td class="pb-1"><span
                                                class="font-bold">{{ $visit->penagihan_ke ?? '-' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- SECTION A: DATA KUNJUNGAN -->
                        <h2 class="font-bold text-lg mb-2">A. DATA KUNJUNGAN</h2>

                        <table class="report-table text-[10px]">
                            <!-- IDENTITAS NASABAH -->
                            <tr>
                                <td colspan="4" class="header-bg text-xs uppercase py-1">IDENTITAS NASABAH</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Nama</td>
                                <td class="w-[25%] pb-1">: {{ $visit->customer->name ?? '-' }}</td>
                                <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">No. KTP</td>
                                <td class="w-[25%] pb-1">: {{ $visit->customer->identity_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Alamat KTP</td>
                                <td colspan="3" class="pb-1">: {{ $visit->customer->address ?? '-' }}</td>
                            </tr>

                            <!-- DETAIL KUNJUNGAN -->
                            <tr>
                                <td colspan="4" class="header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                    DETAIL KUNJUNGAN</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Kolektibilitas</td>
                                <td class="w-[25%] pb-1">:
                                    @php
                                        $kolLabels = ['1' => '1 - Lancar', '2' => '2 - DPK', '3' => '3 - Kurang Lancar', '4' => '4 - Diragukan', '5' => '5 - Macet'];
                                    @endphp
                                    <span
                                        class="{{ in_array($visit->kolektibilitas, ['3','4','5']) ? 'text-red-600 font-bold' : 'font-bold' }}">
                                        {{ $kolLabels[$visit->kolektibilitas] ?? $visit->kolektibilitas }}
                                    </span>
                                </td>
                                <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">Bertemu Dengan</td>
                                <td class="w-[25%] pb-1">: 
                                    @if($visit->ketemu_dengan === 'Suami/Istri')
                                        @if($visit->customer->gender === 'Laki-Laki')
                                            Istri debitur
                                        @else
                                            Suami debitur 
                                        @endif
                                    @else
                                        {{ $visit->ketemu_dengan }}
                                    @endif
                                    @if($visit->ketemu_dengan !== 'Debitur' && $visit->nama_orang_ditemui)
                                        an. {{ $visit->nama_orang_ditemui }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Penagihan Ke</td>
                                <td class="pb-1">: <span
                                        class="font-bold">{{ $visit->penagihan_ke ?? '-' }}</span></td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Tanggal</td>
                                <td class="pb-1">: {{ $visit->created_at->format('d-M-Y H:i') }}</td>
                            </tr>

                            <!-- HASIL PENAGIHAN -->
                            <tr>
                                <td colspan="4" class="header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                    HASIL PENAGIHAN</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Status</td>
                                <td colspan="3" class="pb-1">:
                                    @if($visit->hasil_penagihan === 'bayar')
                                        <span class="font-bold text-green-700">BAYAR</span> — Rp
                                        {{ number_format($visit->jumlah_bayar ?? 0, 0, ',', '.') }}
                                    @elseif($visit->hasil_penagihan === 'janji_bayar')
                                        <span class="font-bold text-orange-600">JANJI BAYAR</span> — Tanggal:
                                        {{ $visit->tanggal_janji_bayar ? \Carbon\Carbon::parse($visit->tanggal_janji_bayar)->format('d F Y') : '-' }}
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- ALAMAT KUNJUNGAN -->
                            <tr>
                                <td colspan="4" class="header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                    ALAMAT KUNJUNGAN</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Alamat yang dikunjungi</td>
                                <td colspan="3" class="pb-1">: {{ $visit->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="w-[25%] border-r-0 pb-1">Kelurahan/Desa</td>
                                <td class="w-[25%] pb-1">: {{ $visit->village ?? '-' }}</td>
                                <td class="w-[25%] border-r-0 border-l border-gray-600 pb-1">Kecamatan</td>
                                <td class="w-[25%] pb-1">: {{ $visit->district ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="border-r-0 pb-1">Kabupaten/Kota</td>
                                <td class="pb-1">: {{ $visit->regency ?? '-' }}</td>
                                <td class="border-r-0 border-l border-gray-600 pb-1">Provinsi</td>
                                <td class="pb-1">: {{ $visit->province ?? '-' }}</td>
                            </tr>

                            <!-- LOKASI PETA -->
                            @if($visit->location_image_path || ($visit->latitude && $visit->longitude))
                                <tr>
                                    <td colspan="4"
                                        class="header-bg text-xs uppercase py-1 border-t-2 border-gray-600">
                                        LOKASI KUNJUNGAN</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="p-2 border-0">
                                        <div class="flex gap-4 items-start w-full">
                                            <!-- Map Details -->
                                            <div class="flex-1 text-[10px]">
                                                <table class="w-full text-left nested-table m-0">
                                                    <tr>
                                                        <td class="w-[30%] py-1 pl-2 font-bold">Koordinat</td>
                                                        <td class="py-1">:
                                                            {{ $visit->latitude ?? '-' }},
                                                            {{ $visit->longitude ?? '-' }}
                                                        </td>
                                                    </tr>
                                                    @if($visit->latitude && $visit->longitude)
                                                        <tr>
                                                            <td class="w-[30%] py-1 pl-2 font-bold align-top">Tautan
                                                                Peta</td>
                                                            <td class="py-1">: <span
                                                                    class="text-blue-600 break-all text-[8px]">https://www.google.com/maps/search/?api=1&query={{ $visit->latitude }},{{ $visit->longitude }}</span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>

                                            <!-- Map Image & QR Code -->
                                            <div class="flex gap-2">
                                                @if($visit->location_image_path)
                                                    <div
                                                        class="border border-gray-400 p-[2px] bg-white w-48 h-24 overflow-hidden relative">
                                                        <img src="{{ route('media.customer-visits', ['type' => 'map', 'filename' => basename($visit->location_image_path)]) }}"
                                                            alt="Peta Lokasi" class="w-full h-full object-cover">
                                                    </div>
                                                @endif

                                                @if($visit->latitude && $visit->longitude)
                                                    <div class="border border-gray-400 p-[2px] bg-white w-24 h-24">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $visit->latitude . ',' . $visit->longitude) }}"
                                                            alt="QR Code Lokasi"
                                                            class="w-full h-full object-contain">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </table>

                        <!-- SECTION B: KONDISI & RENCANA -->
                        <h2 class="font-bold text-lg mb-2 mt-4">B. KONDISI & RENCANA PENYELESAIAN</h2>

                        <table class="report-table text-[10px]">
                            <tr>
                                <td colspan="2" class="header-bg text-xs uppercase py-1">KONDISI SAAT INI</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="p-3 text-justify leading-relaxed">
                                    {!! $visit->kondisi_saat_ini ?? '<span class="text-gray-400 italic">Tidak ada data</span>' !!}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"
                                    class="header-bg text-xs uppercase py-1 border-t-2 border-gray-600">RENCANA
                                    PENYELESAIAN</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="p-3 text-justify leading-relaxed">
                                    {!! $visit->rencana_penyelesaian ?? '<span class="text-gray-400 italic">Tidak ada data</span>' !!}
                                </td>
                            </tr>
                        </table>

                        <!-- FOTO KUNJUNGAN -->
                        @if($visit->photo_path)
                            <h2 class="font-bold text-lg mb-2 mt-4">C. DOKUMENTASI KUNJUNGAN</h2>
                            <table class="report-table text-[10px]">
                                <tr>
                                    <td class="header-bg text-xs uppercase py-1">FOTO KUNJUNGAN</td>
                                </tr>
                                <tr>
                                    <td class="p-3 text-center">
                                        <div
                                            class="border border-gray-400 p-[2px] bg-white inline-block overflow-hidden"
                                            style="width: 8cm; height: 6cm;">
                                            <img src="{{ route('media.customer-visits', ['type' => 'photos', 'filename' => basename($visit->photo_path)]) }}"
                                                alt="Foto Kunjungan" class="w-full h-full object-cover">
                                        </div>
                                        <p class="text-[8px] text-gray-500 mt-1">Foto Dokumentasi Kunjungan -
                                            {{ $visit->created_at->format('d M Y') }}</p>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- TANDA TANGAN -->
                        <div class="mt-8 text-[10px]">
                            <table class="w-full">
                                <tr>
                                    <td class="w-1/2 text-center pt-2">
                                        <p class="mb-1">Mengetahui,</p>
                                        <p class="font-bold">Kepala Bagian</p>
                                        <div class="h-16"></div>
                                        <p class="border-t border-black inline-block px-8 pt-1 font-bold">
                                            Moch. Arif Priyadi</p>
                                    </td>
                                    <td class="w-1/2 text-center pt-2">
                                        <p class="mb-1">{{ $visit->created_at->format('d F Y') }}</p>
                                        <p class="font-bold">Account Officer</p>
                                        <div class="h-16"></div>
                                        <p class="border-t border-black inline-block px-8 pt-1 font-bold">
                                            {{ $visit->user->name ?? '-' }}</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
