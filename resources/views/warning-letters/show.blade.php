@extends('layouts.dashboard')

@section('title', $letter->type_label)

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('warning-letters.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">Daftar Surat</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $letter->type_short_label }}</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full max-w-4xl mx-auto mt-8 mb-8">
        {{-- Action Bar --}}
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('warning-letters.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white/60 border border-gray-300 rounded-lg hover:bg-white transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 transition-all shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak
            </button>
        </div>

        {{-- Letter Preview --}}
        <div id="letter-content" class="bg-white p-10 rounded-xl shadow-xl border border-gray-200 print:shadow-none print:border-none print:rounded-none" style="font-family: 'Times New Roman', serif;">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-1 text-sm">
                <div>
                    <p><strong>Nomor</strong> : {{ $letter->letter_number ?? '___/BPR.PURI.KRD/___/____' }}</p>
                </div>
                <div class="text-right">
                    <p>Mojokerto, {{ $letter->letter_date->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            {{-- Address --}}
            <div class="mt-6 mb-2 text-sm">
                <p>Kepada Yth.</p>
                <p><strong>Bapak/Ibu {{ $letter->customer->name ?? '____' }}</strong></p>
                <p>{{ $letter->customer->address ?? '____' }}</p>
            </div>

            {{-- Perihal --}}
            <div class="mb-6 text-sm">
                <p><strong>Perihal : {{ $letter->type_label }}</strong></p>
            </div>

            {{-- Body --}}
            <div class="text-sm leading-relaxed space-y-4">
                <p>Dengan hormat,</p>

                <p class="text-justify">
                    Menunjuk Perjanjian Kredit No. <strong>{{ $letter->credit_agreement_number ?? '____' }}</strong>
                    tanggal <strong>{{ $letter->credit_agreement_date ? $letter->credit_agreement_date->translatedFormat('d F Y') : '____' }}</strong>
                    antara PT. BANK PERKREDITAN RAKYAT PURISEGER SENTOSA selanjutnya disebut Bank dengan
                    <strong>{{ $letter->customer->name ?? '____' }}</strong> dan
                    @if($letter->previous_letter_number)
                        Surat Bank No. <strong>{{ $letter->previous_letter_number }}</strong> perihal {{ $letter->type === 'sp2' ? 'Peringatan I (Pertama)' : 'Peringatan II (Kedua)' }}, (copy terlampir),
                    @else
                        memperhatikan kondisi terakhir kredit,
                    @endif
                    dengan ini kami sampaikan hal-hal sebagai berikut:
                </p>

                <ol class="list-decimal pl-6 space-y-3 text-justify">
                    @if($letter->type === 'sp2' || $letter->type === 'sp3')
                        <li>
                            Bahwa pada tanggal <strong>{{ $letter->previous_letter_date ? $letter->previous_letter_date->translatedFormat('d-m-Y') : '____' }}</strong>,
                            Bank telah menerbitkan dan menyampaikan {{ $letter->type === 'sp2' ? 'Surat Peringatan I (Pertama)' : 'Surat Peringatan II (Kedua)' }} yang isinya meminta saudara untuk menyelesaikan tunggakan kewajiban kepada Bank yang jumlahnya pada saat itu sebesar
                            <strong>Rp. {{ $letter->previous_letter_amount ? number_format($letter->previous_letter_amount, 0, ',', '.') : '____' }},-</strong>
                            dengan batas pembayaran paling lambat tanggal <strong>{{ $letter->previous_letter_deadline ? $letter->previous_letter_deadline->translatedFormat('d-m-Y') : '____' }}</strong>.
                        </li>
                    @endif
                    <li>
                        @if($letter->type === 'sp2' || $letter->type === 'sp3')
                            Bahwa ternyata sampai dengan tanggal tersebut dalam butir 1 (satu) di atas, saudara belum juga menyelesaikan tunggakan kewajiban dimaksud maka kami menilai saudara **tidak memiliki itikad baik** untuk menyelesaikan kewajiban saudara kepada Bank, serta telah melakukan tindakan **ingkar janji (wanprestasi)** sesuai dengan ketentuan dalam Perjanjian Kredit.
                        @else
                            Bahwa sampai saat ini saudara belum menyelesaikan tunggakan kewajiban kepada Bank sesuai dengan kesepakatan yang tercantum dalam Perjanjian kredit yang telah saudara tanda tangani.
                        @endif
                    </li>
                    <li>
                        Jumlah tunggakan kewajiban saudara posisi tanggal
                        <strong>{{ $letter->tunggakan_date ? $letter->tunggakan_date->translatedFormat('d-m-Y') : '____' }}</strong>
                        adalah sebesar <strong>Rp. {{ $letter->tunggakan_amount ? number_format($letter->tunggakan_amount, 0, ',', '.') : '____' }},-</strong>
                        <br>Jumlah tunggakan tersebut akan terus bertambah sampai saudara melakukan penyelesaian.
                    </li>
                    <li>
                        Untuk menghindari pembebanan denda atas keterlambatan yang akan semakin memberatkan saudara, serta agar tidak menambah kerugian bagi Bank, kami sangat mengharapkan agar saudara segera menyelesaikan seluruh tunggakan dimaksud,
                        @if($letter->deadline_date)
                            <strong>paling lambat tanggal {{ $letter->deadline_date->translatedFormat('d F Y') }}</strong>.
                        @else
                            <strong>paling lambat tanggal ____</strong>.
                        @endif
                    </li>
                </ol>

                <p class="text-justify">
                    Demikian {{ $letter->type_label }} ini kami sampaikan untuk menjadi perhatian saudara. Selanjutnya kami menunggu penyelesaian saudara. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
                </p>
            </div>

            {{-- Signature --}}
            <div class="mt-10 text-sm text-right">
                <p>Hormat kami,</p>
                <p>Bank Perekonomian Rakyat</p>
                <p>PURISEGER SENTOSA</p>
                <div class="h-20"></div>
                <p class="font-bold">[____________________]</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                nav, footer, #bottom-nav-bar, .print\\:hidden, [class*="breadcrumb"] {
                    display: none !important;
                }
                body {
                    background: white !important;
                }
                .pt-20 {
                    padding-top: 0 !important;
                }
                #letter-content {
                    margin: 0;
                    padding: 2cm;
                }
            }
        </style>
    @endpush
@endsection
