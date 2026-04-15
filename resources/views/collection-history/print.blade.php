<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print History Penagihan: {{ $customer->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/logo-icon.png') }}">

    <!-- Tailwind CSS (via CDN for print view ease) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-family: 'Inter', sans-serif;
            font-size: 10px;
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 8mm 12mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        @media print {
            body {
                background-color: white;
                font-size: 10px;
            }

            .a4-container {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid;
            }
        }

        .content-rich-text p, 
        .content-rich-text div {
            margin: 0 !important;
            padding: 0 !important;
            display: inline;
        }
    </style>
</head>

<body class="text-gray-900 font-sans antialiased py-8 print:py-0">

    <!-- Print / Close Controls -->
    <div class="max-w-[210mm] mx-auto mb-4 flex justify-end gap-3 no-print px-4 md:px-0">
        <button onclick="window.close()"
            class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-all flex items-center gap-2">
            Tutup
        </button>
        <button onclick="window.print()"
            class="px-5 py-1.5 text-xs font-bold text-white bg-blue-600 rounded hover:bg-blue-700 shadow-md transition-all flex items-center gap-2">
            Cetak Dokumen
        </button>
    </div>

    <!-- A4 Document -->
    <div class="a4-container !py-8 !px-10">
        <!-- Header: Matching Customer Visit Report Style -->
        <div class="flex items-center justify-between border-b-2 border-black pb-1 mb-2 mt-1">
            <div class="flex items-center gap-2">
                <img src="{{ asset('build/assets/logobpr.png') }}" alt="BPR Puri Logo"
                    class="h-10 w-auto object-contain">
            </div>
            <div class="text-[11px] text-right italic font-normal text-gray-500">
                History Penagihan / 
                {{ now()->translatedFormat('d F Y H:i') }}
            </div>
        </div>

        <div class="flex items-center justify-between border-b-2 border-black pb-1 mb-4 relative">
            <div class="w-full text-center">
                <h1 class="text-lg font-bold uppercase tracking-wider">RIWAYAT PENAGIHAN NASABAH</h1>
            </div>
        </div>

        <div class="mb-4 text-[11px] pl-10">
            <table class="w-full table-fixed">
                <tr>
                    <td class="font-bold pb-1 align-top w-44">Identitas Debitur</td>
                    <td class="w-4 pb-1 align-top">:</td>
                    <td class="pb-1 align-top">
                        <span class="font-bold text-[12px]">{{ $customer->name ?? '-' }}</span>
                        (NIK : {{ $customer->identity_number ?? '-' }})
                    </td>
                </tr>
                <tr>
                    <td class="font-bold pb-1 align-top">No. SPK / Rekening</td>
                    <td class="w-4 pb-1 align-top">:</td>
                    <td class="pb-1 align-top">
                        @php
                            $firstVisit = $history->firstWhere('type', 'visit');
                            $spkNumber = $firstVisit ? ($firstVisit['raw_data']->spk_number ?? '-') : '-';
                        @endphp
                        <span class="font-bold">{{ $spkNumber }}</span>
                    </td>
                </tr>

                <tr>
                    <td class="font-bold pb-1 align-top">No. Telepon</td>
                    <td class="w-4 pb-1 align-top">:</td>
                    <td class="pb-1 align-top">{{ $customer->phone_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-bold pb-1 align-top">Alamat</td>
                    <td class="w-4 pb-1 align-top">:</td>
                    <td class="pb-1 align-top">{{ $customer->address ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- History Content -->
        <div>
            <h2 class="text-xs font-bold text-gray-800 uppercase border-b border-gray-300 pb-1 mb-3">Daftar Riwayat Penagihan & Surat</h2>

            @if($history->isEmpty())
                <div class="p-6 text-center border border-dashed border-gray-300 rounded text-gray-500 text-xs italic">
                    Belum ada riwayat tercatat.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($history as $item)
                        @php
                            $isLetter = $item['type'] === 'letter';
                            $borderColor = $isLetter ? 'border-red-200' : 'border-gray-200';
                            $bgTitle = $isLetter ? 'bg-red-50' : 'bg-gray-50';
                        @endphp
                        <div class="border {{ $borderColor }} rounded overflow-hidden">
                            <!-- Item Header -->
                            <div class="{{ $bgTitle }} px-3 py-1 flex justify-between items-center border-b {{ $borderColor }}">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold {{ $isLetter ? 'text-red-700' : 'text-gray-700' }} uppercase">
                                        {{ $isLetter ? '📄 ' . $item['title'] : '📍 ' . $item['title'] }}
                                    </span>
                                    <span class="text-[9px] bg-white border px-1 rounded text-gray-500">
                                        {{ $isLetter ? 'Surat' : 'Kunjungan' }}
                                    </span>
                                </div>
                            <div class="flex items-center gap-3">
                                @if(!$isLetter)
                                    <span class="text-[9px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded">AO: {{ $item['ao'] }}</span>
                                @endif
                                <span class="text-[10px] font-bold text-gray-600">
                                    {{ \Carbon\Carbon::parse($item['display_date'])->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            </div>
                            
                            <!-- Item Body -->
                            <div class="p-2 text-[10px] leading-relaxed">
                                @if($isLetter)
                                    <div class="grid grid-cols-2 gap-2">
                                        <p><span class="text-gray-500 font-semibold">No. Surat:</span> {{ $item['raw_data']->letter_number }}</p>
                                        @if($item['raw_data']->tunggakan_amount)
                                            <p><span class="text-gray-500 font-semibold">Tunggakan:</span> <strong class="text-red-600">Rp {{ number_format($item['raw_data']->tunggakan_amount, 0, ',', '.') }}</strong></p>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-gray-700 italic">"{{ $item['details'] }}"</p>
                                @else
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <p class="text-gray-700 italic">"{{ $item['details'] }}"</p>
                                            @if($item['raw_data']->hasil_penagihan === 'bayar')
                                                <div class="mt-1.5 p-1.5 bg-green-50 border border-green-100 rounded text-[9px] flex justify-between items-center">
                                                    <span class="font-bold text-green-800">Pembayaran Langsung</span>
                                                    <span class="font-bold text-green-800">Rp {{ number_format($item['raw_data']->jumlah_bayar, 0, ',', '.') }}</span>
                                                </div>
                                            @elseif($item['raw_data']->hasil_penagihan === 'janji_bayar' && $item['raw_data']->tanggal_janji_bayar)
                                                <div class="mt-1.5 p-1.5 bg-orange-50 border border-orange-100 rounded text-[9px] flex flex-col gap-1">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-bold text-orange-800">Janji Bayar: {{ \Carbon\Carbon::parse($item['raw_data']->tanggal_janji_bayar)->translatedFormat('d M Y') }}</span>
                                                        <span class="font-bold text-orange-800">Rp {{ number_format($item['raw_data']->jumlah_pembayaran ?: $item['raw_data']->jumlah_bayar, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($item['raw_data']->janji_bayar_fulfilled)
                                                        <div class="bg-green-100 border border-green-200 text-green-800 px-1.5 py-1 rounded flex justify-between items-center mt-0.5">
                                                            <span class="font-bold">✅ Dibayar: {{ \Carbon\Carbon::parse($item['raw_data']->janji_bayar_fulfilled_at)->translatedFormat('d M Y') }}</span>
                                                            <span class="font-bold">Rp {{ number_format($item['raw_data']->jumlah_pembayaran, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($item['raw_data']->hasil_penagihan === 'tidak_ada_janji')
                                                <div class="mt-1.5 p-1.5 bg-red-50 border border-red-100 rounded text-[9px]">
                                                    <span class="font-bold text-red-800">Tidak Ada Janji</span>
                                                </div>
                                            @elseif($item['raw_data']->hasil_penagihan === 'janji_lainnya')
                                                <div class="mt-1.5 p-1.5 bg-yellow-50 border border-yellow-100 rounded text-[9px]">
                                                    <span class="font-bold text-yellow-800">Janji Lainnya:</span>
                                                    <span class="text-gray-700 italic border-l border-yellow-300 pl-1 ml-1">{{ $item['raw_data']->janji_lainnya_desc }}</span>
                                                </div>
                                            @endif

                                            @if($item['raw_data']->kondisi_saat_ini || $item['raw_data']->rencana_penyelesaian)
                                                <div class="mt-2 pl-2 border-l-2 border-gray-200 text-[9px] space-y-1 content-rich-text">
                                                    @if(trim(strip_tags($item['raw_data']->kondisi_saat_ini)) !== '')
                                                        <div class="flex gap-1">
                                                            <span class="text-gray-500 font-bold uppercase text-[7px] shrink-0 mt-0.5">Kondisi Debitur:</span> 
                                                            <div class="flex-1">{!! $item['raw_data']->kondisi_saat_ini !!}</div>
                                                        </div>
                                                    @endif
                                                    @if(trim(strip_tags($item['raw_data']->rencana_penyelesaian)) !== '')
                                                        <div class="flex gap-1">
                                                            <span class="text-gray-500 font-bold uppercase text-[7px] shrink-0 mt-0.5">Rencana Penyelesaian:</span> 
                                                            <div class="flex-1">{!! $item['raw_data']->rencana_penyelesaian !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @php
                                            $photos = [
                                                ['path' => $item['raw_data']->photo_path, 'label' => 'Foto Lokasi'],
                                                ['path' => $item['raw_data']->photo_rumah_path, 'label' => 'Foto Rumah'],
                                                ['path' => $item['raw_data']->photo_orang_path, 'label' => 'Foto Orang'],
                                            ];
                                            $availablePhotos = array_filter($photos, fn($p) => !empty($p['path']));
                                        @endphp

                                        @if(count($availablePhotos) > 0)
                                            <div class="shrink-0 flex gap-1.5 flex-wrap justify-end max-w-[220px]">
                                                @foreach($availablePhotos as $photo)
                                                    @php
                                                        $pathParts = explode('/', $photo['path']);
                                                        // Fallback if path format is unexpected
                                                        $type = count($pathParts) >= 2 ? $pathParts[count($pathParts)-2] : 'photos';
                                                        $filename = end($pathParts);
                                                    @endphp
                                                    <div class="text-center">
                                                        <img src="{{ route('media.customer-visits', ['type' => $type, 'filename' => $filename]) }}" 
                                                            alt="{{ $photo['label'] }}" 
                                                            class="w-16 h-16 object-cover rounded border border-gray-200">
                                                        <span class="text-[7px] text-gray-400 block mt-0.5 uppercase">{{ $photo['label'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="mt-1 text-[9px] text-gray-400 flex justify-end">
                                    Petugas: {{ $item['ao'] }}
                                    @if(isset($item['accompanying_names']) && $item['accompanying_names'])
                                        (Didampingi: {{ $item['accompanying_names'] }})
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-2 border-t border-gray-200 text-center">
            <p class="text-[8px] text-gray-400 italic">
                Dokumen ini di-generate secara otomatis oleh sistem pada {{ now()->translatedFormat('d F Y H:i') }}.<br>
                Tidak memerlukan tanda tangan basah ("Mengetahui" atau "Account Officer") karena telah diverifikasi dan divalidasi oleh sistem.
            </p>
        </div>
    </div>

</body>

</html>