<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print History Penagihan: {{ $customer->name }}</title>

    <!-- Tailwind CSS (via CDN for print view ease) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 20mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        @media print {
            body {
                background-color: white;
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
        }
    </style>
</head>

<body class="text-gray-900 font-sans antialiased py-8 print:py-0">

    <!-- Print / Close Controls -->
    <div class="max-w-[210mm] mx-auto mb-6 flex justify-end gap-3 no-print px-4 md:px-0">
        <button onclick="window.close()"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Tutup
        </button>
        <button onclick="window.print()"
            class="px-6 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-md transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Cetak Dokumen
        </button>
    </div>

    <!-- A4 Document -->
    <div class="a4-container">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-gray-800 pb-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-100 flex items-center justify-center rounded-xl border border-blue-200">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase">History Penagihan Nasabah
                    </h1>
                    <p class="text-sm font-medium text-gray-500 line-clamp-1 mt-1">Dicetak pada:
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aplikasi</p>
                <p class="text-lg font-black text-blue-700 tracking-tight">KRD System</p>
            </div>
        </div>

        <!-- Customer Identity Box -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 mb-8 flex flex-col md:flex-row gap-6">
            <div class="flex-1 space-y-3">
                <h2
                    class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2 mb-3">
                    Identitas Nasabah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Nama Lengkap</p>
                        <p class="text-base font-bold text-gray-900">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">No. Telepon</p>
                        <p class="text-sm font-medium text-gray-900">{{ $customer->phone_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Perjanjian Kredit / Plafon</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $customer->evaluations->first()->credit_agreement_number ?? 'Belum ada Evaluasi' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Account Officer (AO)</p>
                        <p
                            class="text-sm font-bold text-blue-700 inline-flex items-center px-2 py-0.5 rounded bg-blue-100 border border-blue-200 mt-0.5">
                            {{ $customer->user->name ?? '-' }}
                        </p>
                    </div>
                    <div class="md:col-span-2 mt-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Alamat Sesuai KTP</p>
                        <p class="text-sm font-medium text-gray-800 leading-relaxed">{{ $customer->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Timeline -->
        <div>
            <h2 class="text-lg font-bold text-gray-900 border-b-2 border-gray-100 pb-3 mb-6">Riwayat Tindakan
                Administratif & Penagihan</h2>

            @if($history->isEmpty())
                <div class="p-8 text-center bg-gray-50 border border-dashed border-gray-300 rounded-xl">
                    <p class="text-gray-500 font-medium">Belum ada riwayat kunjungan maupun surat peringatan untuk nasabah
                        ini.</p>
                </div>
            @else
                <div class="relative border-l-2 border-gray-200 ml-4 md:ml-6 space-y-8 pb-4">
                    @foreach($history as $item)
                        @php
                            $isLetter = $item['type'] === 'letter';
                            $iconColor = $isLetter ? 'text-red-600 bg-red-100 border-red-200' : 'text-orange-600 bg-orange-100 border-orange-200';
                            $badgeColor = $isLetter ? 'bg-red-50 text-red-700 border-red-200' : 'bg-orange-50 text-orange-700 border-orange-200';
                        @endphp
                        <div class="relative pl-6 sm:pl-8">
                            <!-- Timeline Dot Icon -->
                            <span
                                class="absolute -left-[17px] sm:-left-[17px] top-1 flex items-center justify-center w-8 h-8 rounded-full border-2 bg-white {{ $iconColor }} shadow-sm">
                                @if($isLetter)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </span>

                            <!-- Content -->
                            <div
                                class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-base font-bold text-gray-900">{{ $item['title'] }}</h3>
                                        <span
                                            class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded border {{ $badgeColor }}">
                                            {{ $isLetter ? 'Surat Penagihan' : 'Kunjungan Lapangan' }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-sm font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200">
                                        {{ \Carbon\Carbon::parse($item['display_date'])->translatedFormat('d F Y') }}
                                    </span>
                                </div>

                                <div
                                    class="text-sm text-gray-700 bg-gray-50/50 p-4 rounded-lg border border-gray-100 leading-relaxed min-h-[60px]">
                                    @if($isLetter)
                                        <p class="font-medium text-gray-900 mb-1">Informasi Penerbitan Surat:</p>
                                        <p>Nomor Surat: <span
                                                class="font-mono bg-gray-100 px-1 py-0.5 rounded border border-gray-200">{{ $item['raw_data']->letter_number }}</span>
                                        </p>
                                        @if($item['raw_data']->tunggakan_amount)
                                            <p>Nominal Tunggakan: <strong class="text-red-600">Rp
                                                    {{ number_format($item['raw_data']->tunggakan_amount, 0, ',', '.') }}</strong></p>
                                        @endif
                                        <p>Catatan: <span class="italic">{{ $item['details'] }}</span></p>
                                    @else
                                        <p class="font-medium text-gray-900 mb-1">Kesimpulan Kunjungan:</p>
                                        <p class="italic text-gray-600">"{{ $item['details'] }}"</p>

                                        @if($item['raw_data']->tanggal_janji_bayar)
                                            <div
                                                class="mt-3 p-3 bg-orange-50 border border-orange-100 rounded-lg flex items-start gap-3">
                                                <svg class="w-5 h-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-bold text-orange-800">Terdapat Janji Bayar</p>
                                                    <p class="text-xs text-orange-700 mt-0.5">Direncanakan pada:
                                                        <strong>{{ \Carbon\Carbon::parse($item['raw_data']->tanggal_janji_bayar)->translatedFormat('d F Y') }}</strong>
                                                        (Rp {{ number_format($item['raw_data']->jumlah_bayar, 0, ',', '.') }})</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <p class="text-xs text-gray-500">Petugas / AO terkait:</p>
                                    <p class="text-xs font-bold text-gray-700">{{ $item['ao'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Print Footer -->
        <div class="mt-12 pt-6 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">Dokumen ini dicetak dari Sistem Informasi Kredit (KRD System) BPR Puri.</p>
        </div>
    </div>

</body>

</html>