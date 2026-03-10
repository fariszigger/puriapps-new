@extends('layouts.dashboard')

@section('title', 'Tambah Kunjungan Nasabah')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        #visit-map {
            height: 350px;
            width: 100%;
            border-radius: 0.5rem;
            z-index: 0;
        }

        .cropper-view-box,
        .cropper-face {
            border-radius: 0;
        }

        .ql-editor {
            min-height: 120px;
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
            <a href="{{ route('customer-visits.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">Daftar Kunjungan</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Kunjungan</span>
        </div>
    </li>
@endsection

@section('content')
    <div x-data="visitForm()" x-init="$watch('selectedCustomer', value => { if(!value) showCustomerModal = true })">
        <div
            class="w-full max-w-4xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-24">

            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6">Tambah Kunjungan Nasabah</h1>

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif



            <form action="{{ route('customer-visits.store') }}" method="POST" enctype="multipart/form-data" id="visit-form">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="customer_id" :value="selectedCustomer?.id || ''">
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                <input type="hidden" id="village" name="village" value="{{ old('village') }}">
                <input type="hidden" id="district" name="district" value="{{ old('district') }}">
                <input type="hidden" id="regency" name="regency" value="{{ old('regency') }}">
                <input type="hidden" id="province" name="province" value="{{ old('province') }}">
                <input type="hidden" id="location_image" name="location_image">
                <input type="hidden" id="photo_base64" name="photo_base64" value="{{ old('photo_base64') }}">
                <input type="hidden" id="photo_rumah_base64" name="photo_rumah_base64"
                    value="{{ old('photo_rumah_base64') }}">
                <input type="hidden" id="photo_orang_base64" name="photo_orang_base64"
                    value="{{ old('photo_orang_base64') }}">
                <input type="hidden" id="kondisi_saat_ini_hidden" name="kondisi_saat_ini"
                    value="{{ old('kondisi_saat_ini') }}">
                <input type="hidden" id="rencana_penyelesaian_hidden" name="rencana_penyelesaian"
                    value="{{ old('rencana_penyelesaian') }}">

                <div class="space-y-8">

                    {{-- ================= SECTION HEADER ================= --}}
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3 border-b pb-4 mb-2">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                </path>
                            </svg>
                        </span>
                        Data Kunjungan Nasabah
                    </h2>

                    {{-- ================= 1. CUSTOMER SELECTION (evaluations.create style) ================= --}}
                    <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100 rounded-bl-full -mr-4 -mt-4 opacity-50">
                        </div>
                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
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
                                                class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">KTP
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

                                    {{-- Penagihan Ke badge --}}
                                    <div x-show="selectedCustomer" style="display: none;" class="mt-3">
                                        <span
                                            class="inline-flex items-center gap-2 bg-white/80 border border-blue-200 rounded-lg px-3 py-1.5 text-sm">
                                            <span
                                                class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Penagihan
                                                Ke</span>
                                            <span class="font-bold text-blue-900 text-lg" x-text="pengihanKe"></span>
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="showCustomerModal = true"
                                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center shadow-lg transform hover:-translate-y-0.5 transition-all shrink-0">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Pilih Nasabah
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 1.5. SPK NUMBER ================= --}}
                    <div class="space-y-4">
                        <label for="spk_number" class="block mb-2 text-sm font-medium text-gray-900">Nomor SPK / Rekening
                            Kredit</label>
                        <input type="text" id="spk_number" name="spk_number" value="{{ old('spk_number') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            placeholder="Masukkan Nomor SPK / Rekening Kredit">
                    </div>

                    {{-- ================= 2. ADDRESS WITH GEOMAP ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Alamat & Lokasi
                            Kunjungan</h2>

                        <div class="space-y-4">
                            <div>
                                <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Alamat
                                    Lengkap</label>
                                <textarea id="address" name="address" rows="3"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 backdrop-blur-sm cursor-not-allowed"
                                    placeholder="Alamat akan terisi otomatis dari lokasi"
                                    readonly>{{ old('address') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="village_display"
                                        class="block mb-2 text-sm font-medium text-gray-900">Kelurahan
                                        / Desa</label>
                                    <input type="text" id="village_display" value="{{ old('village') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                        readonly>
                                </div>
                                <div>
                                    <label for="district_display"
                                        class="block mb-2 text-sm font-medium text-gray-900">Kecamatan</label>
                                    <input type="text" id="district_display" value="{{ old('district') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                        readonly>
                                </div>
                                <div>
                                    <label for="regency_display" class="block mb-2 text-sm font-medium text-gray-900">Kota /
                                        Kabupaten</label>
                                    <input type="text" id="regency_display" value="{{ old('regency') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                        readonly>
                                </div>
                                <div>
                                    <label for="province_display"
                                        class="block mb-2 text-sm font-medium text-gray-900">Provinsi</label>
                                    <input type="text" id="province_display" value="{{ old('province') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                        readonly>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <label class="block text-sm font-medium text-gray-900">Lokasi (Pin on map)</label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="take-map-btn"
                                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Ambil Peta
                                        </button>
                                        <button type="button" id="get-location-btn"
                                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Read Current Location
                                        </button>
                                    </div>
                                </div>
                                <div id="visit-map"
                                    style="height: 350px; width: 100%; border-radius: 0.5rem; z-index: 0; border: 1px solid #d1d5db;">
                                </div>
                                {{-- Map preview after capture --}}
                                <div id="map-preview-container" class="hidden mt-2">
                                    <p class="text-xs text-green-600 font-medium mb-1">✓ Peta berhasil diambil</p>
                                    <img id="map-preview"
                                        class="w-full max-w-sm rounded-lg border border-gray-300 shadow-sm"
                                        alt="Map Preview">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Latitude</label>
                                    <input type="text" id="latitude_display" value="{{ old('latitude') }}"
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Longitude</label>
                                    <input type="text" id="longitude_display" value="{{ old('longitude') }}"
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 3. KOLEKTIBILITAS ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Kolektibilitas
                        </h2>
                        <select name="kolektibilitas" required x-model="kol"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option value="" disabled>Pilih Kolektibilitas...
                            </option>
                            <option value="1">1 - Lancar</option>
                            <option value="2">2 - DPK</option>
                            <option value="3">3 - Kurang Lancar</option>
                            <option value="4">4 - Diragukan</option>
                            <option value="5">5 - Macet</option>
                        </select>

                        {{-- Baki Debet (only for KL/Diragukan/Macet) --}}
                        <div x-show="['3','4','5'].includes(kol)" x-transition class="mt-3">
                            <label for="baki_debet_display" class="block mb-2 text-sm font-medium text-gray-900">Baki Debet
                                (Rp)</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                <input type="text" id="baki_debet_display" x-model="bakiDisplay"
                                    @input="updateBaki($event.target.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 bg-white/50 backdrop-blur-sm"
                                    placeholder="0">
                                <input type="hidden" name="baki_debet" :value="bakiRaw">
                            </div>
                        </div>
                    </div>

                    {{-- ================= 4. BERTEMU DENGAN ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Bertemu Dengan
                        </h2>
                        <select name="ketemu_dengan" required x-model="ketemuDengan"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option value="" disabled>Pilih...</option>
                            <option value="Debitur">Debitur</option>
                            <option value="Suami/Istri">Suami/Istri</option>
                            <option value="Anak">Anak</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>

                        {{-- Nama orang yang ditemui (if not Debitur) --}}
                        <div x-show="ketemuDengan && ketemuDengan !== 'Debitur'" x-transition class="mt-3">
                            <label for="nama_orang_ditemui" class="block mb-2 text-sm font-medium text-gray-900">Nama Orang
                                yang
                                Ditemui</label>
                            <input type="text" id="nama_orang_ditemui" name="nama_orang_ditemui"
                                value="{{ old('nama_orang_ditemui') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                placeholder="Masukkan nama orang yang ditemui...">
                        </div>
                    </div>

                    {{-- ================= 5. KONDISI SAAT INI (Rich Text) ================= --}}
                    <div class="space-y-4" x-show="!['1','2'].includes(kol)" x-transition>
                        <div class="flex items-center justify-between border-b-2 border-gray-100 pb-2">
                            <h2 class="text-xl font-semibold text-gray-900">Kondisi Saat Ini</h2>
                            <button type="button" @click="openTemplateModal('kondisi')"
                                class="text-xs text-blue-700 hover:text-white hover:bg-blue-600 bg-blue-100 flex items-center gap-1 font-bold px-3 py-1.5 rounded-lg border border-blue-200 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Gunakan Template (10 Contoh)
                            </button>
                        </div>
                        <div id="kondisi-editor" class="bg-white rounded-lg">{!! old('kondisi_saat_ini') !!}</div>
                    </div>

                    {{-- ================= 6. RENCANA PENYELESAIAN (Rich Text) ================= --}}
                    <div class="space-y-4" x-show="!['1','2'].includes(kol)" x-transition>
                        <div class="flex items-center justify-between border-b-2 border-gray-100 pb-2">
                            <h2 class="text-xl font-semibold text-gray-900">Rencana Penyelesaian</h2>
                            <button type="button" @click="openTemplateModal('rencana')"
                                class="text-xs text-blue-700 hover:text-white hover:bg-blue-600 bg-blue-100 flex items-center gap-1 font-bold px-3 py-1.5 rounded-lg border border-blue-200 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Gunakan Template (10 Contoh)
                            </button>
                        </div>
                        <div id="rencana-editor" class="bg-white rounded-lg">{!! old('rencana_penyelesaian') !!}</div>
                    </div>

                    {{-- ================= 7. HASIL PENAGIHAN ================= --}}
                    <div class="space-y-4" x-show="!['1','2'].includes(kol)" x-transition>
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Hasil Penagihan
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="hasil_penagihan" value="bayar" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Bayar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer" x-show="!['1','2'].includes(kol)">
                                    <input type="radio" name="hasil_penagihan" value="janji_bayar" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Janji Bayar</span>
                                </label>
                            </div>

                            {{-- Bayar: Show Rp input --}}
                            <div x-show="hasilPenagihan === 'bayar'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pembayaran</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                    <input type="text" x-model="displayJumlahBayar"
                                        @input="updateJumlahBayar($event.target.value)"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 bg-white/50 backdrop-blur-sm"
                                        placeholder="0">
                                    <input type="hidden" name="jumlah_bayar" :value="jumlahBayar">
                                </div>
                            </div>

                            {{-- Janji Bayar: Show date + jumlah pembayaran --}}
                            <div x-show="hasilPenagihan === 'janji_bayar'" x-transition class="space-y-3">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Janji Bayar</label>
                                    <input type="date" name="tanggal_janji_bayar" value="{{ old('tanggal_janji_bayar') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pembayaran
                                        (Rp)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                        <input type="text" x-model="displayJumlahPembayaran"
                                            @input="updateJumlahPembayaran($event.target.value)"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 bg-white/50 backdrop-blur-sm"
                                            placeholder="0">
                                        <input type="hidden" name="jumlah_pembayaran" :value="jumlahPembayaran">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Kunjungan
                        </h2>

                        <div class="flex justify-center">
                            <div id="photo-upload-area"
                                class="w-full max-w-md h-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group relative"
                                onclick="document.getElementById('photo').click()">
                                <img id="photo-preview" class="w-full h-full object-cover hidden" alt="Photo Preview">
                                <div id="photo-placeholder"
                                    class="text-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs font-medium">Tap untuk Upload Foto Kunjungan</p>
                                    <p class="text-[10px] mt-1">JPG, PNG • 16:10</p>
                                </div>
                            </div>
                            <input class="hidden photo-input" id="photo" name="photo" type="file" accept="image/*"
                                capture="environment" data-target="photo">
                        </div>
                    </div>

                    {{-- ================= 9. FOTO RUMAH DEBITUR ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Rumah
                            Debitur
                        </h2>

                        <div class="flex justify-center">
                            <div id="photo_rumah-upload-area"
                                class="w-full max-w-md h-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group relative"
                                onclick="document.getElementById('photo_rumah').click()">
                                <img id="photo_rumah-preview" class="w-full h-full object-cover hidden"
                                    alt="Photo Rumah Preview">
                                <div id="photo_rumah-placeholder"
                                    class="text-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs font-medium">Tap untuk Upload Foto Rumah</p>
                                    <p class="text-[10px] mt-1">JPG, PNG • 16:10</p>
                                </div>
                            </div>
                            <input class="hidden photo-input" id="photo_rumah" name="photo_rumah" type="file"
                                accept="image/*" capture="environment" data-target="photo_rumah">
                        </div>
                    </div>

                    {{-- ================= 10. FOTO ORANG YANG DITEMUI ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Orang yang
                            Ditemui
                        </h2>

                        <div class="flex justify-center">
                            <div id="photo_orang-upload-area"
                                class="w-full max-w-md h-64 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group relative"
                                onclick="document.getElementById('photo_orang').click()">
                                <img id="photo_orang-preview" class="w-full h-full object-cover hidden"
                                    alt="Photo Orang Preview">
                                <div id="photo_orang-placeholder"
                                    class="text-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs font-medium">Tap untuk Upload Foto Orang</p>
                                    <p class="text-[10px] mt-1">JPG, PNG • 16:10</p>
                                </div>
                            </div>
                            <input class="hidden photo-input" id="photo_orang" name="photo_orang" type="file"
                                accept="image/*" capture="environment" data-target="photo_orang">
                        </div>
                    </div>

                    {{-- ================= SUBMIT ================= --}}
                    <div class="flex justify-end pt-6 mt-8 border-t-2 border-gray-100">
                        <button type="submit" id="submit-btn"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-bold rounded-xl text-lg px-10 py-4 focus:outline-none inline-flex items-center gap-3 shadow-xl hover:shadow-green-500/40 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Kunjungan
                        </button>
                    </div>
                </div>
            </form>

        </div>

        {{-- ================= CUSTOMER SELECTION MODAL ================= --}}
        <div x-show="showCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="showCustomerModal = false"></div>

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
                                            placeholder="Cari berdasarkan Nama / KTP / Alamat...">
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
                                                        KTP</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Alamat</th>
                                                    <th scope="col" class="relative px-6 py-3"><span
                                                            class="sr-only">Pilih</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="customer in filteredCustomers" :key="customer.id">
                                                    <tr class="hover:bg-blue-50 cursor-pointer transition-colors"
                                                        @click="selectCustomer(customer)">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                            x-text="customer.name"></td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                            x-text="customer.identity_number ?? '-'"></td>
                                                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs"
                                                            x-text="customer.address ?? '-'"></td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button type="button"
                                                                class="text-blue-600 hover:text-blue-900 font-bold hover:underline">PILIH</button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="filteredCustomers.length === 0">
                                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                                        Tidak ada data nasabah ditemukan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showCustomerModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TEMPLATE MODAL ================= --}}
        <div x-show="showTemplateModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
            aria-labelledby="template-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"
                    @click="showTemplateModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full relative z-[100]">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="template-modal-title"
                                x-text="activeTemplateTarget === 'kondisi' ? 'Template: Kondisi Saat Ini' : 'Template: Rencana Penyelesaian'">
                            </h3>
                            <button @click="showTemplateModal = false"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500">Klik salah satu kotak template di bawah ini untuk digunakan pada
                            form jawaban. Anda masih dapat mengedit teksnya setelah dimasukkan.</p>
                    </div>
                    <div class="bg-gray-50 p-6 max-h-[60vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(templateText, index) in templates[activeTemplateTarget]" :key="index">
                                <div @click="applyTemplate(templateText)"
                                    class="p-5 bg-white border border-gray-200 rounded-xl hover:bg-blue-50 focus:bg-blue-50 active:bg-blue-100 hover:border-blue-400 cursor-pointer transition-all group flex flex-col justify-between shadow-sm hover:shadow-md h-full">
                                    <p class="text-[15px] text-gray-700 font-medium group-hover:text-blue-900 leading-relaxed mb-4"
                                        x-text="'&quot;' + templateText + '&quot;'"></p>
                                    <div
                                        class="mt-auto pt-3 border-t border-gray-100 border-dashed text-sm font-bold text-blue-600 flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        Gunakan Kalimat Ini
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="bg-gray-100 px-4 py-3 sm:px-6 flex flex-row-reverse border-t shadow-inner">
                        <button type="button" @click="showTemplateModal = false"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CROPPER MODAL ================= --}}
        <div id="cropper-modal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="cropper-modal-title" role="dialog"
            aria-modal="true">
            <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col max-h-[90vh]">
                        <div
                            class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 sm:px-6 flex justify-between items-center shadow-sm">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="cropper-modal-title">
                                Crop Photo (16:10 Ratio)
                            </h3>
                            <div class="flex space-x-2">
                                <button type="button" id="cancel-crop-btn"
                                    class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                                <button type="button" id="crop-btn"
                                    class="inline-flex justify-center rounded-lg border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-1.5 h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Crop & Save
                                </button>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 bg-gray-50 flex-grow overflow-y-auto flex items-center justify-center">
                            <div class="relative w-full" style="height: 500px; max-height: 60vh;">
                                <img id="cropper-image" src="" alt="Crop Preview"
                                    class="block max-w-full h-full object-contain mx-auto">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 text-center border-t border-gray-200">
                            <p class="text-xs text-gray-500">
                                Drag to adjust. The selection is locked to the required report format.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            // ============ ALPINE.JS DATA ============
            function visitForm() {
                return {
                    customers: @json($customers),
                    search: '',
                    showCustomerModal: true,
                    showTemplateModal: false,
                    activeTemplateTarget: 'kondisi',
                    kol: '{{ old('kolektibilitas', '') }}',
                    ketemuDengan: '{{ old('ketemu_dengan', '') }}',
                    bakiRaw: '{{ old('baki_debet', '0') }}',
                    bakiDisplay: '',
                    templates: {
                        kondisi: [
                            "Usaha nasabah saat ini sedang mengalami penurunan omset dibandingkan bulan lalu karena...",
                            "Nasabah dalam kondisi sehat dan usaha berjalan lancar, namun alokasi dana untuk...",
                            "Tempat usaha nasabah sedang tutup sementara dikarenakan ada keperluan yaitu...",
                            "Nasabah sedang mengalami musibah sakit sehingga operasional usaha libur beberapa hari akibatnya...",
                            "Panen / hasil usaha nasabah mengalami kegagalan (penurunan drastis) disebabkan faktor cuaca / kondisi di...",
                            "Usaha mandek sementara waktu karena nasabah kekurangan modal untuk memutar stok bahan baku di...",
                            "Nasabah baru merelokasi atau pindah lokasi usaha barunya ke area... sehingga masih tahap adaptasi dengan pelanggan",
                            "Kondisi di lapangan sepi pembeli dan pendapatan bulanan menurut nasabah tidak mencukupi untuk bayar...",
                            "Nasabah belum lama ini berhenti bekerja dan masih dalam proses wawancara / pencarian kerja di...",
                            "Nasabah sedang tidak ada di tempat dan kegiatan rumah / usahanya diserahkan sementara kepada wakil bernama..."
                        ],
                        rencana: [
                            "Nasabah bersedia serta berjanji kuat akan melunasi tunggakannya tepat pada tanggal...",
                            "Nasabah sepakat dan akan segera menjual properti/aset miliknya yaitu... untuk menutup hutang di koperasi",
                            "Dilakukan kunjungan / follow up kembali secara intensif dengan atasan pada...",
                            "Nasabah sedang berupaya menunggu proses pencairan dana dari tempat lain di... untuk melunaskan angsurannya",
                            "Memberikan peringatan keras lisan/SP-1 jika tidak ada pembayaran paling lambat di penghujung bulan",
                            "Nasabah secara tertulis memohon kebijakan perpanjangan / restrukturisasi kredit angsurannya karena alasan...",
                            "Akan dilakukan penyitaan agunan secara baik-baik atau sesuai prosedur apabila janji pada... kembali meleset",
                            "Nasabah menitipkan sebagian kewajibannya hari ini sejumlah Rp... dan sisanya berjanji akan menyusul di minggu depan",
                            "Membuat nasabah kembali menandatangani draf surat pernyataan kesanggupan bayar yang lebih mengikat pada...",
                            "Mendesak nasabah untuk hadir di kantor esok hari agar bisa dilakukan pembicaraan langsung dengan manajer"
                        ]
                    },
                    openTemplateModal(target) {
                        this.activeTemplateTarget = target;
                        this.showTemplateModal = true;
                    },
                    applyTemplate(text) {
                        const editor = this.activeTemplateTarget === 'kondisi' ? window.kondisiQuill : window.rencanaQuill;
                        if (!editor) return;

                        const currentHtml = editor.root.innerHTML;
                        const isEmpty = currentHtml === '<p><br></p>' || currentHtml === '';
                        editor.root.innerHTML = isEmpty ? `<p>${text}</p>` : `${currentHtml}<p>${text}</p>`;

                        this.showTemplateModal = false;
                    },
                    selectedCustomer: null,
                    pengihanKe: '-',
                    hasilPenagihan: '{{ old("hasil_penagihan", "") }}',
                    jumlahBayar: '{{ old("jumlah_bayar", "0") }}',
                    displayJumlahBayar: '',
                    jumlahPembayaran: '{{ old("jumlah_pembayaran", "0") }}',
                    displayJumlahPembayaran: '',

                    init() {
                        this.displayJumlahBayar = this.formatNumber(this.jumlahBayar);
                        this.displayJumlahPembayaran = this.formatNumber(this.jumlahPembayaran);
                        if (this.bakiRaw > 0) this.bakiDisplay = this.formatNumber(this.bakiRaw);
                    },

                    updateBaki(v) {
                        const n = parseInt(v.replace(/\D/g, '')) || 0;
                        this.bakiRaw = n;
                        this.bakiDisplay = n ? this.formatNumber(n) : '';
                    },

                    get filteredCustomers() {
                        if (!this.search) return this.customers.slice(0, 50);
                        const term = this.search.toLowerCase();
                        return this.customers.filter(c =>
                            (c.name && c.name.toLowerCase().includes(term)) ||
                            (c.no_id && c.no_id.includes(term)) ||
                            (c.address && c.address.toLowerCase().includes(term))
                        ).slice(0, 50);
                    },

                    selectCustomer(customer) {
                        this.selectedCustomer = customer;
                        this.showCustomerModal = false;
                        // Fetch penagihan_ke via AJAX
                        fetch(`/customer-visits/count/${customer.id}`)
                            .then(r => r.json())
                            .then(data => { this.pengihanKe = data.count + 1; })
                            .catch(() => { this.pengihanKe = '?'; });
                    },

                    formatNumber(value) {
                        if (value === null || value === undefined || value === '' || value === '0') return '';
                        return new Intl.NumberFormat('id-ID').format(value);
                    },

                    updateJumlahBayar(value) {
                        const numericValue = value.replace(/\D/g, '');
                        this.jumlahBayar = numericValue;
                        this.displayJumlahBayar = this.formatNumber(numericValue);
                    },

                    updateJumlahPembayaran(value) {
                        const numericValue = value.replace(/\D/g, '');
                        this.jumlahPembayaran = numericValue;
                        this.displayJumlahPembayaran = this.formatNumber(numericValue);
                    }
                }
            }

            // ============ DOM READY ============
            document.addEventListener('DOMContentLoaded', function () {

                // ---- Quill Rich Text Editors ----
                window.kondisiQuill = new Quill('#kondisi-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    placeholder: 'Deskripsikan kondisi nasabah saat ini...'
                });

                window.rencanaQuill = new Quill('#rencana-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    placeholder: 'Deskripsikan rencana penyelesaian...'
                });

                // ---- CropperJS Logic (Generic for multiple fields) ----
                const cropperModal = document.getElementById('cropper-modal');
                const cropperImage = document.getElementById('cropper-image');
                const cropBtn = document.getElementById('crop-btn');
                const cancelCropBtn = document.getElementById('cancel-crop-btn');
                let cropper = null;
                let activeTarget = null; // 'photo', 'photo_rumah', or 'photo_orang'

                document.querySelectorAll('.photo-input').forEach(input => {
                    input.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        activeTarget = this.dataset.target;

                        if (file) {
                            if (!file.type.match('image.*')) {
                                Swal.fire('Error', 'Pilih file gambar.', 'error');
                                return;
                            }
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                cropperModal.classList.remove('hidden');
                                cropperImage.src = e.target.result;
                                if (cropper) cropper.destroy();
                                cropper = new Cropper(cropperImage, {
                                    aspectRatio: 16 / 10,
                                    viewMode: 1,
                                    dragMode: 'move',
                                    autoCropArea: 1,
                                    background: false
                                });
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                });

                cancelCropBtn.addEventListener('click', function () {
                    cropperModal.classList.add('hidden');
                    if (cropper) { cropper.destroy(); cropper = null; }
                    if (activeTarget) {
                        const input = document.getElementById(activeTarget);
                        const preview = document.getElementById(activeTarget + '-preview');
                        const placeholder = document.getElementById(activeTarget + '-placeholder');
                        input.value = '';
                        // Only hide if there's no previous base64
                        if (!document.getElementById(activeTarget + '_base64').value) {
                            preview.classList.add('hidden');
                            placeholder.classList.remove('hidden');
                        }
                    }
                    activeTarget = null;
                });

                cropBtn.addEventListener('click', function () {
                    if (cropper && activeTarget) {
                        const canvas = cropper.getCroppedCanvas();
                        const input = document.getElementById(activeTarget);
                        const preview = document.getElementById(activeTarget + '-preview');
                        const placeholder = document.getElementById(activeTarget + '-placeholder');
                        const base64Input = document.getElementById(activeTarget + '_base64');

                        // Add watermark for all photos
                        const ctx = canvas.getContext('2d');
                        const aoName = '{{ auth()->user()->name }}';
                        const now = new Date();
                        const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + ' ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        const watermarkText = aoName + ' — ' + dateStr;

                        const fontSize = Math.max(14, Math.floor(canvas.width / 40));
                        ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                        ctx.textAlign = 'right';
                        ctx.textBaseline = 'bottom';

                        const textWidth = ctx.measureText(watermarkText).width;
                        const padding = 10;
                        const stripHeight = fontSize + padding * 2;
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                        ctx.fillRect(canvas.width - textWidth - padding * 3, canvas.height - stripHeight, textWidth + padding * 3, stripHeight);

                        ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
                        ctx.fillText(watermarkText, canvas.width - padding, canvas.height - padding);

                        canvas.toBlob(function (blob) {
                            const croppedFile = new File([blob], `cropped_${activeTarget}.jpg`, { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(croppedFile);
                            input.files = dataTransfer.files;

                            const dataUrl = canvas.toDataURL("image/jpeg");
                            preview.src = dataUrl;
                            base64Input.value = dataUrl;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');

                            cropperModal.classList.add('hidden');
                            cropper.destroy();
                            cropper = null;
                            activeTarget = null;
                        }, 'image/jpeg');
                    }
                });

                // Load old photo base64 values if exist (validation failure)
                ['photo', 'photo_rumah', 'photo_orang'].forEach(id => {
                    const base64 = document.getElementById(id + '_base64').value;
                    if (base64) {
                        const preview = document.getElementById(id + '-preview');
                        const placeholder = document.getElementById(id + '-placeholder');
                        preview.src = base64;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    }
                });

                // ---- Leaflet Map ----
                const oldLat = "{{ old('latitude') }}";
                const oldLng = "{{ old('longitude') }}";
                const defaultLat = oldLat ? parseFloat(oldLat) : -7.4704747;
                const defaultLng = oldLng ? parseFloat(oldLng) : 112.4401329;

                const map = L.map('visit-map', {
                    center: [defaultLat, defaultLng],
                    zoom: 13,
                });

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

                osm.addTo(map);
                L.control.layers({
                    "OpenStreetMap": osm,
                    "Google Streets": googleStreets,
                    "Google Satellite": googleHybrid,
                }).addTo(map);

                /* Geocoder search removed to restrict input to current location only */

                let marker;
                if (oldLat && oldLng) {
                    marker = L.marker([defaultLat, defaultLng]).addTo(map);
                }

                function updateMarker(lat, lng) {
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                    document.getElementById('latitude').value = lat.toFixed(8);
                    document.getElementById('longitude').value = lng.toFixed(8);
                    document.getElementById('latitude_display').value = lat.toFixed(8);
                    document.getElementById('longitude_display').value = lng.toFixed(8);
                    map.setView([lat, lng], 16);
                }

                async function fetchAddress(lat, lng) {
                    try {
                        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
                        const response = await fetch(url, {
                            headers: { 'User-Agent': 'PuriApps/1.0' }
                        });
                        if (!response.ok) throw new Error('Failed to fetch address');
                        const data = await response.json();
                        const addr = data.address;

                        const village = addr.village || addr.suburb || addr.hamlet || '';
                        document.getElementById('village_display').value = village;
                        document.getElementById('village').value = village;

                        const district = addr.city_district || addr.district || addr.suburb || '';
                        document.getElementById('district_display').value = district;
                        document.getElementById('district').value = district;

                        const regency = addr.city || addr.town || addr.county || '';
                        document.getElementById('regency_display').value = regency;
                        document.getElementById('regency').value = regency;

                        const province = addr.state || addr.region || '';
                        document.getElementById('province_display').value = province;
                        document.getElementById('province').value = province;

                        // Auto-populate address from combined location parts
                        const parts = [village, district, regency, province].filter(p => p);
                        document.getElementById('address').value = parts.join(', ');
                    } catch (error) {
                        console.error('Error fetching address:', error);
                    }
                }

                /* Manual marker placement removed to restrict input to current location only */

                // Get Current Location button
                const locationBtn = document.getElementById('get-location-btn');
                locationBtn.addEventListener('click', function () {
                    if (navigator.geolocation) {
                        locationBtn.innerHTML = 'Locating...';
                        locationBtn.disabled = true;

                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;
                                updateMarker(lat, lng);
                                fetchAddress(lat, lng);
                                locationBtn.innerHTML = '✓ Location Found';
                                locationBtn.disabled = false;
                                locationBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                                locationBtn.classList.add('bg-green-600', 'hover:bg-green-700');

                                Swal.fire({
                                    toast: true, position: 'bottom-end',
                                    icon: 'success', title: 'Location found',
                                    showConfirmButton: false, timer: 3000
                                });
                            },
                            function (error) {
                                locationBtn.innerHTML = 'Read Current Location';
                                locationBtn.disabled = false;
                                Swal.fire({ icon: 'error', title: 'Location Error', text: 'Unable to retrieve your location.' });
                            }
                        );
                    } else {
                        Swal.fire({ icon: 'error', title: 'Not Supported', text: 'Geolocation is not supported by your browser.' });
                    }
                });

                // Take Map Screenshot button
                const takeMapBtn = document.getElementById('take-map-btn');
                takeMapBtn.addEventListener('click', function () {
                    const latVal = document.getElementById('latitude').value;
                    const lngVal = document.getElementById('longitude').value;

                    if (!latVal || !lngVal) {
                        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan tentukan lokasi terlebih dahulu.' });
                        return;
                    }

                    takeMapBtn.innerHTML = 'Mengambil...';
                    takeMapBtn.disabled = true;

                    const mapEl = document.getElementById('visit-map');
                    const controls = mapEl.querySelectorAll('.leaflet-control-container');
                    controls.forEach(c => c.style.display = 'none');

                    setTimeout(() => {
                        html2canvas(mapEl, {
                            useCORS: true,
                            allowTaint: false,
                            logging: false,
                            ignoreElements: (element) => {
                                if (element.classList && element.classList.contains('leaflet-control-zoom')) return true;
                                return false;
                            },
                            onclone: (clonedDoc) => {
                                const links = clonedDoc.querySelectorAll('link[rel="stylesheet"], style');
                                links.forEach(tag => {
                                    if (tag.tagName === 'LINK' && tag.href && tag.href.includes('leaflet')) return;
                                    tag.remove();
                                });
                            },
                            scale: 1
                        }).then(canvas => {
                            controls.forEach(c => c.style.display = '');
                            const image = canvas.toDataURL('image/png');
                            if (image.length > 1000) {
                                document.getElementById('location_image').value = image;
                                // Show preview
                                document.getElementById('map-preview').src = image;
                                document.getElementById('map-preview-container').classList.remove('hidden');

                                takeMapBtn.innerHTML = '✓ Peta Diambil';
                                takeMapBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                                takeMapBtn.classList.add('bg-emerald-700', 'hover:bg-emerald-800');

                                Swal.fire({
                                    toast: true, position: 'bottom-end',
                                    icon: 'success', title: 'Screenshot peta berhasil',
                                    showConfirmButton: false, timer: 2000
                                });
                            }
                            takeMapBtn.disabled = false;
                        }).catch(err => {
                            controls.forEach(c => c.style.display = '');
                            console.error('Map capture failed:', err);
                            takeMapBtn.innerHTML = 'Ambil Peta';
                            takeMapBtn.disabled = false;
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengambil screenshot peta.' });
                        });
                    }, 500);
                });

                // ---- Form Submit: Capture map image + Quill content ----
                const visitForm = document.getElementById('visit-form');
                visitForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Simpan Kunjungan?',
                        text: 'Pastikan semua data sudah benar sebelum menyimpan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#15803d',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        // Sync Quill content to hidden inputs
                        document.getElementById('kondisi_saat_ini_hidden').value = kondisiQuill.root.innerHTML;
                        document.getElementById('rencana_penyelesaian_hidden').value = rencanaQuill.root.innerHTML;

                        // Capture map screenshot if lat/lng exist
                        const latVal = document.getElementById('latitude').value;
                        const lngVal = document.getElementById('longitude').value;

                        if (latVal && lngVal) {
                            const mapEl = document.getElementById('visit-map');
                            // Remove Leaflet controls temporarily for cleaner capture
                            const controls = mapEl.querySelectorAll('.leaflet-control-container');
                            controls.forEach(c => c.style.display = 'none');

                            setTimeout(() => {
                                html2canvas(mapEl, {
                                    useCORS: true,
                                    allowTaint: false,
                                    logging: false,
                                    ignoreElements: (element) => {
                                        if (element.classList && element.classList.contains('leaflet-control-zoom')) return true;
                                        return false;
                                    },
                                    onclone: (clonedDoc) => {
                                        const links = clonedDoc.querySelectorAll('link[rel="stylesheet"], style');
                                        links.forEach(tag => {
                                            if (tag.tagName === 'LINK' && tag.href && tag.href.includes('leaflet')) return;
                                            tag.remove();
                                        });
                                    },
                                    scale: 1
                                }).then(canvas => {
                                    controls.forEach(c => c.style.display = '');
                                    const image = canvas.toDataURL("image/png");
                                    if (image.length > 1000) {
                                        document.getElementById('location_image').value = image;
                                    }
                                    visitForm.submit();
                                }).catch(err => {
                                    controls.forEach(c => c.style.display = '');
                                    console.error("Map capture failed:", err);
                                    visitForm.submit();
                                });
                            }, 500);
                        } else {
                            visitForm.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection