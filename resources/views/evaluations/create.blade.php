@extends('layouts.dashboard')

@section('title', 'Buat Evaluasi Baru')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        .cropper-view-box,
        .cropper-face {
            border-radius: 0;
        }

        #map {
            height: 400px;
            width: 100%;
            border-radius: 1rem;
            border: 2px solid #e5e7eb;
            z-index: 10;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('evaluations.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">Daftar Evaluasi</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Buat Evaluasi Baru</span>
        </div>
    </li>

@endsection

@section('content')
    <!-- Main Alpine Wrapper (Lifted State) -->
    @push('scripts')
        <script>
            window.scoringComponents = {{ Js::from($scoringComponents) }};
            window.economicSectors = {{ Js::from($economicSectors) }};
            window.nonBankThirdParties = {{ Js::from($nonBankThirdParties) }};
        </script>
        @include('evaluations.partials.form-script')
    @endpush
    <div x-data="evaluationForm"
        x-init="$watch('selectedCustomer', value => { if(!value) showModal = true }); $watch('search', () => { currentPage = 1 })">

        <div
            class="w-full max-w-7xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 relative z-10">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6">Buat Evaluasi Baru</h1>

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Steps Indicator -->
            <ol
                class="flex items-center w-full mb-8 text-sm font-medium text-gray-500 sm:text-base border-b overflow-x-auto pb-4 custom-scrollbar">
                <!-- Step 1 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 1"
                    :class="currentStep === 1 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 1 ? 'border-blue-600' : (currentStep > 1 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        1
                    </span>
                    <span :class="currentStep === 1 ? 'font-bold' : ''">Pinjaman</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 2 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 2"
                    :class="currentStep === 2 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 2 ? 'border-blue-600' : (currentStep > 2 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        2
                    </span>
                    <span :class="currentStep === 2 ? 'font-bold' : ''">Data SLIK / IDeb</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 3 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 3"
                    :class="currentStep === 3 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 3 ? 'border-blue-600' : (currentStep > 3 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        3
                    </span>
                    <span :class="currentStep === 3 ? 'font-bold' : ''">Analisa Keuangan</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 4 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 4"
                    :class="currentStep === 4 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 4 ? 'border-blue-600' : (currentStep > 4 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        4
                    </span>
                    <span :class="currentStep === 4 ? 'font-bold' : ''">Neraca / Aset</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 5 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 5"
                    :class="currentStep === 5 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 5 ? 'border-blue-600' : (currentStep > 5 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        5
                    </span>
                    <span :class="currentStep === 5 ? 'font-bold' : ''">Agunan</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 6 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 6"
                    :class="currentStep === 6 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 6 ? 'border-blue-600' : (currentStep > 6 ? 'border-green-500 bg-green-50 text-green-600' : 'border-gray-500')">
                        6
                    </span>
                    <span :class="currentStep === 6 ? 'font-bold' : ''">Scoring 5C</span>
                    <svg class="w-3 h-3 mx-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 12 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                    </svg>
                </li>

                <!-- Step 7 -->
                <li class="flex items-center whitespace-nowrap cursor-pointer" @click="currentStep = 7"
                    :class="currentStep === 7 ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500'">
                    <span class="flex items-center justify-center w-8 h-8 mr-2 text-xs border rounded-full shrink-0"
                        :class="currentStep === 7 ? 'border-blue-600' : 'border-gray-500'">
                        7
                    </span>
                    <span :class="currentStep === 7 ? 'font-bold' : ''">Kesimpulan</span>
                </li>
            </ol>

            <form id="evaluation-form" method="POST" action="{{ route('evaluations.store') }}"
                enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Informasi Pinjaman -->
                <div x-show="currentStep === 1" class="space-y-6">
                    <!-- Header -->
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4 mb-6">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                </path>
                            </svg>
                        </span>
                        Bagian 1 : Identitas & Pinjaman
                    </h2>

                    <!-- Customer Selection (Top Priority) -->
                    <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100 rounded-bl-full -mr-4 -mt-4 opacity-50">
                        </div>
                        <div class="relative z-10">
                            <input type="hidden" name="customer_id" :value="selectedCustomer?.id" required>
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        Data Nasabah Terpilih
                                    </h3>

                                    <div x-show="!selectedCustomer"
                                        class="mt-2 text-sm text-gray-500 italic flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Belum ada nasabah dipilih. Silakan pilih atau cari nasabah.
                                    </div>

                                    <div x-show="selectedCustomer" style="display: none;"
                                        class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm bg-white/60 p-4 rounded-lg border border-blue-100">
                                        <div>
                                            <span
                                                class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Nama
                                                Lengkap</span>
                                            <span class="text-base font-bold text-gray-900"
                                                x-text="selectedCustomer?.name"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">NIK
                                                / Identitas</span>
                                            <span class="font-medium text-gray-700 font-mono"
                                                x-text="selectedCustomer?.identity_number ?? '-'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Alamat</span>
                                            <span class="font-medium text-gray-700"
                                                x-text="selectedCustomer?.address ?? '-'"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="showModal = true"
                                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center shadow-lg transform hover:-translate-y-0.5 transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Pilih Nasabah
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- Section 1: Data Petugas & Kantor -->
                        <div
                            class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                            <h3
                                class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                                <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </span>
                                Data Petugas & Kantor
                            </h3>
                            @php
                                $loggedInAo = $aoUsers->firstWhere('code', optional(auth()->user())->code);
                            @endphp
                            <div class="space-y-4">
                                <div>
                                    <label for="office_branch"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Kantor Branch</label>
                                    @if($loggedInAo)
                                        <input type="hidden" name="office_branch" value="{{ $loggedInAo->office_branch }}">
                                    @endif
                                    <select id="office_branch" name="office_branch"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm {{ $loggedInAo ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ $loggedInAo ? 'disabled' : 'required' }}>
                                        <option value="" selected disabled>Pilih Kantor</option>
                                        @foreach($aoUsers->pluck('office_branch')->unique() as $branch)
                                            <option value="{{ $branch }}" {{ ($loggedInAo && $loggedInAo->office_branch == $branch) ? 'selected' : (old('office_branch') == $branch ? 'selected' : '') }}>
                                                {{ $branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="user_id" class="block mb-1.5 text-sm font-semibold text-gray-700">Account
                                        Officer (AO)</label>
                                    @if($loggedInAo)
                                        <input type="hidden" name="user_id" value="{{ $loggedInAo->id }}">
                                    @endif
                                    <select id="user_id" name="user_id"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm {{ $loggedInAo ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ $loggedInAo ? 'disabled' : 'required' }}>
                                        <option value="" selected disabled>Pilih Account Officer</option>
                                        @foreach($aoUsers as $ao)
                                            <option value="{{ $ao->id }}" {{ ($loggedInAo && $loggedInAo->id == $ao->id) ? 'selected' : (old('user_id') == $ao->id ? 'selected' : '') }}>
                                                {{ $ao->name }} - ({{ $ao->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Parameter Pengajuan -->
                        <div
                            class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                            <h3
                                class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                                <span class="bg-purple-100 text-purple-600 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                </span>
                                Parameter Pengajuan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="evaluation_date"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Tanggal Evaluasi</label>
                                    <input type="date" id="evaluation_date" name="evaluation_date"
                                        value="{{ old('evaluation_date', date('Y-m-d')) }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                </div>
                                <div>
                                    <label for="loan_scheme" class="block mb-1.5 text-sm font-semibold text-gray-700">Skema
                                        Kredit</label>
                                    <select id="loan_scheme" name="loan_scheme" x-model="loanScheme"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                        <option value="" selected disabled>Pilih Skema</option>
                                        <option value="Modal Kerja">Modal Kerja</option>
                                        <option value="Investasi">Investasi</option>
                                        <option value="Konsumsi">Konsumsi</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_type"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jenis Debitur</label>
                                    <select id="customer_type" name="customer_type"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                        <option value="Perorangan" selected>Perorangan</option>
                                        <option value="Badan Usaha">Badan Usaha</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_status"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Status Debitur</label>
                                    <select id="customer_status" name="customer_status" x-model="customerStatus"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                        <option value="Nasabah Baru" selected>Nasabah Baru</option>
                                        <option value="Nasabah Lama">Nasabah Lama</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Simulasi Pinjaman (Full Width) -->
                        <div
                            class="lg:col-span-2 bg-gradient-to-br from-white/80 to-blue-50/50 backdrop-blur-sm p-8 rounded-2xl border border-blue-100 shadow-md hover:shadow-lg transition-all duration-300 relative overflow-hidden group">

                            <!-- Decorative Circle -->
                            <div
                                class="absolute -top-10 -right-10 w-40 h-40 bg-blue-100 rounded-full opacity-50 blur-2xl group-hover:scale-110 transition-transform duration-500">
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3 border-b border-blue-100 pb-4 relative z-10">
                                <span class="bg-blue-100 text-blue-600 p-2 rounded-xl shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 4h6m-6 4h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10-5V5a2 2 0 00-2-2h-6a2 2 0 00-2 2v2m16 12h-6m6 4h-6M4 20h6">
                                        </path>
                                    </svg>
                                </span>
                                Simulasi & Detail Pinjaman
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                                <div class="col-span-1 md:col-span-3">
                                    <label for="loan_type" class="block mb-2 text-sm font-bold text-gray-700">Jenis
                                        Pinjaman</label>
                                    <select id="loan_type" name="loan_type" x-model="loanType"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-colors hover:border-blue-400"
                                        required>
                                        <option value="Pinjaman Angsuran" selected>Pinjaman Angsuran</option>
                                        <option value="Pinjaman Musiman">Pinjaman Musiman</option>
                                        <option value="Pinjaman Anuitas">Pinjaman Anuitas</option>
                                    </select>
                                </div>

                                <!-- Calculator Inputs -->
                                <div>
                                    <label for="loan_amount" class="block mb-2 text-sm font-bold text-gray-700">Jumlah
                                        Pengajuan (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="text" id="loan_amount_display" x-model="displayLoanAmount"
                                            @input="updateLoanAmount($event.target.value)"
                                            class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm font-bold tracking-wide transition-colors hover:border-blue-400"
                                            required placeholder="0">
                                        <input type="hidden" name="loan_amount" x-model="loanAmount">
                                    </div>
                                </div>

                                <div>
                                    <label for="loan_term_months" class="block mb-2 text-sm font-bold text-gray-700">Jangka
                                        Waktu
                                        (Bulan)</label>
                                    <input type="number" id="loan_term_months" name="loan_term_months"
                                        x-model.debounce.500ms="loanTerm"
                                        class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-semibold transition-colors hover:border-blue-400 text-center"
                                        required placeholder="Contoh: 12">

                                    <!-- Age Warning -->
                                    <div x-show="isAgeRisky" style="display: none;"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 transform scale-100"
                                        x-transition:leave-end="opacity-0 transform scale-95"
                                        class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800 flex items-start gap-2 animate-pulse">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-yellow-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        <div>
                                            <span class="font-bold block text-yellow-900">Perhatian: Usia Berisiko</span>
                                            Usia nasabah saat jatuh tempo > 60 tahun. Mohon pertimbangkan risiko.
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="loan_interest_rate" class="block mb-2 text-sm font-bold text-gray-700">Bunga
                                        per Tahun (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" id="loan_interest_rate" name="loan_interest_rate"
                                            x-model.debounce.500ms="interestRate"
                                            class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm font-semibold transition-colors hover:border-blue-400 text-center"
                                            required placeholder="Contoh: 12">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Calculated Result -->
                                <div
                                    class="col-span-1 md:col-span-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-6 transform transition-transform hover:scale-[1.01]">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Estimasi
                                                Angsuran per Bulan</p>
                                            <h4 class="text-3xl font-extrabold tracking-tight mt-1"
                                                x-text="formatCurrency(calculatedInstallment)">Rp
                                                0</h4>
                                        </div>
                                    </div>
                                    <!-- Hidden input to actually submit the value if needed, or user can edit -->
                                    <div class="w-full md:w-auto flex flex-col items-end">
                                        <label for="loan_installment" class="sr-only">Angsuran</label>
                                        <div class="relative w-full md:w-48">
                                            <input type="number" id="loan_installment" name="loan_installment"
                                                :value="calculatedInstallment"
                                                class="bg-black/20 border border-white/30 text-white text-lg rounded-xl focus:ring-white focus:border-white block w-full p-2.5 font-bold text-right placeholder-white/50 backdrop-blur-sm"
                                                readonly required>
                                        </div>
                                        <p
                                            class="text-xs text-blue-100/80 mt-2 font-medium bg-black/10 px-2 py-1 rounded inline-block">
                                            *Angsuran dihitung otomatis
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="col-span-1 md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-100 pt-6">
                                    <div>
                                        <label for="loan_purpose" class="block mb-2 text-sm font-bold text-gray-700">Tujuan
                                            Penggunaan</label>
                                        <input type="text" id="loan_purpose" name="loan_purpose"
                                            value="{{ old('loan_purpose') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-colors hover:border-blue-400"
                                            placeholder="Contoh: Menambah modal dagang sembako">
                                    </div>
                                    <div x-show="loanType === 'Pinjaman Musiman'">
                                        <label for="seasonal_loan_repayment_source"
                                            class="block mb-2 text-sm font-bold text-gray-700">Sumber Pelunasan
                                            (Musiman)</label>
                                        <input type="text" id="seasonal_loan_repayment_source"
                                            name="seasonal_loan_repayment_source"
                                            value="{{ old('seasonal_loan_repayment_source') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm transition-colors hover:border-blue-400"
                                            placeholder="Contoh: Hasil panen padi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Simulasi Pinjaman Sebelumnya (Full Width) -->
                        <div x-show="customerStatus === 'Nasabah Lama'"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="lg:col-span-2 bg-gradient-to-br from-white/80 to-amber-50/50 backdrop-blur-sm p-8 rounded-2xl border border-amber-200 shadow-md hover:shadow-lg transition-all duration-300 relative overflow-hidden group">

                            <!-- Decorative Circle -->
                            <div
                                class="absolute -top-10 -right-10 w-40 h-40 bg-amber-100 rounded-full opacity-50 blur-2xl group-hover:scale-110 transition-transform duration-500">
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3 border-b border-amber-200 pb-4 relative z-10">
                                <span class="bg-amber-100 text-amber-600 p-2 rounded-xl shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </span>
                                Detail Pinjaman Sebelumnya (Nasabah Lama)
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                                <div class="col-span-1 md:col-span-3">
                                    <label for="old_loan_type" class="block mb-2 text-sm font-bold text-gray-700">Jenis
                                        Pinjaman</label>
                                    <select id="old_loan_type" name="old_loan_type" x-model="oldLoanType"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3 shadow-sm transition-colors hover:border-amber-400"
                                        required>
                                        <option value="Pinjaman Angsuran" selected>Pinjaman Angsuran</option>
                                        <option value="Pinjaman Musiman">Pinjaman Musiman</option>
                                    </select>
                                </div>

                                <!-- Calculator Inputs -->
                                <div>
                                    <label for="old_loan_amount" class="block mb-2 text-sm font-bold text-gray-700">Jumlah
                                        Pengajuan (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="text" id="old_loan_amount_display" x-model="displayOldLoanAmount"
                                            @input="updateOldLoanAmount($event.target.value)"
                                            class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-2.5 shadow-sm font-bold tracking-wide transition-colors hover:border-amber-400"
                                            required placeholder="0">
                                        <input type="hidden" name="old_loan_amount" x-model="oldLoanAmount">
                                    </div>
                                </div>

                                <div>
                                    <label for="old_loan_term_months"
                                        class="block mb-2 text-sm font-bold text-gray-700">Jangka Waktu
                                        (Bulan)</label>
                                    <input type="number" id="old_loan_term_months" name="old_loan_term_months"
                                        x-model.debounce.500ms="oldLoanTerm"
                                        class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 shadow-sm font-semibold transition-colors hover:border-amber-400 text-center"
                                        required placeholder="Contoh: 12">
                                </div>

                                <div>
                                    <label for="old_loan_interest_rate"
                                        class="block mb-2 text-sm font-bold text-gray-700">Bunga per Tahun (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" id="old_loan_interest_rate"
                                            name="old_loan_interest_rate" x-model.debounce.500ms="oldInterestRate"
                                            class="bg-white border border-gray-300 text-gray-900 text-lg rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 shadow-sm font-semibold transition-colors hover:border-amber-400 text-center"
                                            required placeholder="Contoh: 12">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Calculated Result -->
                                <div
                                    class="col-span-1 md:col-span-3 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-6 transform transition-transform hover:scale-[1.01]">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0-2.08-.402-2.599-1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-amber-100 text-sm font-medium uppercase tracking-wider">Angsuran
                                                Sebelumnya</p>
                                            <h4 class="text-3xl font-extrabold tracking-tight mt-1"
                                                x-text="formatCurrency(calculatedOldInstallment)">Rp 0</h4>
                                        </div>
                                    </div>
                                    <!-- Hidden input to actually submit the value if needed, or user can edit -->
                                    <div class="w-full md:w-auto flex flex-col items-end">
                                        <label for="old_loan_installment" class="sr-only">Angsuran</label>
                                        <div class="relative w-full md:w-48">
                                            <input type="number" id="old_loan_installment" name="old_loan_installment"
                                                :value="calculatedOldInstallment"
                                                class="bg-black/20 border border-white/30 text-white text-lg rounded-xl focus:ring-white focus:border-white block w-full p-2.5 font-bold text-right placeholder-white/50 backdrop-blur-sm"
                                                readonly required>
                                        </div>
                                        <p
                                            class="text-xs text-amber-100/80 mt-2 font-medium bg-black/10 px-2 py-1 rounded inline-block">
                                            *Angsuran dihitung otomatis
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="col-span-1 md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-100 pt-6">
                                    <div>
                                        <label for="old_loan_purpose"
                                            class="block mb-2 text-sm font-bold text-gray-700">Tujuan
                                            Penggunaan</label>
                                        <input type="text" id="old_loan_purpose" name="old_loan_purpose"
                                            value="{{ old('old_loan_purpose') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3 shadow-sm transition-colors hover:border-amber-400"
                                            placeholder="Contoh: Menambah modal dagang sembako">
                                    </div>
                                    <div x-show="oldLoanType === 'Pinjaman Musiman'">
                                        <label for="old_seasonal_loan_repayment_source"
                                            class="block mb-2 text-sm font-bold text-gray-700">Sumber Pelunasan
                                            (Musiman)</label>
                                        <input type="text" id="old_seasonal_loan_repayment_source"
                                            name="old_seasonal_loan_repayment_source"
                                            value="{{ old('old_seasonal_loan_repayment_source') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3 shadow-sm transition-colors hover:border-amber-400"
                                            placeholder="Contoh: Hasil panen padi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Sektor Ekonomi & Golongan Debitur -->
                    <div
                        class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow h-full mb-6 relative z-10">
                        <h3
                            class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                            <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </span>
                            Sektor Ekonomi & Golongan Debitur
                        </h3>
                        <div class="space-y-4">
                            <!-- Economic Sector Searchable & Code -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-1 md:col-span-3 relative">
                                    <label for="economic_sector"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Sektor Ekonomi</label>
                                    <input type="text" x-model="searchEconomicSector" @input="selectedEconomicSector = null"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Cari Sektor Ekonomi..." autocomplete="off">

                                    <!-- Hidden input for name -->
                                    <input type="hidden" name="economic_sector"
                                        :value="selectedEconomicSector?.name || (selectedEconomicSector === null ? '' : searchEconomicSector)">

                                    <!-- Dropdown List -->
                                    <div x-show="searchEconomicSector && !selectedEconomicSector" style="display: none;"
                                        class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                                        <ul class="text-sm text-gray-700">
                                            <template x-for="sector in filteredEconomicSectors" :key="sector.id">
                                                <li @click="selectedEconomicSector = sector; searchEconomicSector = sector.name"
                                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0">
                                                    <div class="flex flex-col">
                                                        <span x-text="sector.name" class="font-medium"></span>
                                                        <span x-text="sector.code" class="text-xs text-gray-500"></span>
                                                    </div>
                                                </li>
                                            </template>
                                            <li x-show="filteredEconomicSectors.length === 0"
                                                class="px-4 py-2 text-gray-500 italic text-center p-4">
                                                Tidak ditemukan
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Kode Sektor</label>
                                    <input type="text" name="economic_sector_code" :value="selectedEconomicSector?.code"
                                        readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-600 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed font-mono">
                                </div>
                            </div>

                            <!-- Non Bank Third Party / Golongan Debitur -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-1 md:col-span-3">
                                    <label for="non_bank_third_party"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Golongan Debitur</label>
                                    <input type="hidden" name="non_bank_third_party"
                                        :value="nonBankThirdParties.find(p => String(p.code) == String(selectedNonBankThirdParty))?.name || ''">
                                    <select id="non_bank_third_party_select" x-model="selectedNonBankThirdParty"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        <option value="" selected disabled>Pilih Golongan Debitur</option>
                                        @foreach($nonBankThirdParties as $party)
                                            <option value="{{ $party->code }}">{{ $party->name }}</option>
                                        @endforeach
                                        <option value="-">Tidak Ada</option>
                                    </select>
                                </div>

                                <div class="col-span-1">
                                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Kode Golongan</label>
                                    <input type="text" name="non_bank_third_party_code"
                                        :value="selectedNonBankThirdParty === '-' ? '-' : selectedNonBankThirdParty"
                                        readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-600 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Data Grid (Sections 4, 5, 6) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Section 4: Data Pekerjaan & Usaha -->
                        <div
                            class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow h-full">
                            <h3
                                class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                                <span class="bg-orange-100 text-orange-600 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </span>
                                Data Pekerjaan & Usaha
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="customer_entrepreneurship_status"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Status
                                        Usaha</label>
                                    <select id="customer_entrepreneurship_status" name="customer_entrepreneurship_status"
                                        x-model="entrepreneurshipStatus"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                        <option value="" selected disabled>Pilih Status Usaha</option>
                                        <option value="Wirausaha">Wirausaha</option>
                                        <option value="Bukan Wirausaha">Bukan Wirausaha</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_employment_status"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Status
                                        Kepegawaian</label>
                                    <select id="customer_employment_status" name="customer_employment_status"
                                        x-model="employmentStatus"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        required>
                                        <option value="" selected disabled>Pilih Status Kepegawaian</option>
                                        <option value="PNS">PNS</option>
                                        <option value="TNI/Polri">TNI/Polri</option>
                                        <option value="BUMN">BUMN</option>
                                        <option value="Swasta">Swasta</option>
                                        <option value="Bukan Karyawan">Bukan Karyawan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_profile"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Profil
                                        Singkat</label>
                                    <textarea id="customer_profile" name="customer_profile" rows="3"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: Pedagang sate ayam sejak 2010 dengan lokasi menetap.">{{ old('customer_profile') }}</textarea>
                                </div>
                                <div>
                                    <label for="customer_dependents"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jumlah Tanggungan</label>
                                    <input type="number" id="customer_dependents" name="customer_dependents"
                                        value="{{ old('customer_dependents') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: 2">
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Legalitas & Dokumen -->
                        <div x-show="entrepreneurshipStatus === 'Wirausaha'"
                            class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow h-full">
                            <h3
                                class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                                <span class="bg-teal-100 text-teal-600 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </span>
                                Legalitas & Detail Usaha
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_entreprenuership_legality"
                                        class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase">Legalitas</label>
                                    <select id="customer_entreprenuership_legality"
                                        name="customer_entreprenuership_legality" x-model="legality"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        :required="entrepreneurshipStatus === 'Wirausaha'">
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Berbadan Usaha">Berbadan Usaha</option>
                                        <option value="Tidak Berbadan Usaha">Tidak Berbadan Usaha</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_ownership"
                                        class="block mb-1.5 text-xs font-semibold text-gray-500 uppercase">Kepemilikan</label>
                                    <select id="customer_entreprenuership_ownership"
                                        name="customer_entreprenuership_ownership"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        :required="entrepreneurshipStatus === 'Wirausaha'">
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Milik Sendiri">Milik Sendiri</option>
                                        <option value="Milik Keluarga">Milik Keluarga</option>
                                        <option value="Usaha Patungan">Usaha Patungan</option>
                                        <option value="Usaha Warisan">Usaha Warisan</option>
                                        <option value="Kepemilikan Saham">Kepemilikan Saham</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_entreprenuership_name"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Nama Usaha</label>
                                    <input type="text" id="customer_entreprenuership_name"
                                        name="customer_entreprenuership_name"
                                        value="{{ old('customer_entreprenuership_name') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_type"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jenis Usaha</label>
                                    <input type="text" id="customer_entreprenuership_type"
                                        name="customer_entreprenuership_type"
                                        value="{{ old('customer_entreprenuership_type') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Perdagangan/Jasa">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_products"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Produk yang Dijual</label>
                                    <input type="text" id="customer_entreprenuership_products"
                                        name="customer_entreprenuership_products"
                                        value="{{ old('customer_entreprenuership_products') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: Baju, Celana">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_place_status"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Status Tempat</label>
                                    <input type="text" id="customer_entreprenuership_place_status"
                                        name="customer_entreprenuership_place_status"
                                        value="{{ old('customer_entreprenuership_place_status') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: Sewa">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_phone"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Nomor Telepon</label>
                                    <input type="text" id="customer_entreprenuership_phone"
                                        name="customer_entreprenuership_phone"
                                        value="{{ old('customer_entreprenuership_phone') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: 08123456789">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_employee_count"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jumlah Karyawan</label>
                                    <input type="number" id="customer_entreprenuership_employee_count"
                                        name="customer_entreprenuership_employee_count"
                                        value="{{ old('customer_entreprenuership_employee_count') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh: 5">
                                </div>
                                <div>
                                    <label for="customer_entreprenuership_year"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Tahun
                                        Berdiri</label>
                                    <input type="text" id="customer_entreprenuership_year"
                                        name="customer_entreprenuership_year"
                                        value="{{ old('customer_entreprenuership_year') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_entreprenuership_description"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Deskripsi Usaha</label>
                                    <textarea id="customer_entreprenuership_description"
                                        name="customer_entreprenuership_description" rows="3"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Penjelasan detail mengenai usaha">{{ old('customer_entreprenuership_description') }}</textarea>
                                </div>

                                <!-- Legal IDs -->
                                <div x-show="legality === 'Berbadan Usaha'"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="col-span-1 md:col-span-2 mt-2 border-t pt-2 border-gray-100">
                                    <p class="text-xs text-gray-400 font-semibold uppercase mb-2">Nomor Dokumen
                                        (Jika Ada)
                                    </p>
                                    <div class="space-y-3">
                                        <input type="text" id="customer_entreprenuership_tax_id"
                                            name="customer_entreprenuership_tax_id"
                                            value="{{ old('customer_entreprenuership_tax_id') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5"
                                            placeholder="NPWP">
                                        <input type="text" id="customer_entreprenuership_legality_id"
                                            name="customer_entreprenuership_legality_id"
                                            value="{{ old('customer_entreprenuership_legality_id') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5"
                                            placeholder="Surat Izin Usaha">
                                        <input type="text" id="customer_entreprenuership_legality_register_id"
                                            name="customer_entreprenuership_legality_register_id"
                                            value="{{ old('customer_entreprenuership_legality_register_id') }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5"
                                            placeholder="Tanda Daftar Usaha">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 6: Detail & Instansi -->
                        <div x-show="employmentStatus !== 'Bukan Karyawan'"
                            class="bg-white/50 backdrop-blur-sm p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow h-full">
                            <h3
                                class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2 border-gray-100">
                                <span class="bg-teal-100 text-teal-600 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </span>
                                Detail Instansi / Perusahaan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_company_name"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Nama
                                        Instansi/Perusahaan</label>
                                    <input type="text" id="customer_company_name" name="customer_company_name"
                                        value="{{ old('customer_company_name') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : PT. BPR Puriseger Sentosa">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_company_address"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Alamat
                                        Instansi/Perusahaan</label>
                                    <input type="text" id="customer_company_address" name="customer_company_address"
                                        value="{{ old('customer_company_address') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : Jln. Jayanegara No. 183 Puri, Mojokerto">
                                </div>
                                <div>
                                    <label for="customer_company_position"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jabatan</label>
                                    <input type="text" id="customer_company_position" name="customer_company_position"
                                        value="{{ old('customer_company_position') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : Manajer Operasional">
                                </div>
                                <div>
                                    <label for="customer_company_years"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Lama Bekerja
                                    </label>
                                    <input type="text" id="customer_company_years" name="customer_company_years"
                                        value="{{ old('customer_company_years') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : 12 Tahun">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_company_phone"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Nomor Kontak
                                        Insansi/Perusahaan</label>
                                    <input type="text" id="customer_company_phone" name="customer_company_phone"
                                        value="{{ old('customer_company_phone') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : (0321) 392938">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="customer_employee_status"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Status Kepegawaian</label>
                                    <select id="customer_employee_status" name="customer_employee_status"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        <option value="">Pilih Status Kepegawaian</option>
                                        <option value="Pegawai Tetap" {{ old('customer_employee_status') == 'Pegawai Tetap' ? 'selected' : '' }}>Pegawai Tetap</option>
                                        <option value="Pegawai Kontrak" {{ old('customer_employee_status') == 'Pegawai Kontrak' ? 'selected' : '' }}>Pegawai Kontrak</option>
                                        <option value="Pegawai Honorer" {{ old('customer_employee_status') == 'Pegawai Honorer' ? 'selected' : '' }}>Pegawai Honorer</option>
                                        <option value="Pegawai Paruh Waktu" {{ old('customer_employee_status') == 'Pegawai Paruh Waktu' ? 'selected' : '' }}>Pegawai Paruh Waktu</option>
                                        <option value="Pegawai Musiman" {{ old('customer_employee_status') == 'Pegawai Musiman' ? 'selected' : '' }}>Pegawai Musiman</option>
                                        <option value="Pegawai Lepas" {{ old('customer_employee_status') == 'Pegawai Lepas' ? 'selected' : '' }}>Pegawai Lepas</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_company_sector"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Sektor Usaha</label>
                                    <input type="text" id="customer_company_sector" name="customer_company_sector"
                                        value="{{ old('customer_company_sector') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : Manufaktur">
                                </div>
                                <div>
                                    <label for="customer_company_employee_count"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Jumlah Karyawan</label>
                                    <input type="text" id="customer_company_employee_count"
                                        name="customer_company_employee_count"
                                        value="{{ old('customer_company_employee_count') }}"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                        placeholder="Contoh : 50">
                                </div>
                                <div>
                                    <label for="customer_company_salary_frequency"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Pembayaran Gaji</label>
                                    <select id="customer_company_salary_frequency" name="customer_company_salary_frequency"
                                        x-model="salaryFrequency"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        <option value="">Pilih Pembayaran Gaji</option>
                                        <option value="Harian" {{ old('customer_company_salary_frequency') == 'Harian' ? 'selected' : '' }}>Harian</option>
                                        <option value="Mingguan" {{ old('customer_company_salary_frequency') == 'Mingguan' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="Bulanan" {{ old('customer_company_salary_frequency') == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    </select>
                                </div>
                                <div x-show="salaryFrequency !== 'Harian'">
                                    <label for="customer_company_payday"
                                        class="block mb-1.5 text-sm font-semibold text-gray-700">Tanggal Gajian</label>
                                    <div class="relative max-w-sm">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input type="date" id="customer_company_payday" name="customer_company_payday"
                                            value="{{ old('customer_company_payday') }}"
                                            class="bg-white datepicker border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 shadow-sm"
                                            placeholder="Pilih Tanggal Gajian">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Photo Uploads Section (Wirausaha Only) -->
                    <div x-show="entrepreneurshipStatus === 'Wirausaha'"
                        class="bg-white/50 backdrop-blur-sm p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all mt-8">
                        <h3
                            class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3 border-b pb-4 border-gray-100">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </span>
                            Foto Legalitas & Detail Usaha
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Business Legality -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Foto Usaha 1</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="business_legality_photo"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4"
                                            id="legality-placeholder">
                                            <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <p class="text-xs text-gray-500 font-medium">Klik untuk upload foto</p>
                                        </div>
                                        <img id="legality-preview" class="hidden absolute h-full w-full object-cover"
                                            @if(old('business_legality_photo_data'))
                                            src="{{ old('business_legality_photo_data') }}" @endif />
                                        <input id="business_legality_photo" name="business_legality_photo" type="file"
                                            class="hidden photo-input" data-preview="legality-preview"
                                            data-placeholder="legality-placeholder"
                                            data-base64="business_legality_photo_data" accept="image/*" />
                                        <input type="hidden" name="business_legality_photo_data"
                                            id="business_legality_photo_data"
                                            value="{{ old('business_legality_photo_data') }}" />
                                    </label>
                                </div>
                            </div>

                            <!-- Business Detail 1 -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Foto Usaha 2</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="business_detail_1_photo"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4"
                                            id="detail1-placeholder">
                                            <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <p class="text-xs text-gray-500 font-medium">Klik untuk upload foto</p>
                                        </div>
                                        <img id="detail1-preview" class="hidden absolute h-full w-full object-cover"
                                            @if(old('business_detail_1_photo_data'))
                                            src="{{ old('business_detail_1_photo_data') }}" @endif />
                                        <input id="business_detail_1_photo" name="business_detail_1_photo" type="file"
                                            class="hidden photo-input" data-preview="detail1-preview"
                                            data-placeholder="detail1-placeholder"
                                            data-base64="business_detail_1_photo_data" accept="image/*" />
                                        <input type="hidden" name="business_detail_1_photo_data"
                                            id="business_detail_1_photo_data"
                                            value="{{ old('business_detail_1_photo_data') }}" />
                                    </label>
                                </div>
                            </div>

                            <!-- Business Detail 2 -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Foto Usaha 3</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="business_detail_2_photo"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4"
                                            id="detail2-placeholder">
                                            <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <p class="text-xs text-gray-500 font-medium">Klik untuk upload foto</p>
                                        </div>
                                        <img id="detail2-preview" class="hidden absolute h-full w-full object-cover"
                                            @if(old('business_detail_2_photo_data'))
                                            src="{{ old('business_detail_2_photo_data') }}" @endif />
                                        <input id="business_detail_2_photo" name="business_detail_2_photo" type="file"
                                            class="hidden photo-input" data-preview="detail2-preview"
                                            data-placeholder="detail2-placeholder"
                                            data-base64="business_detail_2_photo_data" accept="image/*" />
                                        <input type="hidden" name="business_detail_2_photo_data"
                                            id="business_detail_2_photo_data"
                                            value="{{ old('business_detail_2_photo_data') }}" />
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Section (Wirausaha Only) -->
                    <div x-show="entrepreneurshipStatus === 'Wirausaha'" class="mt-8 space-y-6">
                        <div
                            class="bg-white/50 backdrop-blur-sm p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
                            <h3
                                class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3 border-b pb-4 border-gray-100">
                                <span class="bg-orange-100 text-orange-600 p-2 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>
                                Lokasi Tempat Usaha
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm font-bold text-gray-700">Tentukan Lokasi di Peta</span>
                                        <button type="button" id="get-location-btn"
                                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg active:scale-95">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Baca Lokasi Saat Ini
                                        </button>
                                    </div>
                                    <div id="map"></div>

                                    <!-- Manual Capture Button & Preview -->
                                    <div class="flex flex-col gap-2 mt-2">
                                        <button type="button" onclick="captureMapManual()"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-all shadow-md active:scale-95">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Ambil Gambar Peta
                                        </button>
                                        <div id="map-preview-container"
                                            class="hidden border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 text-center">
                                            <span class="text-xs text-gray-500 block mb-1">Preview Gambar Peta:</span>
                                            <img id="map-preview"
                                                class="w-full h-32 object-cover rounded-md border border-gray-200" />
                                            <span class="text-[10px] text-green-600 font-bold block mt-1">✓ Gambar Siap
                                                Disimpan</span>
                                        </div>
                                    </div>

                                    <p class="text-[10px] text-gray-500 italic mt-2">
                                        * Klik pada peta untuk lokasi tepat, lalu klik "Ambil Gambar Peta" sebelum
                                        menyimpan.
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block mb-2 text-xs font-bold text-gray-700 uppercase">Latitude</label>
                                            <input type="text" id="business_latitude" name="business_latitude" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 font-mono cursor-not-allowed"
                                                placeholder="Contoh: -7.4704747" value="{{ old('business_latitude') }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-2 text-xs font-bold text-gray-700 uppercase">Longitude</label>
                                            <input type="text" id="business_longitude" name="business_longitude" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 font-mono cursor-not-allowed"
                                                placeholder="Contoh: 112.4401329" value="{{ old('business_longitude') }}">
                                        </div>
                                    </div>

                                    <!-- Distance Alert -->
                                    <div id="distance-alert" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert">
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                            </svg>
                                            <span class="sr-only">Info</span>
                                            <div>
                                                <span class="font-medium">Jarak dari
                                                    {{ auth()->user()->office_branch ?? 'Kantor Pusat' }}:</span> <span
                                                    id="distance-value">Menghitung...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4 pt-2">
                                        <div>
                                            <label
                                                class="block mb-2 text-xs font-bold text-gray-700 uppercase">Provinsi</label>
                                            <input type="text" id="business_province" name="business_province" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                                value="{{ old('business_province') }}">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-700 uppercase">Kabupaten /
                                                Kota</label>
                                            <input type="text" id="business_regency" name="business_regency" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                                value="{{ old('business_regency') }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-2 text-xs font-bold text-gray-700 uppercase">Kecamatan</label>
                                            <input type="text" id="business_district" name="business_district" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                                value="{{ old('business_district') }}">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-700 uppercase">Kelurahan /
                                                Desa</label>
                                            <input type="text" id="business_village" name="business_village" readonly
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                                value="{{ old('business_village') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Map Image Input -->
                    <input type="hidden" id="location_image" name="location_image" value="{{ old('location_image') }}">

                    <div class="flex justify-end pt-6">
                        <button type="button" @click="currentStep = 2"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 mb-2 focus:outline-none transition-all shadow-md flex items-center">
                            Lanjut ke Data SLIK
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Data SLIK / IDeb -->
                <div x-show="currentStep === 2" style="display: none;" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4">
                        <span class="bg-indigo-100 text-indigo-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </span>
                        Bagian 2 : Riwayat Kredit Bank Lain (SLIK/IDeb)
                    </h2>

                    <div class="space-y-4">
                        <template x-for="(loan, index) in loans" :key="index">
                            <div
                                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow p-4 sm:p-6 relative">
                                <button type="button" @click="loans = loans.filter((_, i) => i !== index)"
                                    class="absolute top-4 right-4 text-gray-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                                    <!-- Bank Name -->
                                    <div class="sm:col-span-2 lg:col-span-1">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama
                                            Bank</label>
                                        <input type="text" x-model="loan.bank_name"
                                            :name="'external_loans['+index+'][bank_name]'"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Nama Bank">
                                    </div>

                                    <!-- Status -->
                                    <div class="lg:col-span-1">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kolektibilitas</label>
                                        <select x-model="loan.collectibility"
                                            :name="'external_loans['+index+'][collectibility]'"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="Lancar">Lancar</option>
                                            <option value="DPK">DPK</option>
                                            <option value="Kurang Lancar">Kurang Lancar</option>
                                            <option value="Diragukan">Diragukan</option>
                                            <option value="Macet">Macet</option>
                                        </select>
                                    </div>

                                    <!-- Dates -->
                                    <div class="lg:col-span-1">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tgl
                                            Realisasi</label>
                                        <input type="date" x-model="loan.realization_date"
                                            @change="calculateLoanTenor(index)"
                                            :name="'external_loans['+index+'][realization_date]'"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div class="lg:col-span-1">
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jatuh
                                            Tempo</label>
                                        <input type="date" x-model="loan.maturity_date" @change="calculateLoanTenor(index)"
                                            :name="'external_loans['+index+'][maturity_date]'"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div class="col-span-1 sm:col-span-2 lg:col-span-4 border-t border-gray-100 my-1"></div>

                                    <!-- Financials -> Row 2 -->
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Plafon
                                            Awal</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="text" x-model="loan.original_amount"
                                                @input="formatLoanField(loan, 'original_amount'); calculateInstallment(index)"
                                                :name="'external_loans['+index+'][original_amount]'"
                                                class="block w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-right"
                                                placeholder="0">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Baki
                                            Debet</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="text" x-model="loan.outstanding_balance"
                                                @input="formatLoanField(loan, 'outstanding_balance'); calculateInstallment(index)"
                                                :name="'external_loans['+index+'][outstanding_balance]'"
                                                class="block w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-right"
                                                placeholder="0">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tenor</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <input type="number" x-model="loan.term_months"
                                                    @input="calculateInstallment(index)"
                                                    :name="'external_loans['+index+'][term_months]'"
                                                    class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center"
                                                    placeholder="0">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-gray-500 sm:text-xs">Bln</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Bunga</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" x-model="loan.interest_rate"
                                                    @input="calculateInstallment(index)"
                                                    :name="'external_loans['+index+'][interest_rate]'"
                                                    class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center"
                                                    placeholder="0">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-gray-500 sm:text-xs">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Calculation -->
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Metode
                                            Bunga</label>
                                        <select x-model="loan.interest_method" @change="calculateInstallment(index)"
                                            :name="'external_loans['+index+'][interest_method]'"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="Flat">Flat</option>
                                            <option value="Anuitas">Anuitas</option>
                                            <option value="Efektif">Efektif</option>
                                            <option value="Musiman">Musiman</option>
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2 lg:col-span-4 border-t border-gray-100 my-1"></div>

                                    <!-- Result -->
                                    <div
                                        class="sm:col-span-2 lg:col-span-4 bg-blue-50/50 rounded-lg p-3 flex justify-between items-center">
                                        <span class="text-sm font-bold text-blue-900">Estimasi Angsuran / Bulan</span>
                                        <div class="text-right">
                                            <span class="text-xs text-blue-600 block">Rp</span>
                                            <input type="text" x-model="loan.installment_amount" readonly
                                                :name="'external_loans['+index+'][installment_amount]'"
                                                class="block w-40 rounded-md border-0 bg-transparent p-0 text-right text-lg font-bold text-blue-700 focus:ring-0"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button"
                            @click="loans.push({ bank_name: '', realization_date: '', maturity_date: '', outstanding_balance: '', collectibility: 'Lancar', original_amount: '', term_months: '', interest_rate: '', interest_method: 'Flat', installment_amount: '' })"
                            class="mt-4 flex items-center justify-center w-full py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50/30 transition-all font-medium group">
                            <span
                                class="bg-gray-100 group-hover:bg-blue-100 text-gray-500 group-hover:text-blue-600 rounded-full p-1 mr-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </span>
                            Tambah Data Bank Lain
                        </button>

                        <div
                            class="bg-gray-50 rounded-lg p-4 flex justify-between items-center border border-gray-200 mt-4">
                            <span class="font-medium text-gray-700">Total Angsuran Bank Lain</span>
                            <span class="font-bold text-lg text-gray-900"
                                x-text="formatNumber(totalExternalInstallment)"></span>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="currentStep = 1"
                            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition-colors">Kembali</button>
                        <button type="button" @click="currentStep = 3"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none transition-colors">Lanjut
                            ke Analisa Keuangan</button>
                    </div>
                </div>

                <!-- Step 3: Analisa Keuangan / Arus Kas -->
                <div x-show="currentStep === 3" style="display: none;" class="space-y-6">

                    <!-- Financial Dashboard -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- DSR Card -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">DSR (Debt Service Ratio)</p>
                                    <p class="text-lg font-bold"
                                        :class="dsrRatio > 50 ? 'text-red-600' : (dsrRatio > 35 ? 'text-yellow-600' : 'text-green-600')">
                                        <span x-text="dsrRatio"></span>%
                                    </p>
                                </div>
                                <div class="p-2 rounded-lg"
                                    :class="dsrRatio > 50 ? 'bg-red-100 text-red-600' : (dsrRatio > 35 ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                    :class="dsrRatio > 50 ? 'bg-red-500' : (dsrRatio > 35 ? 'bg-yellow-500' : 'bg-green-500')"
                                    :style="'width: ' + Math.min(dsrRatio, 100) + '%'"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Target: < 35% (Aman)</p>
                                    <p class="text-[9px] text-gray-500 mt-1 italic">Formula: (Angsuran + Angsuran Lain) /
                                        Net Cash Flow</p>
                        </div>

                        <!-- DAR Card -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">DAR (Debt-to-Asset Ratio)</p>
                                    <p class="text-lg font-bold"
                                        :class="darRatio < 50 ? 'text-green-600' : 'text-yellow-600'">
                                        <span x-text="darRatio"></span>%
                                    </p>
                                </div>
                                <div class="p-2 rounded-lg"
                                    :class="darRatio < 50 ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                    :class="darRatio < 50 ? 'bg-green-500' : 'bg-yellow-500'"
                                    :style="'width: ' + Math.min(darRatio, 100) + '%'"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Target: < 50%</p>
                                    <p class="text-[9px] text-gray-500 mt-1 italic">Formula: Total Pinjaman Eksternal /
                                        Total Aset</p>
                        </div>

                        <!-- DER Card -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">DER (Debt-to-Equity Ratio)</p>
                                    <p class="text-lg font-bold"
                                        :class="derRatio < 100 ? 'text-green-600' : (derRatio < 200 ? 'text-yellow-600' : 'text-red-600')">
                                        <span x-text="derRatio"></span>%
                                    </p>
                                </div>
                                <div class="p-2 rounded-lg"
                                    :class="derRatio < 100 ? 'bg-green-100 text-green-600' : (derRatio < 200 ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                    :class="derRatio < 100 ? 'bg-green-500' : (derRatio < 200 ? 'bg-yellow-500' : 'bg-red-500')"
                                    :style="'width: ' + Math.min(derRatio, 100) + '%'"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Target: < 100%</p>
                                    <p class="text-[9px] text-gray-500 mt-1 italic">Formula: Total Pinjaman Eksternal /
                                        Modal Usaha</p>
                        </div>

                        <!-- DTI Card -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">DTI (Debt-to-Income Ratio)</p>
                                    <p class="text-lg font-bold"
                                        :class="dtiRatio > 50 ? 'text-red-600' : (dtiRatio > 40 ? 'text-yellow-600' : 'text-green-600')">
                                        <span x-text="dtiRatio"></span>%
                                    </p>
                                </div>
                                <div class="p-2 rounded-lg"
                                    :class="dtiRatio > 50 ? 'bg-red-100 text-red-600' : (dtiRatio > 40 ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                    :class="dtiRatio > 50 ? 'bg-red-500' : (dtiRatio > 40 ? 'bg-yellow-500' : 'bg-green-500')"
                                    :style="'width: ' + Math.min(dtiRatio, 100) + '%'"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Target: < 40% (Aman)</p>
                                    <p class="text-[9px] text-gray-500 mt-1 italic">Formula: Total Angsuran Eksternal /
                                        Total Pemasukan</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-b pb-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </span>
                            Bagian 3 : Analisa Arus Kas
                        </h2>
                        <div
                            class="text-sm font-medium text-blue-600 bg-blue-50 px-4 py-2 rounded-full border border-blue-100 italic">
                            * Seluruh nilai dalam mata uang Rupiah (IDR)
                        </div>
                    </div>


                    <div
                        class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm ring-1 ring-gray-900/5 bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 sm:pl-6">
                                        Keterangan Arus Kas
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50/50">
                                        Sebelum Pencairan
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50/50">
                                        Setelah Pencairan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- Group 1: Opening Balance -->
                                <tr class="bg-gray-50/80">
                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold text-gray-800 sm:pl-6">
                                        SALDO & KAS USAHA
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-900 bg-blue-50/20">
                                        <span x-text="formatNumber(openingBalance)"></span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-500 bg-emerald-50/10">
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Kas Usaha
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="openingCash"
                                            @input="updateCFValue('openingCash', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="kas_usaha" x-model="openingCash">
                                    </td>
                                    <td class="bg-emerald-50/5"></td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Piutang Usaha
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="openingSavings"
                                            @input="updateCFValue('openingSavings', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="piutang_usaha" x-model="openingSavings">
                                    </td>
                                    <td class="bg-emerald-50/5"></td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Persediaan
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="openingGiro"
                                            @input="updateCFValue('openingGiro', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="persediaan" x-model="openingGiro">
                                    </td>
                                    <td class="bg-emerald-50/5"></td>
                                </tr>


                                <!-- Section a: Saldo Awal Operasional -->
                                <tr class="bg-gray-50 border-t border-gray-200 text-gray-700">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold uppercase tracking-wide sm:pl-6">
                                        a. Saldo Awal Operasional
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right bg-blue-50/10">
                                        <div class="relative rounded-lg shadow-sm">
                                            <input type="text" :value="formatNumber(opOpeningTotalBefore)" readonly
                                                class="block w-full rounded-lg border-gray-200 py-2 pl-3 pr-3 text-gray-700 bg-gray-100 focus:ring-0 text-right sm:text-sm font-bold cursor-default shadow-sm">
                                            <input type="hidden" name="op_opening_balance_before"
                                                :value="opOpeningTotalBefore">
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right bg-emerald-50/10">
                                        <div class="relative rounded-lg shadow-sm">
                                            <input type="text" :value="formatNumber(opOpeningTotalAfter)" readonly
                                                class="block w-full rounded-lg border-emerald-200 py-2 pl-3 pr-3 text-emerald-700 bg-emerald-50 focus:ring-0 text-right sm:text-sm font-bold cursor-default shadow-sm">
                                            <input type="hidden" name="op_opening_balance_after"
                                                :value="opOpeningTotalAfter">
                                        </div>
                                    </td>
                                </tr>

                                <!-- Section b: Arus Kas Masuk -->
                                <tr class="bg-gray-100 border-t border-gray-200">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold text-gray-900 uppercase tracking-wide sm:pl-6">
                                        b. Arus Kas Masuk (Income)
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-900 bg-blue-50/20"
                                        x-text="formatNumber(cashInTotalBefore)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-emerald-700 bg-emerald-50/20"
                                        x-text="formatNumber(cashInTotalAfter)"></td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Gaji
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="salaryBefore"
                                            @input="updateCFValue('salaryBefore', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="cash_in_salary_before" x-model="salaryBefore">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="salaryAfter"
                                            @input="updateCFValue('salaryAfter', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="cash_in_salary_after" x-model="salaryAfter">
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Pendapatan Usaha
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="businessBefore"
                                            @input="updateCFValue('businessBefore', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="cash_in_business_before" x-model="businessBefore">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="businessAfter"
                                            @input="updateCFValue('businessAfter', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="cash_in_business_after" x-model="businessAfter">
                                    </td>
                                </tr>
                                <template x-for="(income, index) in otherIncomes" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td
                                            class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors flex items-center justify-between">
                                            <div class="flex items-center gap-2 w-full">
                                                <input type="text" x-model="income.name"
                                                    :name="'other_incomes[' + index + '][name]'"
                                                    class="block w-full rounded-lg border-gray-300 py-1.5 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white"
                                                    placeholder="Nama Pendapatan">
                                                <button type="button" @click="removeOtherIncome(index)"
                                                    class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded-md transition-colors"
                                                    title="Hapus Pendapatan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="w-4 h-4">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                        <line x1="10" x2="10" y1="11" y2="17" />
                                                        <line x1="14" x2="14" y1="11" y2="17" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" :value="income.before"
                                                @input="updateOtherIncomeCFValue(index, 'before', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" :name="'other_incomes[' + index + '][before]'"
                                                :value="income.before">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" :value="income.after"
                                                @input="updateOtherIncomeCFValue(index, 'after', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" :name="'other_incomes[' + index + '][after]'"
                                                :value="income.after">
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50/50">
                                    <td colspan="3" class="py-2 pl-8 pr-3 sm:pl-10">
                                        <button type="button" @click="addOtherIncome()"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                <line x1="12" x2="12" y1="5" y2="19" />
                                                <line x1="5" x2="19" y1="12" y2="12" />
                                            </svg>
                                            Tambah Pendapatan Lainnya
                                        </button>
                                        <!-- Keep the original fields as hidden to save the total in existing columns easily without changing controller too much, though controller will also save the JSON -->
                                        <input type="hidden" name="cash_in_other_before" :value="otherInBefore">
                                        <input type="hidden" name="cash_in_other_after" :value="otherInAfter">
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Kas Masuk Lainnya
                                    </td>
                                    <td class="bg-blue-50/5"></td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="capitalInjection"
                                            @input="updateCFValue('capitalInjection', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="capital_injection_amount" x-model="capitalInjection">
                                    </td>
                                </tr>

                                <!-- Section c: Arus Kas Keluar -->
                                <tr class="bg-gray-100 border-t border-gray-200">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold text-gray-900 uppercase tracking-wide sm:pl-6">
                                        c. Arus Kas Keluar
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-900 bg-blue-50/20"
                                        x-text="formatNumber(cashOutTotalBefore)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-emerald-700 bg-emerald-50/20"
                                        x-text="formatNumber(cashOutTotalAfter)"></td>
                                </tr>



                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-3 pl-6 pr-3 text-sm font-bold text-gray-800 sm:pl-8">
                                        Angsuran Bank Lain
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2.5 text-right text-sm font-bold text-gray-900"
                                        x-text="formatNumber(bankInstallmentsBefore)"></td>
                                    <td class="whitespace-nowrap px-3 py-2.5 text-right text-sm font-bold text-gray-900"
                                        x-text="formatNumber(bankInstallmentsAfter)"></td>
                                </tr>

                                <!-- Household Expenses Sub-section -->
                                <tr class="bg-gray-50/50 border-t border-gray-100">
                                    <td class="whitespace-nowrap py-3 pl-6 pr-3 text-sm font-bold text-gray-800 sm:pl-8">
                                        Beban Rumah Tangga
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-700 bg-blue-50/5">
                                        <span x-text="formatNumber(hhTotalBefore)"></span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-700 bg-emerald-50/5">
                                        <span x-text="formatNumber(hhTotalAfter)"></span>
                                    </td>
                                </tr>

                                <!-- HH Rows -->
                                @php
                                    $hhFields = [
                                        ['label' => 'Biaya Hidup', 'model' => 'hhLiving', 'name' => 'hh_living'],
                                        ['label' => 'Listrik & Air', 'model' => 'hhUtilities', 'name' => 'hh_utilities'],
                                        ['label' => 'Pendidikan', 'model' => 'hhEducation', 'name' => 'hh_education'],
                                        ['label' => 'Telekomunikasi', 'model' => 'hhTelecom', 'name' => 'hh_telecom'],
                                        ['label' => 'Transportasi', 'model' => 'hhTransport', 'name' => 'hh_transport'],
                                        ['label' => 'Hiburan', 'model' => 'hhEntertainment', 'name' => 'hh_entertainment'],
                                        ['label' => 'Sewa', 'model' => 'hhRent', 'name' => 'hh_rent'],
                                        ['label' => 'Lainnya', 'model' => 'hhOther', 'name' => 'hh_other'],
                                    ];
                                @endphp
                                @foreach($hhFields as $field)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td
                                            class="whitespace-nowrap py-2.5 pl-10 pr-3 text-sm text-gray-500 sm:pl-12 group-hover:text-blue-600 transition-colors">
                                            {{ $field['label'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="{{ $field['model'] }}Before"
                                                @input="updateCFValue('{{ $field['model'] }}Before', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right text-xs bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" name="{{ $field['name'] }}_before"
                                                x-model="{{ $field['model'] }}Before">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="{{ $field['model'] }}After"
                                                @input="updateCFValue('{{ $field['model'] }}After', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right text-xs bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" name="{{ $field['name'] }}_after"
                                                x-model="{{ $field['model'] }}After">
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Business Expenses Sub-section -->
                                <tr class="bg-gray-50/50 border-t border-gray-100">
                                    <td class="whitespace-nowrap py-3 pl-6 pr-3 text-sm font-bold text-gray-800 sm:pl-8">
                                        Beban Usaha
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-700 bg-blue-50/5">
                                        <span x-text="formatNumber(bizTotalBefore)"></span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-700 bg-emerald-50/5">
                                        <span x-text="formatNumber(bizTotalAfter)"></span>
                                    </td>
                                </tr>

                                @php
                                    $bizFields = [
                                        ['label' => 'HPP / Bahan Baku', 'model' => 'bizHPP', 'name' => 'biz_hpp'],
                                        ['label' => 'Tenaga Kerja', 'model' => 'bizLabor', 'name' => 'biz_labor'],
                                        ['label' => 'Telekomunikasi', 'model' => 'bizTelecom', 'name' => 'biz_telecom'],
                                        ['label' => 'Transportasi', 'model' => 'bizTransport', 'name' => 'biz_transport'],
                                        ['label' => 'Listrik & Air', 'model' => 'bizUtilities', 'name' => 'biz_utilities'],
                                        ['label' => 'Sewa', 'model' => 'bizRent', 'name' => 'biz_rent'],
                                        ['label' => 'Lainnya', 'model' => 'bizOther', 'name' => 'biz_other'],
                                    ];
                                @endphp
                                @foreach($bizFields as $field)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td
                                            class="whitespace-nowrap py-2.5 pl-10 pr-3 text-sm text-gray-500 sm:pl-12 group-hover:text-blue-600 transition-colors">
                                            {{ $field['label'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="{{ $field['model'] }}Before"
                                                @input="updateCFValue('{{ $field['model'] }}Before', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right text-xs bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" name="{{ $field['name'] }}_before"
                                                x-model="{{ $field['model'] }}Before">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="{{ $field['model'] }}After"
                                                @input="updateCFValue('{{ $field['model'] }}After', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right text-xs bg-gray-50 focus:bg-white transition-all"
                                                placeholder="0">
                                            <input type="hidden" name="{{ $field['name'] }}_after"
                                                x-model="{{ $field['model'] }}After">
                                        </td>
                                    </tr>
                                @endforeach

                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-8 pr-3 text-sm text-gray-600 sm:pl-10 group-hover:text-blue-600 transition-colors">
                                        Beban Lainnya
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="otherExpBefore"
                                            @input="updateCFValue('otherExpBefore', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="other_expenses_before" x-model="otherExpBefore">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">
                                        <input type="text" x-model="otherExpAfter"
                                            @input="updateCFValue('otherExpAfter', $event.target.value)"
                                            class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right sm:text-sm bg-gray-50 focus:bg-white transition-all"
                                            placeholder="0">
                                        <input type="hidden" name="other_expenses_after" x-model="otherExpAfter">
                                    </td>
                                </tr>

                                <!-- Net Cash Flow (d) -->
                                <tr class="bg-gray-800 text-white border-t-4 border-gray-900">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold uppercase tracking-wide sm:pl-6">
                                        d. Arus Kas Bersih (b - c)
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold border-r border-gray-700 bg-gray-800/50"
                                        :class="netCashFlowBefore < 0 ? 'text-red-300' : 'text-emerald-300'"
                                        x-text="(netCashFlowBefore < 0 ? '(' + formatNumber(Math.abs(netCashFlowBefore)) + ')' : formatNumber(netCashFlowBefore))">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold bg-gray-800/50"
                                        :class="netCashFlowAfter < 0 ? 'text-red-300' : 'text-emerald-300'"
                                        x-text="(netCashFlowAfter < 0 ? '(' + formatNumber(Math.abs(netCashFlowAfter)) + ')' : formatNumber(netCashFlowAfter))">
                                    </td>
                                </tr>

                                <!-- Ending Operational Balance (e) -->
                                <tr class="bg-gray-100 border-t border-gray-200">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-extrabold text-gray-900 uppercase tracking-wide sm:pl-6">
                                        e. Saldo Akhir Operasional (a + d)
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold border-r border-gray-200 bg-blue-50/10"
                                        :class="endOpBalanceBefore < 0 ? 'text-red-700' : 'text-blue-800'"
                                        x-text="(endOpBalanceBefore < 0 ? '(' + formatNumber(Math.abs(endOpBalanceBefore)) + ')' : formatNumber(endOpBalanceBefore))">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold bg-emerald-50/10"
                                        :class="endOpBalanceAfter < 0 ? 'text-red-700' : 'text-blue-800'"
                                        x-text="(endOpBalanceAfter < 0 ? '(' + formatNumber(Math.abs(endOpBalanceAfter)) + ')' : formatNumber(endOpBalanceAfter))">
                                    </td>
                                </tr>

                                <!-- RPC Row -->
                                <tr class="bg-white border-t border-gray-200">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 flex items-center">
                                        RPC (Repayment Capacity)
                                        <div
                                            class="ml-2 flex items-center bg-gray-50 rounded-lg border border-gray-300 px-2 py-1 shadow-sm">
                                            <input type="number" step="1" name="rpc_ratio" x-model="rpcRatio"
                                                class="w-12 border-0 p-0 text-center text-xs focus:ring-0 text-blue-600 font-bold bg-transparent"
                                                placeholder="0">
                                            <span class="text-xs text-gray-400 font-semibold">%</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-700 bg-blue-50/10"
                                        x-text="formatNumber(rpcTotalBefore)">
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-700 bg-emerald-50/10">
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div
                        class="overflow-x-auto mt-6 rounded-xl border border-gray-200 shadow-sm ring-1 ring-gray-900/5 bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 sm:pl-6">
                                        Realisasi Kredit / Pembiayaan
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50/50">
                                        Kredit yang Diusulkan
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50/50">
                                        Rekomendasi Maksimum Pencairan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- BPR Credit Section -->
                                <tr class="bg-amber-50 border-t border-amber-200">
                                    <td colspan="3"
                                        class="px-4 py-3 text-sm font-bold text-amber-900 uppercase tracking-wide sm:pl-6">
                                        f. Realisasi Kredit / Pembiayaan dari BPR
                                    </td>
                                </tr>
                                <!-- f1 Proposed Credit -->
                                <tr class="bg-emerald-50 border-t border-emerald-100">
                                    <td
                                        class="whitespace-nowrap py-3 pl-8 pr-3 text-sm font-medium text-emerald-800 sm:pl-10">
                                        f1. Plafond Realisasi
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-700 bg-blue-50/10"
                                        x-text="formatNumber(loanAmount)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-emerald-700"
                                        x-text="formatNumber(maxLoanLimit)"></td>
                                </tr>

                                <tr class="bg-amber-50/30 hover:bg-amber-50/50 transition-colors">
                                    <td
                                        class="whitespace-nowrap py-3 pl-8 pr-3 text-sm font-medium text-amber-800 sm:pl-10">
                                        f2. Beban Realisasi
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-red-600"
                                        x-text="formatNumber(loanTotalRealizationCost)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-red-600"
                                        x-text="formatNumber(loanTotalRealizationCost)"></td>
                                </tr>

                                @php
                                    $loanCosts = [
                                        ['label' => 'Beban Provisi', 'model' => 'loanProvision', 'rate_model' => 'loanProvisionRate', 'manual_flag' => 'isProvisionManual', 'name' => 'loan_provision_cost'],
                                        ['label' => 'Beban Administrasi', 'model' => 'loanAdmin', 'rate_model' => 'loanAdminRate', 'manual_flag' => 'isAdminManual', 'name' => 'loan_administration_cost'],
                                    ];
                                @endphp

                                @foreach($loanCosts as $cost)
                                    <tr class="hover:bg-amber-50/40 transition-colors group">
                                        <td
                                            class="whitespace-nowrap py-2.5 pl-12 pr-3 text-xs text-gray-500 sm:pl-14 flex items-center group-hover:text-amber-700 transition-colors">
                                            {{ $cost['label'] }}
                                            <div
                                                class="ml-2 flex items-center bg-white rounded-lg border border-gray-200 px-1 shadow-sm">
                                                <input type="number" step="0.01"
                                                    name="{{ ($cost['name'] == 'loan_provision_cost') ? 'loan_provision_rate' : 'loan_admin_rate' }}"
                                                    x-model="{{ $cost['rate_model'] }}"
                                                    @input="{{ $cost['manual_flag'] }} = true"
                                                    class="w-12 border-0 p-0 text-center text-xs focus:ring-0 text-gray-600 font-medium"
                                                    placeholder="0">
                                                <span class="text-xs text-gray-400 p-1">%</span>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" name="{{ $cost['name'] }}" x-model="{{ $cost['model'] }}Amount"
                                                @input="updateCFValue('{{ $cost['model'] }}Amount', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-right text-xs bg-white focus:bg-amber-50 transition-all">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" :value="{{ $cost['model'] }}Amount" readonly tabindex="-1"
                                                class="block w-full rounded-lg border-gray-200 py-2 text-gray-500 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-right text-xs bg-gray-50 cursor-default transition-all">
                                            <input type="hidden" :name="'rekomendasi_' + '{{ $cost['name'] }}'"
                                                :value="{{ $cost['model'] }}Amount">
                                        </td>
                                    </tr>
                                @endforeach

                                @php
                                    $otherLoanCosts = [
                                        ['label' => 'Biaya Materai', 'model' => 'loanStampDuty', 'name' => 'loan_duty_stamp_cost'],
                                        ['label' => 'Biaya Notaris', 'model' => 'loanNotary', 'name' => 'loan_notary_public_cost'],
                                        ['label' => 'Biaya Asuransi', 'model' => 'loanInsurance', 'name' => 'loan_insurance_cost'],
                                        ['label' => 'Biaya Lainnya', 'model' => 'loanOtherCost', 'name' => 'loan_other_cost'],
                                    ];
                                @endphp

                                @foreach($otherLoanCosts as $cost)
                                    <tr class="hover:bg-amber-50/40 transition-colors group">
                                        <td
                                            class="whitespace-nowrap py-2.5 pl-12 pr-3 text-xs text-gray-500 sm:pl-14 group-hover:text-amber-700 transition-colors">
                                            {{ $cost['label'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" name="{{ $cost['name'] }}" x-model="{{ $cost['model'] }}"
                                                @input="updateCFValue('{{ $cost['model'] }}', $event.target.value)"
                                                class="block w-full rounded-lg border-gray-300 py-2 text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-right text-xs bg-white focus:bg-amber-50 transition-all">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" :value="{{ $cost['model'] }}" readonly tabindex="-1"
                                                class="block w-full rounded-lg border-gray-200 py-2 text-gray-500 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-right text-xs bg-gray-50 cursor-default transition-all">
                                            <input type="hidden" :name="'rekomendasi_' + '{{ $cost['name'] }}'"
                                                :value="{{ $cost['model'] }}">
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- f3 Installment -->
                                <tr class="bg-orange-50 border-t border-orange-100">
                                    <td
                                        class="whitespace-nowrap py-3 pl-8 pr-3 text-sm font-medium text-orange-800 sm:pl-10">
                                        f3. Beban Cicilan
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-orange-700"
                                        x-text="formatNumber(monthlyInstallment)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-emerald-700"
                                        x-text="formatNumber(recMonthlyInstallment)"></td>
                                </tr>
                                <tr class="hover:bg-orange-50/50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-12 pr-3 text-xs text-gray-500 sm:pl-14 flex items-center group-hover:text-orange-700 transition-colors">
                                        Bunga Flat p.a
                                        <div
                                            class="ml-2 flex items-center bg-white rounded-lg border border-gray-200 px-1 shadow-sm">
                                            <input type="number" step="0.01" name="interest_rate" x-model="interestRate"
                                                class="w-12 border-0 p-0 text-center text-xs focus:ring-0 text-gray-600 font-medium bg-transparent">
                                            <span class="text-xs text-gray-400 p-1">%</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5 text-right text-xs font-medium text-orange-600"
                                        x-text="formatNumber(monthlyInterest)"></td>
                                    <td class="whitespace-nowrap px-3 py-1.5 text-right text-xs font-medium text-emerald-600"
                                        x-text="formatNumber(recMonthlyInterest)"></td>
                                </tr>
                                <tr class="hover:bg-orange-50/50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-12 pr-3 text-xs text-gray-500 sm:pl-14 group-hover:text-orange-700 transition-colors">
                                        Cicilan Pokok
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-1.5 text-right text-xs font-medium text-orange-600"
                                        x-text="formatNumber(monthlyPrincipal)"></td>
                                    <td class="whitespace-nowrap px-3 py-1.5 text-right text-xs font-medium text-emerald-600"
                                        x-text="formatNumber(recMonthlyPrincipal)"></td>
                                </tr>
                                <tr class="hover:bg-orange-50/50 transition-colors group">
                                    <td
                                        class="whitespace-nowrap py-2.5 pl-12 pr-3 text-xs text-gray-500 sm:pl-14 flex items-center group-hover:text-orange-700 transition-colors">
                                        Tenor (Bulan)
                                    </td>
                                    <td colspan="2" class="whitespace-nowrap px-3 py-2">
                                        <input type="number" name="loan_tenor" x-model="loanTerm"
                                            class="block w-full rounded-lg border-gray-300 py-1.5 text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-right text-xs font-bold bg-white focus:bg-orange-50 transition-all">
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Risk Alert -->
                    <div class="mt-4" x-show="loanAmount > 0 || maxLoanLimit > 0">
                        <div x-show="parseFloat(loanAmount) <= maxLoanLimit"
                            class="flex items-center gap-3 p-4 rounded-xl border border-green-200 bg-green-50 shadow-sm">
                            <span class="flex-shrink-0 bg-green-100 text-green-600 p-2 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-green-800">Aman — Kredit yang diusulkan dalam batas
                                    rekomendasi</p>
                                <p class="text-xs text-green-600 mt-0.5">Plafond yang diusulkan (<span
                                        x-text="formatNumber(loanAmount)"></span>) ≤ Rekomendasi Maksimum (<span
                                        x-text="formatNumber(maxLoanLimit)"></span>)</p>
                            </div>
                        </div>
                        <div x-show="parseFloat(loanAmount) > maxLoanLimit"
                            class="flex items-center gap-3 p-4 rounded-xl border border-red-200 bg-red-50 shadow-sm">
                            <span class="flex-shrink-0 bg-red-100 text-red-600 p-2 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-red-800">Beresiko — Kredit yang diusulkan melebihi batas
                                    rekomendasi</p>
                                <p class="text-xs text-red-600 mt-0.5">Plafond yang diusulkan (<span
                                        x-text="formatNumber(loanAmount)"></span>) > Rekomendasi Maksimum (<span
                                        x-text="formatNumber(maxLoanLimit)"></span>)</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-x-auto mt-6 rounded-xl border border-gray-200 shadow-sm ring-1 ring-gray-900/5 bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 sm:pl-6">
                                        Keterangan Saldo Akhir
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50/50">
                                        Sebelum Pencairan
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50/50">
                                        Setelah Pencairan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- g. Final Cash Balance -->
                                <tr class="bg-slate-900 text-white border-t-4 border-slate-950">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold uppercase tracking-wide sm:pl-6">
                                        g. Saldo Akhir Kas & Bank (e - f1 + f2 - f3)
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold bg-slate-800/50"
                                        :class="endCashBankBefore < 0 ? 'text-red-300' : 'text-emerald-300'"
                                        x-text="(endCashBankBefore < 0 ? '(' + formatNumber(Math.abs(endCashBankBefore)) + ')' : formatNumber(endCashBankBefore))">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold bg-slate-800/50"
                                        :class="endCashBankAfter < 0 ? 'text-red-300' : 'text-emerald-300'"
                                        x-text="(endCashBankAfter < 0 ? '(' + formatNumber(Math.abs(endCashBankAfter)) + ')' : formatNumber(endCashBankAfter))">
                                    </td>
                                </tr>

                                <!-- h. Loan Balance -->
                                <tr class="bg-white border-t border-gray-200">
                                    <td
                                        class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-bold text-gray-700 uppercase tracking-wide sm:pl-6">
                                        h. Saldo Baki Debet
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-900"
                                        x-text="formatNumber(loanRemBalanceBefore)"></td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm font-bold text-gray-900"
                                        x-text="formatNumber(loanRemBalanceAfter)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center pt-8">
                        <button type="button" @click="currentStep = 2"
                            class="flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Data SLIK
                        </button>
                        <button type="button" @click="currentStep = 4"
                            class="flex items-center gap-2 px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg active:scale-95">
                            Lanjut ke Analisa Aset
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Neraca (Assets) -->
                <div x-show="currentStep === 4" style="display: none;" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4">
                        <span class="bg-purple-100 text-purple-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </span>
                        Bagian 4 : Neraca / Aset
                    </h2>

                    <!-- Custom Assets Table -->
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Aset</h3>
                    <div class="bg-blue-50/50 p-4 border-l-4 border-blue-500 rounded-r-lg mb-6">
                        <p class="text-sm text-blue-800">
                            Masukkan rincian aset yang dimiliki.
                        </p>
                    </div>

                    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200">
                                    <th scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 sm:pl-6">
                                        Nama Aset
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                        Jenis Aset
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-600">
                                        Perkiraan Harga Aset
                                    </th>
                                    <th scope="col" class="px-3 py-3 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(asset, index) in customAssetsList" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="whitespace-nowrap py-2 pl-4 pr-3 sm:pl-6">
                                            <input type="text" x-model="asset.name" :name="'custom_assets['+index+'][name]'"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                placeholder="Contoh: Rumah Tinggal">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="asset.type" :name="'custom_assets['+index+'][type]'"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                placeholder="Contoh: Properti">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <input type="text" x-model="asset.estimated_price"
                                                :name="'custom_assets['+index+'][estimated_price]'"
                                                @input="asset.estimated_price = $event.target.value.replace(/\D/g, '')"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right sm:text-sm"
                                                placeholder="0">
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-2 text-right">
                                            <button type="button" @click="removeCustomAsset(index)"
                                                class="text-red-500 hover:text-red-700 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-blue-50 border-t-2 border-gray-200 font-bold text-gray-900">
                                    <td colspan="2" class="whitespace-nowrap py-3 pl-4 pr-3 text-sm sm:pl-6 text-right">
                                        Total Harga Aset
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-sm text-blue-800"
                                        x-text="'Rp ' + formatNumber(totalCustomAssets)"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                            <button type="button" @click="addCustomAsset()"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Aset
                            </button>
                        </div>
                    </div>

                    <!-- NERACA (Balance Sheet) -->
                    <div class="mb-4" x-show="entrepreneurshipStatus === 'Wirausaha'" x-cloak>
                        <div class="bg-gray-400 p-2 font-bold text-gray-900 border border-gray-400">
                            NERACA
                        </div>
                        <div class="overflow-x-auto shadow-sm">
                            <table class="w-full text-sm border-x border-b border-gray-400">
                                <tbody class="bg-white">
                                    <!-- AKTIVA LANCAR & KEWAJIBAN Headers -->
                                    <tr>
                                        <td class="pt-2 pb-1 pl-2 font-bold w-[35%]"><u class="underline-offset-2">AKTIVA
                                                LANCAR</u></td>
                                        <td class="pt-2 pb-1 pr-2 w-[15%]"></td>
                                        <td class="pt-2 pb-1 pl-2 font-bold w-[35%]"><u
                                                class="underline-offset-2">KEWAJIBAN</u></td>
                                        <td class="pt-2 pb-1 pr-2 w-[15%]"></td>
                                    </tr>

                                    <!-- Row 1: Kas Usaha | Pinjaman Bank Lain -->
                                    <tr>
                                        <td class="py-1 pl-2 text-gray-800">Kas Usaha</td>
                                        <td class="py-1 pr-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 mr-1">Rp</span>
                                                <span x-text="formatNumber(openingCash)"></span>
                                            </div>
                                        </td>
                                        <td class="py-1 pl-2 text-gray-800">Pinjaman di Bank Lain / Pihak Lain</td>
                                        <td class="py-1 pr-2 text-right text-gray-800">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 mr-1">Rp</span>
                                                <span x-text="formatNumber(totalExternalLoanOutstanding)"></span>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Row 2: Piutang Usaha | Kewajiban Lancar -->
                                    <tr>
                                        <td class="py-1 pl-2 text-gray-800">Piutang Usaha</td>
                                        <td class="py-1 pr-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 mr-1">Rp</span>
                                                <span x-text="formatNumber(openingSavings)"></span>
                                            </div>
                                        </td>
                                        <td class="py-1 pl-2 text-gray-800">Kewajiban Lancar / Hutang Dagang</td>
                                        <td class="py-1 pr-2 text-right">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 mr-1">Rp</span>
                                                <input type="text" :value="formatNumber(kewajibanLancar)"
                                                    @input="kewajibanLancar = $event.target.value.replace(/[^\d]/g, '')"
                                                    class="w-full text-right p-0 border-0 focus:ring-0 text-sm bg-transparent">
                                                <input type="hidden" name="kewajiban_lancar" x-model="kewajibanLancar">
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Row 3: Persediaan | (empty) -->
                                    <tr>
                                        <td class="py-1 pl-2 text-gray-800">Persediaan</td>
                                        <td class="py-1 pr-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 mr-1">Rp</span>
                                                <span x-text="formatNumber(openingGiro)"></span>
                                            </div>
                                        </td>
                                        <td class="py-1 pl-2"></td>
                                        <td class="py-1 pr-2"></td>
                                    </tr>

                                    <!-- TOTAL AKTIVA LANCAR | TOTAL KEWAJIBAN LANCAR -->
                                    <tr>
                                        <td class="py-2 pl-8 font-bold text-gray-900 text-center">TOTAL AKTIVA LANCAR</td>
                                        <td class="py-2 pr-2 font-bold text-right text-gray-900">
                                            <div class="flex items-center justify-between">
                                                <span class="mr-1">Rp</span>
                                                <span x-text="formatNumber(totalAktivaLancar)"></span>
                                            </div>
                                        </td>
                                        <td class="py-2 pl-8 font-bold text-gray-900 text-center">TOTAL KEWAJIBAN LANCAR
                                        </td>
                                        <td class="py-2 pr-2 font-bold text-right text-gray-900">
                                            <div class="flex items-center justify-between">
                                                <span class="mr-1">Rp</span>
                                                <span x-text="formatNumber(totalKewajibanLancar)"></span>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- ASET & MODAL Headers -->
                                    <tr>
                                        <td class="pt-3 pb-1 pl-2 font-bold"><u class="underline-offset-2">ASET</u></td>
                                        <td class="pt-3 pb-1 pr-2"></td>
                                        <td class="pt-3 pb-1 pl-2 font-bold"><u class="underline-offset-2">MODAL</u></td>
                                        <td class="pt-3 pb-1 pr-2"></td>
                                    </tr>

                                    <!-- Custom Assets & Modal Rows -->
                                    <template x-for="(asset, aIdx) in customAssetsList" :key="'neraca-asset-' + aIdx">
                                        <tr>
                                            <td class="py-1 pl-2 text-gray-800">
                                                <span x-text="(asset.name ? asset.name : 'Aset ' + (aIdx + 1))"></span>
                                            </td>
                                            <td class="py-1 pr-2 text-right text-gray-800">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-600 mr-1">Rp</span>
                                                    <span
                                                        x-text="formatNumber(String(asset.estimated_price || 0).replace(/\D/g, ''))"></span>
                                                </div>
                                            </td>

                                            <!-- Modal section -->
                                            <td class="py-1 pl-2 text-gray-800">
                                                <template x-if="aIdx === 0"><span>Kewajiban Jangka Panjang</span></template>
                                                <template x-if="aIdx === 1"><span>Laba Berjalan</span></template>
                                                <template x-if="aIdx === 2"><span>Modal Usaha</span></template>
                                            </td>
                                            <td class="py-1 pr-2 text-right">
                                                <template x-if="aIdx === 0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-gray-600 mr-1">Rp</span>
                                                        <input type="text" :value="formatNumber(kewajibanJangkaPanjang)"
                                                            @input="kewajibanJangkaPanjang = $event.target.value.replace(/[^\d]/g, '')"
                                                            class="w-full text-right p-0 border-0 focus:ring-0 text-sm bg-transparent">
                                                        <input type="hidden" name="kewajiban_jangka_panjang"
                                                            x-model="kewajibanJangkaPanjang">
                                                    </div>
                                                </template>
                                                <template x-if="aIdx === 1">
                                                    <div class="flex items-center justify-between text-gray-800">
                                                        <span class="text-gray-600 mr-1">Rp</span>
                                                        <span x-text="formatNumber(labaBerjalan)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="aIdx === 2">
                                                    <div class="flex items-center justify-between text-gray-800">
                                                        <span class="text-gray-600 mr-1">Rp</span>
                                                        <span x-text="formatNumber(modalUsaha)"></span>
                                                        <input type="hidden" name="modal_usaha" :value="modalUsaha">
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Fallback Modal Rows (if < 3 assets) -->
                                    <template x-if="customAssetsList.length < 1">
                                        <tr>
                                            <td class="py-1 pl-2"></td>
                                            <td class="py-1 pr-2"></td>
                                            <td class="py-1 pl-2 text-gray-800">Kewajiban Jangka Panjang</td>
                                            <td class="py-1 pr-2 text-right">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-600 mr-1">Rp</span>
                                                    <input type="text" :value="formatNumber(kewajibanJangkaPanjang)"
                                                        @input="kewajibanJangkaPanjang = $event.target.value.replace(/[^\d]/g, '')"
                                                        class="w-full text-right p-0 border-0 focus:ring-0 text-sm bg-transparent">
                                                    <input type="hidden" name="kewajiban_jangka_panjang"
                                                        x-model="kewajibanJangkaPanjang">
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="customAssetsList.length < 2">
                                        <tr>
                                            <td class="py-1 pl-2"></td>
                                            <td class="py-1 pr-2"></td>
                                            <td class="py-1 pl-2 text-gray-800">Laba Berjalan</td>
                                            <td class="py-1 pr-2 text-right text-gray-800">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-600 mr-1">Rp</span>
                                                    <span x-text="formatNumber(labaBerjalan)"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="customAssetsList.length < 3">
                                        <tr>
                                            <td class="py-1 pl-2"></td>
                                            <td class="py-1 pr-2"></td>
                                            <td class="py-1 pl-2 text-gray-800">Modal Usaha</td>
                                            <td class="py-1 pr-2 text-right text-gray-800">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-600 mr-1">Rp</span>
                                                    <span x-text="formatNumber(modalUsaha)"></span>
                                                    <input type="hidden" name="modal_usaha" :value="modalUsaha">
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- TOTAL ASSET | TOTAL KEWAJIBAN DAN MODAL -->
                                    <tr>
                                        <td class="py-2 pl-8 font-bold text-gray-900 text-center">TOTAL ASSET</td>
                                        <td class="py-2 pr-2 font-bold text-right text-gray-900">
                                            <div class="flex items-center justify-between">
                                                <span class="mr-1">Rp</span>
                                                <span x-text="formatNumber(totalAssetNeraca)"></span>
                                            </div>
                                        </td>
                                        <td class="py-2 pl-8 font-bold text-gray-900 text-center">TOTAL KEWAJIBAN DAN MODAL
                                        </td>
                                        <td class="py-2 pr-2 font-bold text-right text-gray-900">
                                            <div class="flex items-center justify-between">
                                                <span class="mr-1">Rp</span>
                                                <span x-text="formatNumber(totalKewajibanDanModal)"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="currentStep = 3"
                            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition-colors">Kembali</button>
                        <button type="button" @click="currentStep = 5"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none transition-colors">Lanjut
                            ke Data Agunan</button>
                    </div>
                </div>

                <!-- Step 5: Agunan (Collateral) -->
                <div x-show="currentStep === 5" style="display: none;" class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between border-b pb-4 mb-6 gap-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                            <span class="bg-orange-100 text-orange-600 p-2 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </span>
                            Bagian 5 : Data Agunan
                        </h2>

                        <!-- Search Button (Moved to Header) -->
                        <button type="button" @click="showCollateralModal = true"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Agunan Lama
                        </button>
                    </div>

                    <!-- Inline Collateral Search Section -->
                    <div x-show="showCollateralModal" x-transition
                        class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 shadow-inner">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-700">Pilih dari Agunan Lama</h3>
                            <button type="button" @click="showCollateralModal = false"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Search & Filter -->
                        <div class="mb-4 flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text" x-model="collateralSearch"
                                    placeholder="Cari pemilik, nomor sertifikat/polisi, alamat..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex rounded-md shadow-sm" role="group">
                                <button type="button" @click="collateralFilter = 'all'"
                                    :class="collateralFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-l-lg focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                    Semua
                                </button>
                                <button type="button" @click="collateralFilter = 'certificate'"
                                    :class="collateralFilter === 'certificate' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium border-t border-b border-r border-gray-200 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                    Sertifikat
                                </button>
                                <button type="button" @click="collateralFilter = 'vehicle'"
                                    :class="collateralFilter === 'vehicle' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium border-t border-b border-r border-gray-200 rounded-r-lg focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                    Kendaraan
                                </button>
                            </div>
                        </div>

                        <!-- List -->
                        <div class="overflow-y-auto max-h-60 border bg-white rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Detail Utama</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi / Keterangan</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Pilih</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(col, index) in filteredAvailableCollaterals" :key="col.id || index">
                                        <tr class="hover:bg-blue-50 transition-colors cursor-pointer"
                                            @click="selectExistingCollateral(col)">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="col.type === 'certificate' ? 'bg-indigo-100 text-indigo-800' : 'bg-orange-100 text-orange-800'"
                                                    x-text="col.type === 'certificate' ? 'Sertifikat' : 'Kendaraan'">
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900"
                                                    x-text="col.type === 'certificate' ? (col.certificate_number || '-') : (col.police_number || '-')">
                                                </div>
                                                <div class="text-xs text-gray-500" x-text="col.owner_name"></div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <div x-show="col.type === 'certificate'">
                                                    <span x-text="col.location_address"></span>
                                                </div>
                                                <div x-show="col.type === 'vehicle'">
                                                    <span
                                                        x-text="col.brand + ' ' + col.model + ' (' + col.year + ')'"></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button"
                                                    class="text-blue-600 hover:text-blue-900 font-bold hover:underline">PILIH</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="filteredAvailableCollaterals.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Tidak ada data agunan ditemukan untuk filter ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        @update-collateral-address.window="
                                                                                                                                                            const idx = $event.detail.index;
                                                                                                                                                            if(collaterals[idx]) {
                                                                                                                                                                collaterals[idx].latitude = $event.detail.lat;
                                                                                                                                                                collaterals[idx].longitude = $event.detail.lng;
                                                                                                                                                                collaterals[idx].village = $event.detail.village;
                                                                                                                                                                collaterals[idx].district = $event.detail.district;
                                                                                                                                                                collaterals[idx].regency = $event.detail.regency;
                                                                                                                                                                collaterals[idx].province = $event.detail.province;
                                                                                                                                                            }
                                                                                                                                                        ">
                        <template x-for="(col, index) in collaterals" :key="index">
                            <div
                                class="border border-gray-200 rounded-xl p-6 mb-6 bg-gray-50/50 relative hover:shadow-md transition-all group">
                                <div class="absolute top-4 right-4 flex items-center gap-2">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400"
                                        x-text="'Agunan #' + (index + 1)"></span>
                                    <button type="button" @click="removeCollateral(index)"
                                        class="text-red-600 hover:text-white hover:bg-red-600 rounded-lg p-1.5 transition-colors"
                                        title="Hapus Agunan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="md:col-span-2">
                                        <label class="block mb-2 text-sm font-bold text-gray-700">Jenis Agunan</label>
                                        <select :name="'collaterals['+index+'][type]'" x-model="col.type"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                            <option value="certificate">Sertifikat (Tanah / Bangunan)</option>
                                            <option value="vehicle">Kendaraan Bermotor (BPKB)</option>
                                        </select>
                                    </div>

                                    <!-- Common Fields -->
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama
                                            Pemilik</label>
                                        <input type="text" :name="'collaterals['+index+'][owner_name]'"
                                            x-model="col.owner_name"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">No. KTP
                                            Pemilik</label>
                                        <input type="text" :name="'collaterals['+index+'][owner_ktp]'"
                                            x-model="col.owner_ktp"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis
                                            Bukti</label>
                                        <select :name="'collaterals['+index+'][proof_type]'" x-model="col.proof_type"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                            <template x-if="col.type === 'certificate'">
                                                <optgroup label="Pilh Jenis Sertifikat">
                                                    <option value="SHM">SHM (Hak Milik)</option>
                                                    <option value="SHGB">SHGB (Guna Bangunan)</option>
                                                    <option value="AJB">AJB</option>
                                                    <option value="Petok D">Petok D</option>
                                                    <option value="Letter C">Letter C</option>
                                                    <option value="Sewa Beli">Sewa Beli</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </optgroup>
                                            </template>
                                            <template x-if="col.type === 'vehicle'">
                                                <optgroup label="Dokumen Kendaraan">
                                                    <option value="BPKB">BPKB</option>
                                                    <option value="STNK Only">STNK Only (Darurat)</option>
                                                </optgroup>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">
                                            <span
                                                x-text="col.type === 'certificate' ? 'No. SHM / SHGB' : 'No. BPKB'"></span>
                                        </label>
                                        <input type="text" :name="'collaterals['+index+'][proof_number]'"
                                            x-model="col.proof_number"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nilai Pasar
                                            (Estimasi)</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-bold">Rp</span>
                                            </div>
                                            <input type="text" :name="'collaterals['+index+'][market_value]'"
                                                x-model="col.market_value"
                                                @input="formatCollateralValue(index, 'market_value', $event.target.value)"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm font-bold">
                                        </div>
                                    </div>
                                    <div class="md:col-span-2" x-effect="col.bank_value = formatNumber(getBankValue(col))">
                                        <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nilai Bank
                                            (Taksasi)</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-bold">Rp</span>
                                            </div>
                                            <input type="text" :name="'collaterals['+index+'][bank_value]'"
                                                x-model="col.bank_value" readonly
                                                class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full pl-10 p-2.5 shadow-sm font-bold cursor-not-allowed">
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400"
                                            x-text="col.type === 'certificate' ? 'Sertifikat: 80% dari Nilai Pasar' : 'BPKB: 50% dari Nilai Pasar'">
                                        </p>
                                    </div>

                                    <!-- Certificate Specific Fields -->
                                    <div x-show="col.type === 'certificate'"
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 contents">
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Luas Tanah
                                                (m2)</label>
                                            <input type="number" :name="'collaterals['+index+'][land_area]'"
                                                x-model="col.land_area"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Luas
                                                Bangunan (m2)</label>
                                            <input type="number" :name="'collaterals['+index+'][building_area]'"
                                                x-model="col.building_area"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Peruntukan
                                                Tanah</label>
                                            <input type="text" :name="'collaterals['+index+'][peruntukan_tanah]'"
                                                x-model="col.peruntukan_tanah"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"
                                                placeholder="Contoh: Perumahan">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Lebar Jalan
                                                (m)</label>
                                            <input type="number" step="0.01" :name="'collaterals['+index+'][lebar_jalan]'"
                                                x-model="col.lebar_jalan"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Kondisi
                                                Bangunan</label>
                                            <input type="text" :name="'collaterals['+index+'][kondisi_bangunan]'"
                                                x-model="col.kondisi_bangunan"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Material
                                                Pondasi</label>
                                            <input type="text" :name="'collaterals['+index+'][material_pondasi]'"
                                                x-model="col.material_pondasi"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Material
                                                Tembok</label>
                                            <input type="text" :name="'collaterals['+index+'][material_tembok]'"
                                                x-model="col.material_tembok"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Material
                                                Atap</label>
                                            <input type="text" :name="'collaterals['+index+'][material_atap]'"
                                                x-model="col.material_atap"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Material
                                                Kusen</label>
                                            <input type="text" :name="'collaterals['+index+'][material_kusen]'"
                                                x-model="col.material_kusen"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Material
                                                Daun Pintu</label>
                                            <input type="text" :name="'collaterals['+index+'][material_daun_pintu]'"
                                                x-model="col.material_daun_pintu"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Alamat
                                                Lokasi</label>
                                            <textarea :name="'collaterals['+index+'][location_address]'"
                                                x-model="col.location_address" rows="2"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"></textarea>
                                        </div>

                                        <!-- Map Trigger for Certificate -->
                                        <!-- Map Trigger moved to shared section below -->

                                        <!-- Image Uploads for Certificate (4 slots) -->
                                        <div class="md:col-span-2 mt-2">
                                            <label class="block mb-3 text-sm font-bold text-gray-700 border-b pb-1">Dokumen
                                                & Foto (Sertifikat)</label>
                                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                <!-- Loop for 4 images: Sertifikat, PBB, IMB, Lokasi -->
                                                <template
                                                    x-for="(label, imgIdx) in ['Foto Tampak Depan', 'Foto Tampak Jalan', 'Foto Tampak Dalam', 'Lokasi Jaminan']">
                                                    <div class="space-y-2">
                                                        <label class="block text-xs font-bold text-gray-500"
                                                            x-text="label"></label>
                                                        <div class="flex items-center justify-center w-full">
                                                            <label :for="'col_img_' + index + '_' + imgIdx"
                                                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                                                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-2"
                                                                    :id="'col-placeholder-' + index + '-' + imgIdx">
                                                                    <svg class="w-6 h-6 mb-2 text-gray-400" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                    <p class="text-[10px] text-gray-500">Upload</p>
                                                                </div>
                                                                <img :id="'col-preview-' + index + '-' + imgIdx"
                                                                    class="hidden absolute h-full w-full object-cover" />
                                                                <input :id="'col_img_' + index + '_' + imgIdx"
                                                                    :name="col.type === 'certificate' ? 'collaterals['+index+'][images]['+imgIdx+']' : ''"
                                                                    type="file" class="hidden photo-input"
                                                                    :data-preview="'col-preview-' + index + '-' + imgIdx"
                                                                    :data-placeholder="'col-placeholder-' + index + '-' + imgIdx"
                                                                    :data-base64="'col_img_data_' + index + '_' + imgIdx"
                                                                    accept="image/*" />
                                                                <input type="hidden"
                                                                    :id="'col_img_data_' + index + '_' + imgIdx"
                                                                    :name="'collaterals['+index+'][images_data]['+imgIdx+']'" />
                                                            </label>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vehicle Specific Fields -->
                                    <div x-show="col.type === 'vehicle'"
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 contents">
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Merk
                                                Kendaraan</label>
                                            <input type="text" :name="'collaterals['+index+'][brand]'" x-model="col.brand"
                                                placeholder="Contoh: Honda"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tipe /
                                                Model</label>
                                            <input type="text" :name="'collaterals['+index+'][model]'" x-model="col.model"
                                                placeholder="Contoh: Vario 150"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tahun
                                                Pembuatan</label>
                                            <input type="number" :name="'collaterals['+index+'][year]'" x-model="col.year"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-1 text-xs font-bold text-gray-500 uppercase">Warna</label>
                                            <input type="text" :name="'collaterals['+index+'][color]'" x-model="col.color"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nomor
                                                Polisi</label>
                                            <input type="text" :name="'collaterals['+index+'][police_number]'"
                                                x-model="col.police_number"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">No.
                                                Rangka</label>
                                            <input type="text" :name="'collaterals['+index+'][chassis_number]'"
                                                x-model="col.chassis_number"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">No.
                                                Mesin</label>
                                            <input type="text" :name="'collaterals['+index+'][engine_number]'"
                                                x-model="col.engine_number"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm">
                                        </div>

                                        <!-- Image Uploads for Vehicle (4 slots) -->
                                        <div class="md:col-span-2 mt-2">
                                            <label class="block mb-3 text-sm font-bold text-gray-700 border-b pb-1">Dokumen
                                                & Foto (Kendaraan)</label>
                                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                <template
                                                    x-for="(label, imgIdx) in ['Foto Depan Tampak Plat', 'Foto Samping', 'Foto Belakang', 'Lokasi Jaminan']">
                                                    <div class="space-y-2">
                                                        <label class="block text-xs font-bold text-gray-500"
                                                            x-text="label"></label>
                                                        <div class="flex items-center justify-center w-full">
                                                            <label :for="'col_veh_img_' + index + '_' + imgIdx"
                                                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden group">
                                                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-2"
                                                                    :id="'col-veh-placeholder-' + index + '-' + imgIdx">
                                                                    <svg class="w-6 h-6 mb-2 text-gray-400" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                    <p class="text-[10px] text-gray-500">Upload</p>
                                                                </div>
                                                                <img :id="'col-veh-preview-' + index + '-' + imgIdx"
                                                                    class="hidden absolute h-full w-full object-cover" />
                                                                <input :id="'col_veh_img_' + index + '_' + imgIdx"
                                                                    :name="col.type === 'vehicle' ? 'collaterals['+index+'][images]['+imgIdx+']' : ''"
                                                                    type="file" class="hidden photo-input"
                                                                    :data-preview="'col-veh-preview-' + index + '-' + imgIdx"
                                                                    :data-placeholder="'col-veh-placeholder-' + index + '-' + imgIdx"
                                                                    :data-base64="'col_veh_img_data_' + index + '_' + imgIdx"
                                                                    accept="image/*" />
                                                                <input type="hidden"
                                                                    :id="'col_veh_img_data_' + index + '_' + imgIdx"
                                                                    :name="'collaterals['+index+'][images_data]['+imgIdx+']'" />
                                                            </label>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shared Map & Location Section (For Both Types) -->
                                    <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-bold text-gray-800 mb-3 flex items-center justify-between">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Lokasi Agunan (Wajib Diisi)
                                            </span>
                                            <span :id="'collateral-distance-badge-' + index"
                                                class="hidden text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap transition-colors duration-300"></span>
                                        </h5>
                                        <input type="hidden" :name="'collaterals['+index+'][path_distance]'"
                                            :id="'collateral-path-distance-' + index" value="0">

                                        <!-- Embedded Map -->
                                        <!-- Embedded Map & Capture -->
                                        <div class="mb-4">
                                            <div :id="'collateral-map-' + index"
                                                class="h-64 w-full rounded-lg z-0 mb-2 border border-blue-200 shadow-inner"
                                                x-init="$nextTick(() => initCollateralMap('collateral-map-' + index, index))">
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="button" :id="'btn-loc-' + index"
                                                    @click="getCollateralLocation(index)"
                                                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 shadow-sm transition-all mr-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Baca Lokasi Saat Ini
                                                </button>
                                                <button type="button" @click="captureCollateralMap(index)"
                                                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Ambil Foto Lokasi (Snapshot)
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Coordinates -->
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-bold text-gray-500 uppercase">Latitude</label>
                                                <input type="text" :name="'collaterals['+index+'][latitude]'"
                                                    x-model="col.latitude" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-500 text-xs rounded-lg block w-full p-2.5">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-bold text-gray-500 uppercase">Longitude</label>
                                                <input type="text" :name="'collaterals['+index+'][longitude]'"
                                                    x-model="col.longitude" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-500 text-xs rounded-lg block w-full p-2.5">
                                            </div>

                                            <!-- Address Fields -->
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Alamat
                                                    Lengkap (Jalan, RT/RW)</label>
                                                <textarea :name="'collaterals['+index+'][location_address]'"
                                                    x-model="col.location_address" rows="2"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm"></textarea>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Desa /
                                                    Kelurahan</label>
                                                <input type="text" :name="'collaterals['+index+'][village]'"
                                                    x-model="col.village" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2.5">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-bold text-gray-500 uppercase">Kecamatan</label>
                                                <input type="text" :name="'collaterals['+index+'][district]'"
                                                    x-model="col.district" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2.5">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-bold text-gray-500 uppercase">Kabupaten /
                                                    Kota</label>
                                                <input type="text" :name="'collaterals['+index+'][regency]'"
                                                    x-model="col.regency" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2.5">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-bold text-gray-500 uppercase">Provinsi</label>
                                                <input type="text" :name="'collaterals['+index+'][province]'"
                                                    x-model="col.province" readonly
                                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2.5">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </template>

                        <!-- Total Collateral Bank Value Summary -->
                        <div x-show="collaterals.length > 0"
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-center border border-blue-200 mt-4 shadow-sm">
                            <div class="flex items-center gap-3 mb-2 sm:mb-0">
                                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </span>
                                <div>
                                    <span class="font-bold text-gray-900 text-sm">Total Nilai Taksasi Bank</span>
                                    <p class="text-xs text-gray-500">Jumlah seluruh nilai bank dari semua agunan</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-blue-600 block font-medium">Rp</span>
                                <span class="text-2xl font-black text-blue-700"
                                    x-text="formatNumber(totalCollateralBankValue)"></span>
                            </div>
                        </div>

                        <button type="button" @click="addCollateral()"
                            class="mt-4 flex items-center justify-center w-full py-4 border-2 border-dashed border-blue-300 rounded-xl text-blue-600 hover:border-blue-500 hover:bg-blue-50/50 transition-all font-bold shadow-sm group">
                            <span class="bg-blue-100 p-2 rounded-full mr-2 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </span>
                            Tambah Item Agunan
                        </button>

                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="currentStep = 4"
                            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition-colors">Kembali</button>
                        <button type="button" @click="currentStep = 6"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none transition-colors">Lanjut</button>
                    </div>
                </div>

                <!-- Step 6: Analisa 5C -->
                <div x-show="currentStep === 6" style="display: none;" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4">
                        <span class="bg-red-100 text-red-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                        </span>
                        Bagian 6 : Analisa 5C — Character
                    </h2>

                    <!-- Character Scoring Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Penilaian Karakter (Character)
                            </h3>
                            <p class="text-indigo-200 text-xs mt-0.5">Bobot total: 100%</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">
                                            Komponen</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                            Bobot</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">
                                            Skor Tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">

                                    <!-- 1. Credit Bureau History (25%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Riwayat Kredit (SLIK)</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Diambil dari kolektibilitas terburuk
                                                pinjaman eksternal</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">25%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="charCreditBureau" name="char_credit_bureau"
                                                    x-init="$watch('worstCollectibility', val => { charCreditBureau = val }); charCreditBureau = charCreditBureau || worstCollectibility;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-blue-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Lancar</option>
                                                    <option value="4">4 — Dalam Perhatian Khusus (DPK)</option>
                                                    <option value="3">3 — Kurang Lancar</option>
                                                    <option value="2">2 — Diragukan</option>
                                                    <option value="1">1 — Macet</option>
                                                </select>
                                                <span class="text-xs text-blue-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <!-- Bad Collectibility Warning -->
                                            <div x-show="parseInt(charCreditBureau) <= 3" x-transition
                                                class="mt-2 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-xs font-semibold text-red-700">
                                                    ⚠️ Debitur memiliki <span class="font-black"
                                                        x-text="badCollectibilityLoans.length"></span> kredit
                                                    <span class="font-black underline"
                                                        x-text="worstCollectibilityLabel"></span>
                                                    dengan total baki debet
                                                    <span class="font-black">Rp <span
                                                            x-text="formatNumber(badCollectibilityTotalOutstanding)"></span></span>
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-indigo-700"
                                                x-text="(parseInt(charCreditBureau) / 5 * 25).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 2. Information Consistency (20%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Konsistensi Informasi</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Keterbukaan nasabah terhadap informasi
                                                yang dibutuhkan bank</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="charInfoConsistency" name="char_info_consistency"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="4">4 — Terbuka terhadap setiap informasi yang dibutuhkan bank
                                                </option>
                                                <option value="3">3 — Cukup terbuka terhadap informasi yang dibutuhkan bank
                                                </option>
                                                <option value="2">2 — Tidak terbuka terhadap informasi yang dibutuhkan bank
                                                </option>
                                                <option value="1">1 — Tertutup terhadap informasi yang dibutuhkan bank
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-indigo-700"
                                                x-text="(parseInt(charInfoConsistency) / 4 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 3. Relationship & Track Record (10%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">3. Hubungan & Rekam Jejak</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan status nasabah (lama /
                                                baru)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">10%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="charRelationship" name="char_relationship"
                                                    x-init="$watch('charRelationshipAuto', val => { charRelationship = val }); charRelationship = charRelationship || charRelationshipAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-blue-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="2">2 — Nasabah Lama</option>
                                                    <option value="1">1 — Nasabah Baru</option>
                                                </select>
                                                <span class="text-xs text-blue-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-indigo-700"
                                                x-text="(parseInt(charRelationship) / 2 * 10).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 4. Stability of Employment (20%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">4. Stabilitas Pekerjaan /
                                                Tempat Tinggal</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan lama bekerja / usaha
                                                (dapat dimuat dari data pekerjaan)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="charStability" name="char_stability"
                                                    x-init="$watch('charStabilityFromYears', val => { charStability = val }); charStability = charStability || charStabilityFromYears;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-blue-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="4">4 — Di atas 6 tahun</option>
                                                    <option value="3">3 — Antara 4 - 6 tahun</option>
                                                    <option value="2">2 — Antara 2 - 4 tahun</option>
                                                    <option value="1">1 — Kurang dari 2 tahun</option>
                                                </select>
                                                <span class="text-xs text-blue-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-indigo-700"
                                                x-text="(parseInt(charStability) / 4 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 5. Reputation & Reference (25%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">5. Reputasi & Referensi</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan wawancara dan survei ke
                                                lingkungan tempat tinggal nasabah</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">25%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="charReputation" name="char_reputation"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Sangat Bagus</option>
                                                <option value="4">4 — Bagus</option>
                                                <option value="3">3 — Cukup Bagus</option>
                                                <option value="2">2 — Kurang Bagus</option>
                                                <option value="1">1 — Buruk</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-indigo-700"
                                                x-text="(parseInt(charReputation) / 5 * 25).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- Total Row -->
                                    <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                        <td class="px-4 py-3 text-sm font-bold text-indigo-900" colspan="2">Total Skor
                                            Character</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                                :class="characterScoreStatusColor" x-text="characterScoreStatus"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-lg font-black text-indigo-700"
                                                x-text="characterTotalScore"></span>
                                            <span class="text-xs text-gray-400 font-bold">/ 100</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hidden input for total score -->
                    <input type="hidden" name="char_total_score" :value="characterTotalScore">

                    <!-- Capacity Scoring Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mt-6">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Penilaian Kapasitas (Capacity)
                            </h3>
                            <p class="text-emerald-200 text-xs mt-0.5">Bobot total: 100%</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">
                                            Komponen</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                            Bobot</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">
                                            Skor Tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">

                                    <!-- 1. Repayment Capacity (40%) -->
                                    <tr class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Repayment Capacity (RPC)
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">Rasio Kewajiban terhadap Pendapatan
                                                Bersih</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">40%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capRpc" name="cap_rpc"
                                                    x-init="$watch('capRpcAuto', val => { capRpc = val }); capRpc = capRpc || capRpcAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-emerald-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (< 30%)</option>
                                                    <option value="4">4 — Baik (30% - 50%)</option>
                                                    <option value="3">3 — Cukup Baik (51% - 69%)</option>
                                                    <option value="2">2 — Kurang Baik (70% - 80%)</option>
                                                    <option value="1">1 — Tidak Baik (> 80%)</option>
                                                </select>
                                                <span class="text-xs text-emerald-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 font-medium">RPC Saat Ini: <span
                                                    class="font-bold text-gray-700" x-text="rpcRatio + '%'"></span></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capRpc) / 5 * 40).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 2. Lama Usaha / Bekerja (20%) - Entrepreneur -->
                                    <tr x-show="isEntrepreneur"
                                        class="hover:bg-emerald-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Lama Usaha / Bekerja</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan profil nasabah</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capLamaUsaha" name="cap_lama_usaha"
                                                    x-init="$watch('capLamaUsahaAuto', val => { capLamaUsaha = val }); capLamaUsaha = capLamaUsaha || capLamaUsahaAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-emerald-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (> 5 Tahun)</option>
                                                    <option value="4">4 — Baik (4 - < 5 Tahun)</option>
                                                    <option value="3">3 — Cukup Baik (3 - < 4 Tahun)</option>
                                                    <option value="2">2 — Kurang Baik (2 - < 3 Tahun)</option>
                                                    <option value="1">1 — Beresiko (< 2 Tahun)</option>
                                                </select>
                                                <span class="text-xs text-emerald-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capLamaUsaha) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 2. Masa Kerja (20%) - Employee -->
                                    <tr x-show="!isEntrepreneur" x-cloak
                                        class="hover:bg-emerald-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Masa Kerja</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Lama bekerja di perusahaan saat ini
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capLamaUsaha" name="cap_lama_usaha"
                                                    x-init="$watch('capMasaKerjaAuto', val => { if (!isEntrepreneur) capLamaUsaha = val }); if (!isEntrepreneur) capLamaUsaha = capLamaUsaha || capMasaKerjaAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-emerald-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (> 5 Tahun)</option>
                                                    <option value="4">4 — Baik (3 - 5 Tahun)</option>
                                                    <option value="3">3 — Cukup Baik (2 - 3 Tahun)</option>
                                                    <option value="2">2 — Kurang Baik (1 - 2 Tahun)</option>
                                                    <option value="1">1 — Beresiko (< 1 Tahun)</option>
                                                </select>
                                                <span class="text-xs text-emerald-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capLamaUsaha) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 3. Usia + Jangka Waktu (20%) -->
                                    <tr class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">3. Usia + Jangka Waktu Kredit
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">Penilaian manual berdasarkan estimasi
                                                usia saat jatuh tempo</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capUsia" name="cap_usia"
                                                    x-init="$watch('capUsiaAuto', val => { capUsia = val }); capUsia = capUsia || capUsiaAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-emerald-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik</option>
                                                    <option value="4">4 — Baik</option>
                                                    <option value="3">3 — Cukup Baik</option>
                                                    <option value="2">2 — Kurang Baik</option>
                                                    <option value="1">1 — Tidak Baik</option>
                                                </select>
                                                <span class="text-xs text-emerald-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>

                                            <!-- Age Warning -->
                                            <div x-show="isAgeRisky" style="display: none;" x-transition
                                                class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-xs flex items-start gap-2">
                                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                                <div class="text-red-700">
                                                    <span class="font-bold block text-red-900">Peringatan:</span>
                                                    Jangka waktu pinjaman melebihi masa kerja produktif peminjam (Usia > 60
                                                    tahun), dan proyeksi pendapatan pensiun tidak cukup untuk menutupi
                                                    hutang.
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capUsia) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- 4. Pengelolaan Usaha (20%) - Entrepreneur -->
                                    <tr x-show="isEntrepreneur"
                                        class="hover:bg-emerald-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">4. Pengelolaan Usaha</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Keterlibatan pihak lain dalam
                                                mengelola usaha</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="capPengelolaan" name="cap_pengelolaan"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Sangat Baik (Melibatkan Pasangan)</option>
                                                <option value="4">4 — Baik (Melibatkan Anak)</option>
                                                <option value="3">3 — Cukup Baik (Melibatkan Keluarga)</option>
                                                <option value="2">2 — Kurang Baik (Melibatkan Orang Kepercayaan)</option>
                                                <option value="1">1 — Tidak Baik (Tidak Melibatkan Siapapun)</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capPengelolaan) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 4. Status Kepegawaian (20%) - Employee -->
                                    <tr x-show="!isEntrepreneur" x-cloak
                                        class="hover:bg-emerald-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">4. Status Kepegawaian</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Jenis instansi tempat bekerja</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capPengelolaan" name="cap_pengelolaan"
                                                    x-init="$watch('capStatusKepegawaianAuto', val => { if (!isEntrepreneur) capPengelolaan = val }); if (!isEntrepreneur) capPengelolaan = capPengelolaan || capStatusKepegawaianAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-emerald-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — PNS</option>
                                                    <option value="4">4 — TNI/Polri</option>
                                                    <option value="3">3 — BUMN</option>
                                                    <option value="2">2 — Swasta</option>
                                                </select>
                                                <span class="text-xs text-emerald-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-emerald-700"
                                                x-text="(parseInt(capPengelolaan) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>

                                    <!-- Total Row -->
                                    <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                                        <td class="px-4 py-3 text-sm font-bold text-emerald-900" colspan="2">Total Skor
                                            Capacity</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                                :class="capacityScoreStatusColor" x-text="capacityScoreStatus"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-lg font-black text-emerald-700"
                                                x-text="capacityTotalScore"></span>
                                            <span class="text-xs text-gray-400 font-bold">/ 100</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hidden input for capacity total score -->
                    <input type="hidden" name="cap_total_score" :value="capacityTotalScore">

                    <!-- Capital Scoring Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mt-6">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Penilaian Modal (Capital)</h3>
                            <p class="text-blue-200 text-xs mt-0.5">Bobot total: 100%</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">
                                            Komponen</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                            Bobot</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">
                                            Skor Tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <!-- 1. Rasio hutang dibandingkan aset (DAR) (40%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Rasio hutang dibandingkan
                                                aset (DAR)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">40%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capitalDar" name="capital_dar"
                                                    x-init="$watch('capitalDarAuto', val => { capitalDar = val }); capitalDar = capitalDar || capitalDarAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-blue-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (< 20%)</option>
                                                    <option value="4">4 — Baik (20% - 30%)</option>
                                                    <option value="3">3 — Cukup Baik (31% - 40%)</option>
                                                    <option value="2">2 — Kurang Baik (41% - 50%)</option>
                                                    <option value="1">1 — Tidak Baik (> 50%)</option>
                                                </select>
                                                <span class="text-xs text-blue-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 font-medium">DAR Saat Ini: <span
                                                    class="font-bold text-gray-700" x-text="darRatio + '%'"></span></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-blue-700"
                                                x-text="(parseInt(capitalDar) / 5 * 40).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 2. Rasio hutang dibandingkan modal (DER) (60%) -->
                                    <tr class="hover:bg-blue-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Rasio hutang dibandingkan
                                                modal (DER)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">60%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="capitalDer" name="capital_der"
                                                    x-init="$watch('capitalDerAuto', val => { capitalDer = val }); capitalDer = capitalDer || capitalDerAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-blue-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (< 100%)</option>
                                                    <option value="4">4 — Baik (100% - 150%)</option>
                                                    <option value="3">3 — Cukup Baik (151% - 200%)</option>
                                                    <option value="2">2 — Kurang Baik (201% - 250%)</option>
                                                    <option value="1">1 — Tidak Baik (> 250%)</option>
                                                </select>
                                                <span class="text-xs text-blue-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 font-medium">DER Saat Ini: <span
                                                    class="font-bold text-gray-700" x-text="derRatio + '%'"></span></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-blue-700"
                                                x-text="(parseInt(capitalDer) / 5 * 60).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- Total Row -->
                                    <tr class="bg-blue-50 border-t-2 border-blue-200">
                                        <td class="px-4 py-3 text-sm font-bold text-blue-900" colspan="2">Total Skor Capital
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                                :class="capitalScoreStatusColor" x-text="capitalScoreStatus"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-lg font-black text-blue-700"
                                                x-text="capitalTotalScore"></span>
                                            <span class="text-xs text-gray-400 font-bold">/ 100</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="capital_total_score" :value="capitalTotalScore">

                    <!-- Condition Scoring Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mt-6">
                        <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Kondisi Ekonomi (Condition of
                                Economic)</h3>
                            <p class="text-purple-200 text-xs mt-0.5">Bobot total: 100%</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">
                                            Komponen</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                            Bobot</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">
                                            Skor Tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <!-- 1. Lokasi usaha (20%) - Entrepreneur -->
                                    <tr x-show="isEntrepreneur" class="hover:bg-purple-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Lokasi Usaha</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="condLokasi" name="cond_lokasi"
                                                    x-init="$watch('condLokasiAuto', val => { condLokasi = val }); condLokasi = condLokasi || condLokasiAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm bg-purple-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Strategis</option>
                                                    <option value="4">4 — Strategis</option>
                                                    <option value="3">3 — Cukup Strategis</option>
                                                    <option value="2">2 — Kurang Strategis</option>
                                                    <option value="1">1 — Tidak Strategis</option>
                                                </select>
                                                <span class="text-xs text-purple-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-purple-700"
                                                x-text="(parseInt(condLokasi) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 1. Stabilitas Penghasilan (20%) - Employee -->
                                    <tr x-show="!isEntrepreneur" x-cloak class="hover:bg-purple-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Stabilitas Penghasilan</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan frekuensi gaji</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="condLokasi" name="cond_lokasi"
                                                    x-init="$watch('condStabilitasAuto', val => { if (!isEntrepreneur) condLokasi = val }); if (!isEntrepreneur) condLokasi = condLokasi || condStabilitasAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm bg-purple-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Bulanan (Paling Stabil)</option>
                                                    <option value="4">4 — Mingguan</option>
                                                    <option value="3">3 — Harian</option>
                                                    <option value="2">2 — Borongan</option>
                                                    <option value="1">1 — Tidak Tetap</option>
                                                </select>
                                                <span class="text-xs text-purple-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-purple-700"
                                                x-text="(parseInt(condLokasi) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 2. Rasio Laba Kotor (20%) - Entrepreneur -->
                                    <tr x-show="isEntrepreneur"
                                        class="hover:bg-purple-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Rasio Laba Kotor</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Profit Margin Atas Penjualan</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="condProfit" name="cond_profit"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Sangat Baik (> 30%)</option>
                                                <option value="4">4 — Baik (20% - 30%)</option>
                                                <option value="3">3 — Cukup Baik (10% - 19%)</option>
                                                <option value="2">2 — Kurang Baik (5% - 9%)</option>
                                                <option value="1">1 — Tidak Baik (< 5%)</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-purple-700"
                                                x-text="(parseInt(condProfit) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 2. Jaminan Penghasilan (20%) - Employee -->
                                    <tr x-show="!isEntrepreneur" x-cloak
                                        class="hover:bg-purple-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Jaminan Penghasilan</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Berdasarkan jenis instansi
                                                (pensiun/BPJS)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="condProfit" name="cond_profit"
                                                    x-init="$watch('condJaminanAuto', val => { if (!isEntrepreneur) condProfit = val }); if (!isEntrepreneur) condProfit = condProfit || condJaminanAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm bg-purple-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — PNS (Pensiun Terjamin)</option>
                                                    <option value="4">4 — TNI/Polri (Pensiun Terjamin)</option>
                                                    <option value="3">3 — BUMN (Tunjangan Baik)</option>
                                                    <option value="2">2 — Swasta (Tergantung Perusahaan)</option>
                                                </select>
                                                <span class="text-xs text-purple-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-purple-700"
                                                x-text="(parseInt(condProfit) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 3. Rasio DSCR (60%) -->
                                    <tr class="hover:bg-purple-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">3. Rasio Debt Service Coverage
                                                (DSCR)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">60%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="condDscr" name="cond_dscr"
                                                    x-init="$watch('condDscrAuto', val => { condDscr = val }); condDscr = condDscr || condDscrAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm bg-purple-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (> 1.5x)</option>
                                                    <option value="4">4 — Baik (1.2x - 1.5x)</option>
                                                    <option value="3">3 — Cukup Baik (1.0x - 1.1x)</option>
                                                    <option value="2">2 — Kurang Baik (0.8x - 0.9x)</option>
                                                    <option value="1">1 — Tidak Baik (< 0.8x)</option>
                                                </select>
                                                <span class="text-xs text-purple-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 font-medium">DSR Saat Ini: <span
                                                    class="font-bold text-gray-700" x-text="dsrRatio + '%'"></span></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-purple-700"
                                                x-text="(parseInt(condDscr) / 5 * 60).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- Total Row -->
                                    <tr class="bg-purple-50 border-t-2 border-purple-200">
                                        <td class="px-4 py-3 text-sm font-bold text-purple-900" colspan="2">Total Skor
                                            Condition</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                                :class="conditionScoreStatusColor" x-text="conditionScoreStatus"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-lg font-black text-purple-700"
                                                x-text="conditionTotalScore"></span>
                                            <span class="text-xs text-gray-400 font-bold">/ 100</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="condition_total_score" :value="conditionTotalScore">

                    <!-- Collateral Scoring Table -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mt-6">
                        <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-3">
                            <h3 class="text-white font-bold text-sm uppercase tracking-wider">Agunan (Collateral)</h3>
                            <p class="text-orange-100 text-xs mt-0.5">Bobot total: 100%</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">
                                            Komponen</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                            Bobot</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Nilai</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">
                                            Skor Tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <!-- 1. Kepemilikan Agunan (20%) -->
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">1. Kepemilikan Agunan</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="colKepemilikan" name="col_kepemilikan"
                                                    x-init="$watch('colKepemilikanAuto', val => { if (val !== null) colKepemilikan = val }); if (colKepemilikanAuto !== null) colKepemilikan = colKepemilikan || colKepemilikanAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm bg-orange-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Milik Sendiri / Pasangan</option>
                                                    <option value="4">4 — Milik Orang Tua / Anak</option>
                                                    <option value="3">3 — Milik Kakak / Adik Kandung</option>
                                                    <option value="2">2 — Milik Keluarga Jauh</option>
                                                    <option value="1">1 — Milik Orang Lain Tercatat</option>
                                                </select>
                                                <span x-show="colKepemilikanAuto !== null"
                                                    class="text-xs text-orange-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-orange-700"
                                                x-text="(parseInt(colKepemilikan) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 2. Peruntukan (10%) -->
                                    <tr class="hover:bg-orange-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">2. Peruntukan</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">10%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="colPeruntukan" name="col_peruntukan"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Tempat Usaha Aktif & Rumah Tinggal</option>
                                                <option value="4">4 — Tempat Usaha / Ruko</option>
                                                <option value="3">3 — Rumah Tinggal Aktif</option>
                                                <option value="2">2 — Tanah Kosong Produktif</option>
                                                <option value="1">1 — Tanah Kosong Non-Produktif</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-orange-700"
                                                x-text="(parseInt(colPeruntukan) / 5 * 10).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 3. Lebar jalan (20%) -->
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">3. Lebar Jalan Depan Agunan
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="colLebarJalan" name="col_lebar_jalan"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Jalan Nasional / Raya Lebar (Bisa 2 Truk)</option>
                                                <option value="4">4 — Jalan Aspal/Paving Lebar (Bisa 2 Mobil)</option>
                                                <option value="3">3 — Jalan Aspal/Paving Sedang (1 Mobil)</option>
                                                <option value="2">2 — Gang Lebar (Tidak bisa masuk mobil)</option>
                                                <option value="1">1 — Gang Sempit / Susah Akses</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-orange-700"
                                                x-text="(parseInt(colLebarJalan) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 4. Nilai Agunan (Collateral Coverage) (30%) -->
                                    <tr class="hover:bg-orange-50/30 transition-colors bg-gray-50/50">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">4. Nilai Agunan (Collateral
                                                Coverage)</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">30%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <select x-model="colCoverage" name="col_coverage"
                                                    x-init="$watch('colCoverageAuto', val => { colCoverage = val }); colCoverage = colCoverage || colCoverageAuto;"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm bg-orange-50/50">
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="5">5 — Sangat Baik (> 150%)</option>
                                                    <option value="4">4 — Baik (130% - 149%)</option>
                                                    <option value="3">3 — Cukup Baik (110% - 129%)</option>
                                                    <option value="2">2 — Kurang Baik (100% - 109%)</option>
                                                    <option value="1">1 — Tidak Baik (< 100%)</option>
                                                </select>
                                                <span class="text-xs text-orange-600 font-medium whitespace-nowrap">⚡
                                                    Otomatis</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 font-medium">Coverage: <span
                                                    class="font-bold text-gray-700"
                                                    x-text="(() => { const loan = parseFloat(String(loanAmount).replace(/\D/g, '')) || 0; return loan > 0 ? ((totalCollateralBankValue / loan) * 100).toFixed(2) + '%' : '-'; })()"></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-orange-700"
                                                x-text="(parseInt(colCoverage) / 5 * 30).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- 5. Marketable (20%) -->
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">5. Marketable?</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Kemudahan untuk dijual</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">20%</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select x-model="colMarketable" name="col_marketable"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih --</option>
                                                <option value="5">5 — Sangat Mudah Dijual (Lokasi Sangat Strategis/Premium)
                                                </option>
                                                <option value="4">4 — Mudah Dijual</option>
                                                <option value="3">3 — Cukup Mudah Dijual</option>
                                                <option value="2">2 — Agak Sulit Dijual</option>
                                                <option value="1">1 — Sangat Sulit Dijual</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm font-bold text-orange-700"
                                                x-text="(parseInt(colMarketable) / 5 * 20).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                    <!-- Total Row -->
                                    <tr class="bg-orange-50 border-t-2 border-orange-200">
                                        <td class="px-4 py-3 text-sm font-bold text-orange-900" colspan="2">Total Skor
                                            Collateral</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                                :class="collateralScoreStatusColor" x-text="collateralScoreStatus"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-lg font-black text-orange-700"
                                                x-text="collateralTotalScore"></span>
                                            <span class="text-xs text-gray-400 font-bold">/ 100</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="col_total_score" :value="collateralTotalScore">
                    <input type="hidden" name="final_score" :value="finalScore">

                    <!-- 5C Scoring Summary -->
                    <div class="bg-white rounded-xl border-2 border-gray-300 overflow-hidden shadow-lg mt-8">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
                            <h3 class="text-white font-bold text-base uppercase tracking-wider">A. Hasil Penilaian dan
                                Analisis Kredit</h3>
                            <p class="text-gray-400 text-xs mt-0.5">Ringkasan skor 5C dengan bobot kategori</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-amber-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase w-8">#</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Indikator
                                            Penilaian</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase w-16">
                                            Bobot</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase w-16">
                                            Nilai</th>
                                        <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase w-16">
                                            Skala</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <!-- 1. Character -->
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-3 py-2">1</td>
                                        <td class="px-3 py-2">Character (Watak)</td>
                                        <td class="px-3 py-2 text-center" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">1.1</td>
                                        <td class="px-3 py-1.5 pl-6">SLIK</td>
                                        <td class="px-3 py-1.5 text-center">25%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="charCreditBureau"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(charCreditBureau) / 5 * 25 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">1.2</td>
                                        <td class="px-3 py-1.5 pl-6">Keterbukaan</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="charInfoConsistency"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(charInfoConsistency) / 4 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">1.3</td>
                                        <td class="px-3 py-1.5 pl-6">Lama menjadi Nasabah</td>
                                        <td class="px-3 py-1.5 text-center">10%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="charRelationship"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(charRelationship) / 2 * 10 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">1.4</td>
                                        <td class="px-3 py-1.5 pl-6">Keluarga</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="charStability"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(charStability) / 4 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">1.5</td>
                                        <td class="px-3 py-1.5 pl-6">Penjelasan Calon Debitur atas Penggunaan Kredit</td>
                                        <td class="px-3 py-1.5 text-center">25%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="charReputation"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(charReputation) / 5 * 25 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr class="bg-indigo-50 font-bold border-t">
                                        <td class="px-3 py-2" colspan="2"><em>Nilai Character (Watak)</em></td>
                                        <td class="px-3 py-2 text-center">30%</td>
                                        <td class="px-3 py-2 text-center" x-text="characterNilai"></td>
                                        <td class="px-3 py-2 text-center" x-text="characterSkala"></td>
                                    </tr>

                                    <!-- 2. Capacity -->
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-3 py-2">2</td>
                                        <td class="px-3 py-2">Capacity (Kemampuan)</td>
                                        <td class="px-3 py-2 text-center" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">2.1</td>
                                        <td class="px-3 py-1.5 pl-6">Repayment Capacity (RPC)</td>
                                        <td class="px-3 py-1.5 text-center">40%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capRpc"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capRpc) / 5 * 40 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">2.2</td>
                                        <td class="px-3 py-1.5 pl-6">Lama usaha / bekerja</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capLamaUsaha"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capLamaUsaha) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">2.3</td>
                                        <td class="px-3 py-1.5 pl-6">Rekening simpanan</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capUsia"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capUsia) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">2.4</td>
                                        <td class="px-3 py-1.5 pl-6">Pengelolaan Usaha (Keterlibatan Keluarga)</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capPengelolaan"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capPengelolaan) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr class="bg-emerald-50 font-bold border-t">
                                        <td class="px-3 py-2" colspan="2"><em>Nilai Capacity (Kemampuan)</em></td>
                                        <td class="px-3 py-2 text-center">20%</td>
                                        <td class="px-3 py-2 text-center" x-text="capacityNilai"></td>
                                        <td class="px-3 py-2 text-center" x-text="capacitySkala"></td>
                                    </tr>

                                    <!-- 3. Capital -->
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-3 py-2">3</td>
                                        <td class="px-3 py-2">Capital (Modal)</td>
                                        <td class="px-3 py-2 text-center" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">3.1</td>
                                        <td class="px-3 py-1.5 pl-6">Rasio hutang dibandingkan aset (DAR)</td>
                                        <td class="px-3 py-1.5 text-center">40%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capitalDar"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capitalDar) / 5 * 40 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">3.2</td>
                                        <td class="px-3 py-1.5 pl-6">Rasio hutang dibandingkan modal (DER)</td>
                                        <td class="px-3 py-1.5 text-center">60%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="capitalDer"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(capitalDer) / 5 * 60 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr class="bg-blue-50 font-bold border-t">
                                        <td class="px-3 py-2" colspan="2"><em>Nilai Capital (Modal)</em></td>
                                        <td class="px-3 py-2 text-center">20%</td>
                                        <td class="px-3 py-2 text-center" x-text="capitalNilai"></td>
                                        <td class="px-3 py-2 text-center" x-text="capitalSkala"></td>
                                    </tr>

                                    <!-- 4. Condition -->
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-3 py-2">4</td>
                                        <td class="px-3 py-2">Condition of Economic (Prospek Usaha Debitur)</td>
                                        <td class="px-3 py-2 text-center" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">4.1</td>
                                        <td class="px-3 py-1.5 pl-6">Lokasi usaha</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="condLokasi"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(condLokasi) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">4.2</td>
                                        <td class="px-3 py-1.5 pl-6">Rasio Laba Kotor (Profit Margin atas Penjualan)</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="condProfit"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(condProfit) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">4.3</td>
                                        <td class="px-3 py-1.5 pl-6">Rasio Debt Service Coverage (DSCR)</td>
                                        <td class="px-3 py-1.5 text-center">60%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="condDscr"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(condDscr) / 5 * 60 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr class="bg-purple-50 font-bold border-t">
                                        <td class="px-3 py-2" colspan="2"><em>Nilai Condition of Economic (Prospek Usaha
                                                Debitur)</em></td>
                                        <td class="px-3 py-2 text-center">10%</td>
                                        <td class="px-3 py-2 text-center" x-text="conditionNilai"></td>
                                        <td class="px-3 py-2 text-center" x-text="conditionSkala"></td>
                                    </tr>

                                    <!-- 5. Collateral -->
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-3 py-2">5</td>
                                        <td class="px-3 py-2">Collateral (Agunan)</td>
                                        <td class="px-3 py-2 text-center" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">5.1</td>
                                        <td class="px-3 py-1.5 pl-6">Kepemilikan Agunan</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="colKepemilikan"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(colKepemilikan) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">5.2</td>
                                        <td class="px-3 py-1.5 pl-6">Peruntukan</td>
                                        <td class="px-3 py-1.5 text-center">10%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="colPeruntukan"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(colPeruntukan) / 5 * 10 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">5.3</td>
                                        <td class="px-3 py-1.5 pl-6">Lebar jalan</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="colLebarJalan"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(colLebarJalan) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">5.4</td>
                                        <td class="px-3 py-1.5 pl-6">Nilai Agunan (Collateral Coverage)</td>
                                        <td class="px-3 py-1.5 text-center">30%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="colCoverage"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(colCoverage) / 5 * 30 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1.5 text-gray-400">5.5</td>
                                        <td class="px-3 py-1.5 pl-6">Marketable?</td>
                                        <td class="px-3 py-1.5 text-center">20%</td>
                                        <td class="px-3 py-1.5 text-center" x-text="colMarketable"></td>
                                        <td class="px-3 py-1.5 text-center"
                                            x-text="(parseInt(colMarketable) / 5 * 20 / 100 * 5).toFixed(1)"></td>
                                    </tr>
                                    <tr class="bg-orange-50 font-bold border-t">
                                        <td class="px-3 py-2" colspan="2"><em>Nilai Collateral (Agunan)</em></td>
                                        <td class="px-3 py-2 text-center">20%</td>
                                        <td class="px-3 py-2 text-center" x-text="collateralNilai"></td>
                                        <td class="px-3 py-2 text-center" x-text="collateralSkala"></td>
                                    </tr>

                                    <!-- Kesimpulan Nilai -->
                                    <tr class="bg-amber-100 border-t-2 border-gray-400">
                                        <td class="px-3 py-3 font-black text-base" colspan="3">Kesimpulan Nilai</td>
                                        <td class="px-3 py-3 text-center font-black text-lg" colspan="2">
                                            <span x-text="finalScore"></span>
                                            <br>
                                            <span class="text-sm px-3 py-1 rounded-full border-2 inline-block mt-1"
                                                :class="finalScoreColor" x-text="'(' + finalScoreKelayakan + ')'"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Rentang Skala Nilai Kelayakan -->
                        <div class="p-4 bg-gray-50 border-t">
                            <h4 class="font-bold text-sm mb-2">Rentang Skala Nilai Kelayakan</h4>
                            <table class="text-xs border border-gray-300 rounded-lg overflow-hidden">
                                <thead class="bg-amber-200">
                                    <tr>
                                        <th class="px-3 py-1.5 text-left">#</th>
                                        <th class="px-3 py-1.5 text-left">Kelayakan</th>
                                        <th class="px-3 py-1.5 text-left">Rentang Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-1">1</td>
                                        <td class="px-3 py-1 text-green-700 font-bold">Sangat Layak</td>
                                        <td class="px-3 py-1">4.61 s/d 5</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1">2</td>
                                        <td class="px-3 py-1 text-blue-700 font-bold">Layak</td>
                                        <td class="px-3 py-1">3.6 s/d 4.6</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1">3</td>
                                        <td class="px-3 py-1 text-yellow-700 font-bold">Cukup Layak</td>
                                        <td class="px-3 py-1">2.81 s/d 3.6</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1">4</td>
                                        <td class="px-3 py-1 text-orange-700 font-bold">Kurang Layak</td>
                                        <td class="px-3 py-1">1.81 s/d 2.8</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-1">5</td>
                                        <td class="px-3 py-1 text-red-700 font-bold">Tidak Layak</td>
                                        <td class="px-3 py-1">0 s/d 1.8</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <div class="flex justify-between pt-4">
                        <button type="button" @click="currentStep = 5"
                            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 shadow-sm">Kembali</button>
                        <button type="button" @click="currentStep = 7"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none shadow-md hover:shadow-lg transition-all">Selanjutnya</button>
                    </div>
                </div>

                <!-- Step 7: Kesimpulan (Checklist) -->
                <div x-show="currentStep === 7" style="display: none;" class="space-y-6">
                    <!-- Header -->
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4 mb-6">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        Bagian 7 : Kesimpulan & Syarat Dokumen
                    </h2>

                    <!-- Nama Penjamin Card -->
                    <div class="bg-blue-50 rounded-xl p-5 border border-blue-200 mb-6">
                        <h3 class="text-lg font-bold text-blue-800 mb-1">Nama Penjamin</h3>
                        <p class="text-sm text-blue-700 mb-4">Daftar penjamin yang akan menandatangani dokumen evaluasi.</p>

                        <div class="overflow-x-auto rounded-lg shadow-sm border border-blue-200 mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200">
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 sm:pl-6">
                                            Nama
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                            Hubungan dengan Debitur
                                        </th>
                                        <th scope="col" class="px-3 py-3 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template x-for="(guarantor, index) in guarantorsList" :key="index">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="whitespace-nowrap py-2 pl-4 pr-3 sm:pl-6">
                                                <input type="text" x-model="guarantor.name"
                                                    :name="'guarantors['+index+'][name]'"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Nama Penjamin">
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-2">
                                                <input type="text" x-model="guarantor.relationship"
                                                    :name="'guarantors['+index+'][relationship]'"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Contoh: Suami / Istri / Orang Tua">
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-2 text-right">
                                                <button type="button" @click="removeGuarantor(index)"
                                                    class="text-red-500 hover:text-red-700 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                                <button type="button" @click="addGuarantor()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Penjamin
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Input for Document Checklist -->
                    <input type="hidden" name="document_checklist" x-bind:value="JSON.stringify(checkedDocuments)">

                    <!-- Checklist Card -->
                    <div class="bg-orange-50 rounded-xl p-5 border border-orange-200">
                        <h3 class="text-lg font-bold text-orange-800 mb-1">Checklist Persyaratan Dokumen</h3>
                        <p class="text-sm text-orange-700 mb-4">Pastikan dokumen-dokumen berikut telah lengkap sebelum Anda
                            menyimpan dan memproses evaluasi ini.</p>

                        <div class="bg-white rounded-lg border border-orange-200 overflow-hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x border-b border-gray-100 last:border-0"
                                style="display: grid; grid-auto-rows: min-content;">
                                <template x-for="(doc, index) in documentChecklist" :key="index">
                                    <label
                                        class="flex items-center space-x-3 cursor-pointer py-3 px-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" :value="doc" x-model="checkedDocuments"
                                                class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-800 font-medium select-none" x-text="doc"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            <template x-if="documentChecklist.length === 0">
                                <p class="text-gray-500 text-sm italic text-center py-4">Memuat persyaratan dokumen...</p>
                            </template>
                        </div>
                    </div>

                    <!-- Navigation and Submit -->
                    <div class="flex justify-between mt-8 border-t pt-6 bg-gray-50 -mx-6 px-6 pb-6">
                        <button type="button" @click="currentStep = 6"
                            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 shadow-sm">Kembali</button>
                        <button type="button" @click="confirmSubmit"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-bold rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none transition-colors shadow-lg hover:shadow-green-500/30">SIMPAN
                            & PROSES EVALUASI</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Correctly Placed Modal (Sibling to the blurred card) -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="if(selectedCustomer) showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full relative z-50">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Pilih Nasabah
                                </h3>
                                <div>
                                    <div class="relative mb-4">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" x-model="search"
                                            class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari berdasarkan Nama / NIK / Alamat...">
                                    </div>

                                    <div class="overflow-y-auto max-h-96 border rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50 sticky top-0">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Nama</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        NIK</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Alamat</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        AO</th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Pilih</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="customer in paginatedCustomers" :key="customer.id">
                                                    <tr class="hover:bg-blue-50 cursor-pointer transition-colors"
                                                        @click="selectCustomer(customer)">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                            x-text="customer.name"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="customer.identity_number ?? '-'"></td>
                                                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs"
                                                            x-text="customer.address ?? '-'"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="customer.user?.code ?? '-'"></td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button type="button"
                                                                class="text-blue-600 hover:text-blue-900 font-bold hover:underline">PILIH</button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="filteredCustomers.length === 0">
                                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                                        Tidak ada data nasabah ditemukan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination Controls -->
                                    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
                                        x-show="filteredCustomers.length > 0">
                                        <div class="flex flex-1 justify-between sm:hidden">
                                            <button @click="prevPage" :disabled="currentPage === 1"
                                                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">Previous</button>
                                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">Next</button>
                                        </div>
                                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-sm text-gray-700">
                                                    Showing
                                                    <span class="font-medium"
                                                        x-text="(currentPage - 1) * itemsPerPage + 1"></span>
                                                    to
                                                    <span class="font-medium"
                                                        x-text="Math.min(currentPage * itemsPerPage, filteredCustomers.length)"></span>
                                                    of
                                                    <span class="font-medium" x-text="filteredCustomers.length"></span>
                                                    results
                                                </p>
                                            </div>
                                            <div>
                                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                                    aria-label="Pagination">
                                                    <button @click="prevPage" :disabled="currentPage === 1"
                                                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                                        <span class="sr-only">Previous</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                            aria-hidden="true">
                                                            <path fill-rule="evenodd"
                                                                d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <button @click="nextPage" :disabled="currentPage === totalPages"
                                                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                                        <span class="sr-only">Next</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                            aria-hidden="true">
                                                            <path fill-rule="evenodd"
                                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="if(selectedCustomer) showModal = false" :disabled="!selectedCustomer"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Tutup / Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Collateral Map Modal -->
    <div x-show="showCollateralMapModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                @click="showCollateralMapModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-[70]">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pilih Lokasi Agunan
                            </h3>

                            <div class="mb-4">
                                <button type="button" @click="getLocationForCollateral()"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Ambil Lokasi Saat Ini (GPS)
                                </button>
                                <span class="text-xs text-gray-500 ml-2">atau klik pada peta untuk menandai lokasi.</span>
                            </div>

                            <div id="collateral-modal-map"
                                class="h-[500px] w-full rounded-lg border border-gray-300 shadow-inner"></div>

                            <p class="mt-2 text-xs text-gray-500 italic">* Geser marker untuk menyesuaikan posisi lokasi
                                agunan.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" @click="saveCollateralLocation()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Lokasi
                    </button>
                    <button type="button" @click="showCollateralMapModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper Modal -->
    <div id="cropper-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col max-h-[90vh]">
                    <div
                        class="sticky top-0 z-50 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">
                            Crop Photo (Legalitas / Detail Usaha)
                        </h3>
                        <div class="flex space-x-3">
                            <button type="button" id="cancel-crop-btn"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Batal
                            </button>
                            <button type="button" id="crop-confirm-btn"
                                class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 flex-grow overflow-y-auto flex items-center justify-center">
                        <div class="relative w-full" style="height: 500px; max-height: 60vh;">
                            <img id="cropper-image" src="" alt="Crop Preview"
                                class="block max-w-full h-full object-contain mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
        <!-- Scripts for Map & Cropper -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Collateral Map Logic (Restored & Enhanced) ---
                window.collateralMaps = {}; // Store map instances
                window.collateralMarkers = {}; // Store marker instances

                window.initCollateralMap = function (elementId, index) {
                    const mapEl = document.getElementById(elementId);
                    if (!mapEl) return;

                    // Default location (Indonesia Center or Puri)
                    let defaultLat = -7.4704747;
                    let defaultLng = 112.4401329;

                    // Try to get existing values from Alpine data if possible
                    // We can access the input values by ID or Name
                    const latInput = document.querySelector(`input[name="collaterals[${index}][latitude]"]`);
                    const lngInput = document.querySelector(`input[name="collaterals[${index}][longitude]"]`);

                    if (latInput && latInput.value && lngInput && lngInput.value) {
                        defaultLat = parseFloat(latInput.value);
                        defaultLng = parseFloat(lngInput.value);
                    }

                    // Check if map already initialized
                    if (mapEl._leaflet_id) {
                        // If already exists, just update view
                        if (window.collateralMaps[index]) {
                            window.collateralMaps[index].invalidateSize();
                            window.collateralMaps[index].setView([defaultLat, defaultLng], 13);
                        }
                        return;
                    }

                    const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        crossOrigin: true
                    });

                    const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 19,
                        attribution: 'Tiles &copy; Esri'
                    });

                    const map = L.map(elementId, {
                        center: [defaultLat, defaultLng],
                        zoom: 13,
                        layers: [osm]
                    });

                    const baseMaps = {
                        "OpenStreetMap": osm,
                        "Google Streets": googleStreets,
                        "Google Satellite": googleHybrid,
                        "Esri Satellite": esriSatellite
                    };

                    L.control.layers(baseMaps).addTo(map);

                    // Add Search Control
                    if (L.Control.Geocoder) {
                        L.Control.geocoder({
                            defaultMarkGeocode: false
                        })
                            .on('markgeocode', function (e) {
                                const latlng = e.geocode.center;
                                setCollateralLocation(index, latlng.lat, latlng.lng, map);
                                map.fitBounds(e.geocode.bbox);
                            })
                            .addTo(map);
                    }

                    let marker = null;
                    if (window.collateralMarkers[index]) {
                        marker = window.collateralMarkers[index];
                        marker.addTo(map);
                    } else if (latInput && latInput.value) {
                        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                        window.collateralMarkers[index] = marker;
                    }

                    map.on('click', function (e) {
                        setCollateralLocation(index, e.latlng.lat, e.latlng.lng, map);
                    });

                    // Allow marker drag updates
                    if (marker) {
                        marker.on('dragend', function (e) {
                            const latlng = e.target.getLatLng();
                            setCollateralLocation(index, latlng.lat, latlng.lng, map, false); // false to avoid re-setting marker
                        });
                    }

                    // helper to update marker and inputs
                    function setCollateralLocation(idx, lat, lng, mapInstance, updateMarker = true) {
                        if (updateMarker) {
                            if (window.collateralMarkers[idx]) {
                                window.collateralMarkers[idx].setLatLng([lat, lng]);
                            } else {
                                window.collateralMarkers[idx] = L.marker([lat, lng], { draggable: true }).addTo(mapInstance);
                                // Add drag listener to new marker
                                window.collateralMarkers[idx].on('dragend', function (e) {
                                    const pos = e.target.getLatLng();
                                    setCollateralLocation(idx, pos.lat, pos.lng, mapInstance, false);
                                });
                            }
                        }

                        // Dispatch event to update Alpine data
                        const latIn = document.querySelector(`input[name="collaterals[${idx}][latitude]"]`);
                        const lngIn = document.querySelector(`input[name="collaterals[${idx}][longitude]"]`);

                        if (latIn) { latIn.value = lat.toFixed(8); latIn.dispatchEvent(new Event('input')); }
                        if (lngIn) { lngIn.value = lng.toFixed(8); lngIn.dispatchEvent(new Event('input')); }

                        // Update distance alert for this collateral
                        updateCollateralDistanceAlert(idx, lat, lng);

                        // Fetch Address
                        window.fetchAddressForModal(lat, lng, 'return_object').then(addr => {
                            if (addr) {
                                const villageIn = document.querySelector(`input[name="collaterals[${idx}][village]"]`);
                                const districtIn = document.querySelector(`input[name="collaterals[${idx}][district]"]`);
                                const regencyIn = document.querySelector(`input[name="collaterals[${idx}][regency]"]`);
                                const provinceIn = document.querySelector(`input[name="collaterals[${idx}][province]"]`);
                                const addrIn = document.querySelector(`textarea[name="collaterals[${idx}][location_address]"]`);

                                if (villageIn) { villageIn.value = addr.village; villageIn.dispatchEvent(new Event('input', { bubbles: true })); }
                                if (districtIn) { districtIn.value = addr.district; districtIn.dispatchEvent(new Event('input', { bubbles: true })); }
                                if (regencyIn) { regencyIn.value = addr.regency; regencyIn.dispatchEvent(new Event('input', { bubbles: true })); }
                                if (provinceIn) { provinceIn.value = addr.province; provinceIn.dispatchEvent(new Event('input', { bubbles: true })); }
                                if (addrIn) { addrIn.value = addr.location_address; addrIn.dispatchEvent(new Event('input', { bubbles: true })); }

                                // Force update via Alpine directly
                                const mapContainer = document.getElementById('collateral-map-' + idx);
                                if (mapContainer && window.Alpine) {
                                    const alpineData = Alpine.$data(mapContainer);
                                    if (alpineData && alpineData.collaterals && alpineData.collaterals[idx]) {
                                        alpineData.collaterals[idx].village = addr.village;
                                        alpineData.collaterals[idx].district = addr.district;
                                        alpineData.collaterals[idx].regency = addr.regency;
                                        alpineData.collaterals[idx].province = addr.province;
                                        alpineData.collaterals[idx].location_address = addr.location_address;
                                        // Force reactivity by shallow copy
                                        alpineData.collaterals[idx] = { ...alpineData.collaterals[idx] };
                                    }
                                }
                            }
                        });
                    }

                    // Store setCollateralLocation for external use
                    window.setCollateralLocationGlobal = setCollateralLocation;

                    // Store map instance
                    window.collateralMaps[index] = map;

                    // IntersectionObserver to invalidateSize when the map becomes visible
                    // (fixes tiles not rendering when map is inside a hidden step)
                    const mapObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                setTimeout(() => {
                                    map.invalidateSize();
                                }, 200);
                            }
                        });
                    }, { threshold: 0.1 });
                    mapObserver.observe(mapEl);

                    // Calculate initial distance if lat/lng already set
                    if (latInput && latInput.value && lngInput && lngInput.value) {
                        updateCollateralDistanceAlert(index, defaultLat, defaultLng);
                    }
                };

                window.getCollateralLocation = function (index) {
                    if (navigator.geolocation) {
                        const btn = document.getElementById('btn-loc-' + index);
                        const originalText = btn ? btn.innerHTML : 'Baca Lokasi';
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Membaca...';
                        }

                        navigator.geolocation.getCurrentPosition(function (position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            const map = window.collateralMaps[index];
                            if (map && window.setCollateralLocationGlobal) {
                                window.setCollateralLocationGlobal(index, lat, lng, map, true);
                                map.setView([lat, lng], 16);
                            }

                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = originalText;
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Ditemukan',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }, function (error) {
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = originalText;
                            }
                            Swal.fire('Error', 'Gagal mendeteksi lokasi: ' + error.message, 'error');
                        });
                    } else {
                        Swal.fire('Error', 'Browser anda tidak mendukung Geolocation.', 'error');
                    }
                };

                window.captureCollateralMap = function (index) {
                    const mapElement = document.getElementById('collateral-map-' + index);
                    if (!mapElement) return;

                    Swal.fire({
                        title: 'Mengambil Foto Lokasi...',
                        text: 'Mohon tunggu...',
                        showConfirmButton: false, allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Ensure map is ready
                    if (window.collateralMaps[index]) {
                        window.collateralMaps[index].invalidateSize();
                    }

                    // Short delay to ensure map renders
                    setTimeout(() => {
                        // Configuration with onclone to fix 'oklch' error and prevent 0x0 size
                        const rect = mapElement.getBoundingClientRect();

                        html2canvas(mapElement, {
                            useCORS: true,
                            allowTaint: false,
                            ignoreElements: (el) => el.classList.contains('leaflet-control-zoom') || el.classList.contains('leaflet-control-container') || el.classList.contains('leaflet-control-geocoder'),
                            scale: 1,
                            backgroundColor: '#ffffff',
                            logging: false,
                            onclone: (clonedDoc) => {
                                // 1. Remove stylesheets that contain modern CSS (Tailwind v4 uses oklch)
                                // keep Leaflet CSS for the map to look right.
                                const links = Array.from(clonedDoc.getElementsByTagName('link'));
                                links.forEach(link => {
                                    const href = link.href || '';
                                    if ((href.includes('app') || href.includes('resources') || href.includes('vite')) && !href.includes('leaflet')) {
                                        link.remove();
                                    }
                                });

                                // 2. Remove style tags with oklch
                                const styles = Array.from(clonedDoc.getElementsByTagName('style'));
                                styles.forEach(style => {
                                    if (style.innerHTML.includes('oklch')) {
                                        style.remove();
                                    }
                                });

                                // 3. FORCE DIMENSIONS on the cloned map element
                                // Since we removed Tailwind classes (h-64/w-full), the map might collapse to 0 height.
                                // We must explicitly set width/height based on original computations.
                                const clonedMap = clonedDoc.getElementById('collateral-map-' + index);
                                if (clonedMap) {
                                    clonedMap.style.width = rect.width + 'px';
                                    clonedMap.style.height = rect.height + 'px';
                                    clonedMap.style.position = 'relative'; // Ensure it flows or stays put
                                    clonedMap.style.display = 'block';
                                }
                            }
                        }).then(canvas => {
                            canvas.toBlob(function (blob) {
                                if (!blob) {
                                    Swal.fire('Error', 'Gagal membuat gambar (Blob kosong).', 'error');
                                    return;
                                }

                                // Determine Target Input based on type (Certificate vs Vehicle)
                                // Certificate: Index 3 ('Foto Lokasi') -> col_img_{index}_3
                                // Vehicle: Index 3 ('Foto Lokasi BPKB') -> col_veh_img_{index}_3

                                // Determine Target Input based on actual selected type
                                const typeSelect = document.querySelector(`select[name="collaterals[${index}][type]"]`);
                                const colType = typeSelect ? typeSelect.value : 'certificate';

                                let fileInputId = `col_img_${index}_3`;
                                let previewId = `col-preview-${index}-3`;
                                let placeholderId = `col-placeholder-${index}-3`;

                                if (colType === 'vehicle') {
                                    fileInputId = `col_veh_img_${index}_3`;
                                    previewId = `col-veh-preview-${index}-3`;
                                    placeholderId = `col-veh-placeholder-${index}-3`;
                                }

                                const fileInput = document.getElementById(fileInputId);
                                const previewImg = document.getElementById(previewId);
                                const placeholder = document.getElementById(placeholderId);

                                if (fileInput) {
                                    const file = new File([blob], `map_snapshot_${index}.jpg`, { type: 'image/jpeg' });
                                    const container = new DataTransfer();
                                    container.items.add(file);
                                    fileInput.files = container.files;

                                    // Trigger change for any listeners
                                    fileInput.dispatchEvent(new Event('change'));

                                    // Store base64 in companion hidden input for old() persistence
                                    const base64InputId = fileInput.getAttribute('data-base64');
                                    if (base64InputId) {
                                        const base64Input = document.getElementById(base64InputId);
                                        if (base64Input) {
                                            const reader = new FileReader();
                                            reader.onload = function (e) {
                                                base64Input.value = e.target.result;
                                            };
                                            reader.readAsDataURL(blob);
                                        }
                                    }

                                    // Manually update preview
                                    if (previewImg) {
                                        previewImg.src = URL.createObjectURL(blob);
                                        previewImg.classList.remove('hidden');
                                    }
                                    if (placeholder) {
                                        placeholder.classList.add('hidden');
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Foto peta berhasil disimpan!',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    console.warn('Target input not found for index:', index);
                                    Swal.fire('Warning', 'Lokasi penyimpanan foto tidak ditemukan. Pastikan jenis agunan benar.', 'warning');
                                }
                            }, 'image/jpeg');
                        }).catch(err => {
                            console.error('Capture Promise Error:', err);
                            Swal.fire('Error', 'Gagal mengambil gambar peta: ' + (err.message || err), 'error');
                        });
                    }, 500);
                };

                // --- Collateral Map Logic (Embedded) -REMOVED (Replaced by Modal) ---
                // Window.initCollateralMap was removed as it is no longer used by the new modal approach.

                // --- Cropper Logic (Event Delegation) ---
                const cropperModal = document.getElementById('cropper-modal');
                const cropperImage = document.getElementById('cropper-image');
                const cropConfirmBtn = document.getElementById('crop-confirm-btn');
                const cancelCropBtn = document.getElementById('cancel-crop-btn');
                let cropper = null;
                let activeInput = null;

                // Use document to listen for changes on .photo-input (Event Delegation)
                document.body.addEventListener('change', function (e) {
                    if (e.target && e.target.classList.contains('photo-input')) {
                        const input = e.target;
                        const file = input.files[0];
                        if (file && file.type.match('image.*')) {
                            activeInput = input;
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                cropperModal.classList.remove('hidden');
                                cropperImage.src = event.target.result;
                                if (cropper) cropper.destroy();
                                cropper = new Cropper(cropperImage, {
                                    aspectRatio: 16 / 9,
                                    viewMode: 1,
                                    dragMode: 'move',
                                    autoCropArea: 1,
                                    background: false
                                });
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });

                cancelCropBtn.addEventListener('click', function () {
                    cropperModal.classList.add('hidden');
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    if (activeInput) activeInput.value = '';
                });

                cropConfirmBtn.addEventListener('click', function () {
                    if (cropper && activeInput) {
                        const canvas = cropper.getCroppedCanvas();
                        canvas.toBlob(function (blob) {
                            const fileName = 'cropped_' + (activeInput.name || 'image') + '.jpg';
                            const croppedFile = new File([blob], fileName, {
                                type: 'image/jpeg'
                            });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(croppedFile);
                            activeInput.files = dataTransfer.files;

                            const base64Data = canvas.toDataURL('image/jpeg');

                            const previewId = activeInput.getAttribute('data-preview');
                            const placeholderId = activeInput.getAttribute('data-placeholder');
                            const base64InputId = activeInput.getAttribute('data-base64');
                            if (previewId) {
                                const previewImg = document.getElementById(previewId);
                                if (previewImg) {
                                    previewImg.src = base64Data;
                                    previewImg.classList.remove('hidden');
                                }
                            }
                            if (placeholderId) {
                                const placeholderDiv = document.getElementById(placeholderId);
                                if (placeholderDiv) placeholderDiv.classList.add('hidden');
                            }
                            // Store base64 in companion hidden input
                            if (base64InputId) {
                                const base64Input = document.getElementById(base64InputId);
                                if (base64Input) base64Input.value = base64Data;
                            }

                            cropperModal.classList.add('hidden');
                            cropper.destroy();
                            cropper = null;
                        }, 'image/jpeg');
                    }
                });

                // --- Main Business Map Logic (Static) ---
                const defaultLat = -7.4704747;
                const defaultLng = 112.4401329;
                let mainMap = null;
                let mainMarker = null;

                function initMainMap() {
                    const mapEl = document.getElementById('map');
                    if (!mapEl || mainMap) return;

                    // Load existing values or use defaults
                    const latInput = document.getElementById('business_latitude');
                    const lngInput = document.getElementById('business_longitude');
                    const currentLat = parseFloat(latInput.value) || defaultLat;
                    const currentLng = parseFloat(lngInput.value) || defaultLng;

                    const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        crossOrigin: true
                    });

                    const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 19,
                        attribution: 'Tiles &copy; Esri'
                    });

                    mainMap = L.map('map', {
                        center: [currentLat, currentLng],
                        zoom: 13,
                        layers: [osm]
                    });

                    const baseMaps = {
                        "OpenStreetMap": osm,
                        "Google Streets": googleStreets,
                        "Google Satellite": googleHybrid,
                        "Esri Satellite": esriSatellite
                    };

                    L.control.layers(baseMaps).addTo(mainMap);

                    // Add Search Control
                    if (L.Control.Geocoder) {
                        L.Control.geocoder({
                            defaultMarkGeocode: false
                        })
                            .on('markgeocode', function (e) {
                                const latlng = e.geocode.center;
                                updateMainMarker(latlng.lat, latlng.lng);
                                fetchAddress(latlng.lat, latlng.lng);
                                mainMap.fitBounds(e.geocode.bbox);
                            })
                            .addTo(mainMap);
                    }

                    // Set initial marker and ensure inputs are populated
                    updateMainMarker(currentLat, currentLng);

                    mainMap.on('click', function (e) {
                        updateMainMarker(e.latlng.lat, e.latlng.lng);
                        fetchAddress(e.latlng.lat, e.latlng.lng);
                    });

                    // IntersectionObserver for visibility
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                setTimeout(() => {
                                    if (mainMap) {
                                        mainMap.invalidateSize();
                                        const lat = parseFloat(document.getElementById('business_latitude').value) || defaultLat;
                                        const lng = parseFloat(document.getElementById('business_longitude').value) || defaultLng;
                                        mainMap.setView([lat, lng], 16);
                                    }
                                }, 200);
                            }
                        });
                    }, { threshold: 0.1 });
                    observer.observe(mapEl);
                }
                setTimeout(initMainMap, 1000);

                function updateCollateralDistanceAlert(index, lat, lng) {
                    const userOfficeBranch = '{{ auth()->user()->office_branch ?? "Kantor Pusat" }}';
                    const officeCoords = {
                        'Kantor Pusat': { lat: -7.487391381663846, lon: 112.44006721604295 },
                        'Kantor Kas Mojosari': { lat: -7.518635412777564, lon: 112.55732458220886 },
                    };
                    const activeOffice = officeCoords[userOfficeBranch] || officeCoords['Kantor Pusat'];
                    const officeLat = activeOffice.lat;
                    const officeLon = activeOffice.lon;
                    const distance = calculateDistance(lat, lng, officeLat, officeLon);

                    const badgeEl = document.getElementById('collateral-distance-badge-' + index);
                    const hiddenInput = document.getElementById('collateral-path-distance-' + index);

                    // Update hidden input value
                    if (hiddenInput) {
                        hiddenInput.value = distance.toFixed(2);
                    }

                    if (badgeEl) {
                        badgeEl.classList.remove('hidden');

                        let message = '';
                        let colorClasses = [];

                        if (distance < 12) {
                            message = '📍 Jarak dari ' + userOfficeBranch + ': ' + distance.toFixed(2) + ' km (Jarak Aman)';
                            colorClasses = ['text-blue-800', 'bg-blue-100'];
                        } else if (distance >= 12 && distance <= 40) {
                            message = '⚠️ Jarak dari ' + userOfficeBranch + ': ' + distance.toFixed(2) + ' km (Jarak Cukup Jauh)';
                            colorClasses = ['text-yellow-800', 'bg-yellow-100'];
                        } else {
                            message = '🚨 Jarak dari ' + userOfficeBranch + ': ' + distance.toFixed(2) + ' km (Terlalu Jauh)';
                            colorClasses = ['text-red-800', 'bg-red-100'];
                        }

                        badgeEl.innerText = message;

                        // Remove old classes
                        badgeEl.classList.remove('text-blue-800', 'bg-blue-100', 'text-yellow-800', 'bg-yellow-100', 'text-red-800', 'bg-red-100');

                        // Add new classes
                        badgeEl.classList.add(...colorClasses);
                    }
                }

                function calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371; // Radius of the earth in km
                    const dLat = deg2rad(lat2 - lat1);
                    const dLon = deg2rad(lon2 - lon1);
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    const d = R * c; // Distance in km
                    return d;
                }

                function deg2rad(deg) {
                    return deg * (Math.PI / 180);
                }

                function updateDistanceAlert(lat, lng) {
                    const userOfficeBranch = '{{ auth()->user()->office_branch ?? "Kantor Pusat" }}';
                    const officeCoords = {
                        'Kantor Pusat': { lat: -7.487391381663846, lon: 112.44006721604295 },
                        'Kantor Kas Mojosari': { lat: -7.518635412777564, lon: 112.55732458220886 },
                    };
                    const activeOffice = officeCoords[userOfficeBranch] || officeCoords['Kantor Pusat'];
                    const officeLat = activeOffice.lat;
                    const officeLon = activeOffice.lon;
                    const distance = calculateDistance(lat, lng, officeLat, officeLon);

                    const alertEl = document.getElementById('distance-alert');
                    const valueEl = document.getElementById('distance-value');

                    if (alertEl && valueEl) {
                        alertEl.classList.remove('hidden');

                        let message = '';
                        let colorClasses = [];

                        if (distance < 12) {
                            message = ' (Jarak Aman)';
                            colorClasses = ['text-blue-800', 'bg-blue-50'];
                        } else if (distance >= 12 && distance <= 40) {
                            message = ' (Jarak Cukup Jauh)';
                            colorClasses = ['text-yellow-800', 'bg-yellow-50'];
                        } else {
                            message = ' (Jarak Berisiko / Terlalu Jauh)';
                            colorClasses = ['text-red-800', 'bg-red-50'];
                        }

                        valueEl.innerText = distance.toFixed(2) + ' km' + message;

                        // Remove old classes
                        alertEl.classList.remove('text-blue-800', 'bg-blue-50', 'text-yellow-800', 'bg-yellow-50', 'text-red-800', 'bg-red-50');

                        // Add new classes
                        alertEl.classList.add(...colorClasses);
                    }
                }

                function updateMainMarker(lat, lng) {
                    if (mainMarker) mainMarker.setLatLng([lat, lng]);
                    else mainMarker = L.marker([lat, lng]).addTo(mainMap);

                    document.getElementById('business_latitude').value = lat.toFixed(8);
                    document.getElementById('business_longitude').value = lng.toFixed(8);
                    mainMap.setView([lat, lng], 16);
                    updateDistanceAlert(lat, lng);
                }

                // Shared Address Fetch
                // Shared Address Fetch
                async function fetchAddress(lat, lng, targetPrefix = 'business_') {
                    try {
                        const url = `/geocoding/reverse?lat=${lat}&lon=${lng}`;
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Failed to fetch address');
                        const data = await response.json();
                        const addr = data.address;

                        if (targetPrefix === 'business_') {
                            document.getElementById('business_village').value = addr.village || addr.suburb || addr.hamlet || '';
                            document.getElementById('business_district').value = addr.city_district || addr.district || addr.suburb || '';
                            document.getElementById('business_regency').value = addr.city || addr.town || addr.county || '';
                            document.getElementById('business_province').value = addr.state || addr.region || '';
                        } else if (targetPrefix === 'return_object') {
                            const village = addr.village || addr.suburb || addr.hamlet || '';
                            const district = addr.city_district || addr.district || addr.suburb || '';
                            const regency = addr.city || addr.town || addr.county || '';
                            const province = addr.state || addr.region || '';
                            const locationAddress = data.display_name || [village, district, regency, province].filter(Boolean).join(', ');
                            return {
                                village,
                                district,
                                regency,
                                province,
                                location_address: locationAddress
                            };
                        }
                    } catch (error) {
                        console.error('Error fetching address:', error);
                        return null;
                    }
                }

                // Expose fetchAddress globally if needed by Alpine
                window.fetchAddressForModal = fetchAddress;

                // Get Location Button (Main Map)
                const locationBtn = document.getElementById('get-location-btn');
                if (locationBtn) {
                    locationBtn.addEventListener('click', function () {
                        if (navigator.geolocation) {
                            locationBtn.innerText = 'Locating...';
                            locationBtn.disabled = true;
                            navigator.geolocation.getCurrentPosition(
                                function (position) {
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;
                                    updateMainMarker(lat, lng);
                                    fetchAddress(lat, lng);
                                    locationBtn.disabled = false;
                                    locationBtn.innerText = 'Lokasi Terdeteksi';
                                },
                                function (error) {
                                    locationBtn.disabled = false;
                                    locationBtn.innerText = 'Baca Lokasi';
                                    Swal.fire('Error', 'Gagal mendeteksi lokasi.', 'error');
                                }
                            );
                        }
                    });
                }

                // --- Form Capture Logic ---
                // --- Form Capture Logic (Exposed Globally) ---
                window.captureMapAndSubmit = function (formElement) {
                    const mapElement = document.getElementById('map');

                    // Check if map is visible/initialized (only for Wirausaha)
                    if (!mapElement || mapElement.offsetParent === null) {
                        console.log('Map skipped (not visible).');
                        formElement.submit();
                        return;
                    }

                    Swal.fire({
                        title: 'Memproses Data...',
                        text: 'Mohon tunggu sebentar, sedang mengambil snapshot peta...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Ensure map logic runs before capture
                    if (mainMap) {
                        mainMap.invalidateSize();
                    }

                    setTimeout(() => {
                        html2canvas(mapElement, {
                            useCORS: true,
                            allowTaint: false,
                            ignoreElements: (el) => el.classList.contains('leaflet-control-zoom') || el.classList.contains('leaflet-control-container'),
                            scale: 1
                        }).then(canvas => {
                            try {
                                const croppedCanvas = document.createElement('canvas');
                                const targetWidth = 800;
                                const targetHeight = 400;
                                croppedCanvas.width = targetWidth;
                                croppedCanvas.height = targetHeight;
                                const ctx = croppedCanvas.getContext('2d');

                                // Center crop
                                const sx = (canvas.width - targetWidth) / 2;
                                const sy = (canvas.height - targetHeight) / 2;

                                // Fill white background
                                ctx.fillStyle = "#FFFFFF";
                                ctx.fillRect(0, 0, targetWidth, targetHeight);

                                // Draw image centered (clamp if smaller)
                                const drawSx = Math.max(0, sx);
                                const drawSy = Math.max(0, sy);
                                const drawW = Math.min(canvas.width, targetWidth);
                                const drawH = Math.min(canvas.height, targetHeight);

                                ctx.drawImage(canvas, drawSx, drawSy, drawW, drawH, 0, 0, targetWidth, targetHeight);

                                const dataUrl = croppedCanvas.toDataURL("image/png");

                                // Update input and preview
                                document.getElementById('location_image').value = dataUrl;

                                const previewContainer = document.getElementById('map-preview-container');
                                const previewImg = document.getElementById('map-preview');
                                if (previewContainer && previewImg) {
                                    previewImg.src = dataUrl;
                                    previewContainer.classList.remove('hidden');
                                }

                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Gambar peta berhasil diambil!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                            } catch (err) {
                                console.error('Cropping error:', err);
                                Swal.fire('Error', 'Gagal memproses gambar peta.', 'error');
                            }

                        }).catch(err => {
                            console.error('Map capture failed:', err);
                            Swal.fire('Error', 'Gagal mengambil gambar peta.', 'error');
                        });
                    }, 800);
                };

                // Global wrapper for manual button
                window.captureMapManual = function () {
                    const formElement = document.getElementById('evaluation-form'); // Dummy or unused for manual
                    // We reuse the logic but don't submit. 
                    // Let's refactor captureMapAndSubmit slightly to separate capture from submit.
                    // For now, I'll essentially duplicate the capture logic adapted for manual click (no submit).

                    const mapElement = document.getElementById('map');
                    if (!mapElement) return;

                    Swal.fire({
                        title: 'Mengambil Gambar...',
                        text: 'Mohon tunggu sebentar...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    if (mainMap) mainMap.invalidateSize();

                    setTimeout(() => {
                        html2canvas(mapElement, {
                            useCORS: true,
                            allowTaint: false,
                            ignoreElements: (el) => el.classList.contains('leaflet-control-zoom') || el.classList.contains('leaflet-control-container'),
                            scale: 1,
                            backgroundColor: '#ffffff',
                            onclone: (clonedDoc) => {
                                // Remove styles that might contain oklch (Tailwind v4)
                                const links = clonedDoc.getElementsByTagName('link');
                                for (let i = links.length - 1; i >= 0; i--) {
                                    const href = links[i].href || '';
                                    if (href.includes('app') || href.includes('resources/css')) {
                                        links[i].parentNode.removeChild(links[i]);
                                    }
                                }
                            }
                        }).then(canvas => {
                            try {
                                const targetWidth = 800;
                                // Calculate height to maintain aspect ratio
                                const scaleFactor = targetWidth / canvas.width;
                                const targetHeight = canvas.height * scaleFactor;

                                const croppedCanvas = document.createElement('canvas');
                                croppedCanvas.width = targetWidth;
                                croppedCanvas.height = targetHeight;
                                const ctx = croppedCanvas.getContext('2d');

                                // Fill white background
                                ctx.fillStyle = "#FFFFFF";
                                ctx.fillRect(0, 0, targetWidth, targetHeight);

                                // Draw the FULL canvas scaled down to target dimensions
                                ctx.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, targetWidth, targetHeight);

                                const dataUrl = croppedCanvas.toDataURL("image/png");
                                document.getElementById('location_image').value = dataUrl;

                                const previewContainer = document.getElementById('map-preview-container');
                                const previewImg = document.getElementById('map-preview');
                                if (previewContainer && previewImg) {
                                    previewImg.src = dataUrl;
                                    previewContainer.classList.remove('hidden');
                                }

                                Swal.close();
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Gambar peta berhasil diambil'
                                });

                            } catch (err) {
                                console.error('Start Manual Capture Error:', err);
                                Swal.fire('Error', 'Gagal memproses gambar: ' + err.message, 'error');
                            }
                        }).catch(err => {
                            console.error('Capture Failed:', err);
                            Swal.fire('Error', 'Gagal mengambil snapshot: ' + err.message, 'error');
                        });
                    }, 500);
                };

                // Updated Submit Logic
                window.captureMapAndSubmit = function (formElement) {
                    // Check if we already have an image
                    const existingImage = document.getElementById('location_image').value;
                    if (existingImage && existingImage.length > 100) {
                        formElement.submit();
                        return;
                    }

                    // If no image, suggest manual capture or try auto
                    const mapElement = document.getElementById('map');
                    if (!mapElement || mapElement.offsetParent === null) {
                        formElement.submit(); // Not visible, skip
                        return;
                    }

                    Swal.fire({
                        title: 'Gambar Peta Belum Diambil',
                        text: "Anda belum mengambil gambar peta. Sistem akan mencoba mengambilnya sekarang.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Ambil & Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Trigger manual capture then submit
                            window.captureMapManual();
                            // Wait briefly then submit (risky if capture is slow, but better than nothing)
                            setTimeout(() => {
                                const img = document.getElementById('location_image').value;
                                if (img) formElement.submit();
                                else Swal.fire('Gagal', 'Silakan coba ambil gambar peta secara manual.', 'error');
                            }, 2000);
                        }
                    });
                };

                // --- Restore old() image previews after validation failure ---
                document.addEventListener('DOMContentLoaded', function () {
                    // 1. Restore Section 1 business photo previews
                    const photoFields = [
                        { preview: 'legality-preview', placeholder: 'legality-placeholder', data: 'business_legality_photo_data' },
                        { preview: 'detail1-preview', placeholder: 'detail1-placeholder', data: 'business_detail_1_photo_data' },
                        { preview: 'detail2-preview', placeholder: 'detail2-placeholder', data: 'business_detail_2_photo_data' }
                    ];
                    photoFields.forEach(function (field) {
                        const dataInput = document.getElementById(field.data);
                        if (dataInput && dataInput.value && dataInput.value.length > 50) {
                            const previewImg = document.getElementById(field.preview);
                            const placeholder = document.getElementById(field.placeholder);
                            if (previewImg) {
                                previewImg.src = dataInput.value;
                                previewImg.classList.remove('hidden');
                            }
                            if (placeholder) placeholder.classList.add('hidden');
                        }
                    });

                    // 2. Restore map preview from old() location_image
                    const locationImageInput = document.getElementById('location_image');
                    if (locationImageInput && locationImageInput.value && locationImageInput.value.length > 50) {
                        const previewContainer = document.getElementById('map-preview-container');
                        const previewImg = document.getElementById('map-preview');
                        if (previewContainer && previewImg) {
                            previewImg.src = locationImageInput.value;
                            previewContainer.classList.remove('hidden');
                        }
                    }

                    // 3. Restore collateral image previews from old() base64 data
                    @if(old('collaterals'))
                        @foreach(old('collaterals') as $colIdx => $col)
                            @if(isset($col['images_data']))
                                @foreach($col['images_data'] as $imgIdx => $imgData)
                                    @if(!empty($imgData))
                                        (function () {
                                            var colIdx = {{ $colIdx }};
                                            var imgIdx = {{ $imgIdx }};
                                            var data = @json($imgData);
                                            // Try certificate preview first, then vehicle
                                            var previewEl = document.getElementById('col-preview-' + colIdx + '-' + imgIdx)
                                                || document.getElementById('col-veh-preview-' + colIdx + '-' + imgIdx);
                                            var placeholderEl = document.getElementById('col-placeholder-' + colIdx + '-' + imgIdx)
                                                || document.getElementById('col-veh-placeholder-' + colIdx + '-' + imgIdx);
                                            var dataInput = document.getElementById('col_img_data_' + colIdx + '_' + imgIdx)
                                                || document.getElementById('col_veh_img_data_' + colIdx + '_' + imgIdx);
                                            if (previewEl && data && data.length > 50) {
                                                previewEl.src = data;
                                                previewEl.classList.remove('hidden');
                                                if (placeholderEl) placeholderEl.classList.add('hidden');
                                            }
                                            if (dataInput) dataInput.value = data;
                                        })();
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                                                                                                                                                        });
            });
        </script>
    @endpush



    </div>
@endsection