@extends('layouts.dashboard')

@section('title', 'Tambah Debitur Baru')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
            z-index: 0;
        }

        .cropper-view-box,
        .cropper-face {
            border-radius: 0;
            /* Square/Rect options */
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
            <a href="{{ route('customers.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">Daftar Debitur</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Debitur</span>
        </div>
    </li>
@endsection

@section('content')
    <div
        class="w-full max-w-4xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6">Tambah Debitur Baru</h1>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Steps Indicator -->
        <ol class="grid grid-cols-3 w-full mb-8 text-sm font-medium text-gray-500 sm:text-base">
            <li class="flex items-center justify-center text-blue-600 dark:text-blue-500" id="step-indicator-1">
                <span
                    class="flex items-center justify-center w-8 h-8 mr-2 text-xs border border-blue-600 rounded-full shrink-0 dark:border-blue-500">
                    1
                </span>
                Bagian 1 : Identitas
            </li>
            <li class="flex items-center justify-center">
                <svg class="w-3 h-3 ml-2 sm:ml-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 12 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                </svg>
            </li>
            <li class="flex items-center justify-center" id="step-indicator-2">
                <span
                    class="flex items-center justify-center w-8 h-8 mr-2 text-xs border border-gray-500 rounded-full shrink-0 dark:border-gray-400">
                    2
                </span>
                Bagian 2 : Hubungan Debitur
            </li>
        </ol>

        <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" id="customer-form">
            @csrf

            <!-- Hidden User ID -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <!-- Part 1: Identity -->
            <div id="part-1" class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 border-b pb-2">Bagian 1 : Identitas Debitur / Calon Debitur
                </h2>
                <div>
                    <label for="customer_type" class="block mb-2 text-sm font-medium text-gray-900">Jenis Debitur / Calon
                        Debitur</label>
                    <select id="customer_type" name="customer_type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                        <option value="Perorangan" {{ old('customer_type') == 'Perorangan' ? 'selected' : '' }}>Perorangan
                        </option>
                        <option value="Badan" {{ old('customer_type') == 'Badan' ? 'selected' : '' }}>Badan</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Photo Upload -->
                    <div class="col-span-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="photo">Foto Debitur / Calon Debitur
                            (4cm x 6cm)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="photo"
                                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-colors relative overflow-hidden group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" id="photo-placeholder">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                            class="font-semibold">Click to upload photo</span></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or GIF (MAX. 10MB)</p>
                                </div>
                                <!-- Added max-w-xs to constrain preview aspect visually in centering -->
                                <img id="photo-preview" class="hidden absolute h-full object-contain rounded-lg" />
                                <input id="photo" name="photo" type="file" class="hidden" accept="image/*" />
                                <input type="hidden" name="photo_base64" id="photo_base64"
                                    value="{{ old('photo_base64') }}">
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="nama" class="block mb-2 text-sm font-medium text-gray-900">Nama Debitur</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            required>
                    </div>
                    <div id="no_id_container">
                        @livewire('check-customer-ktp', ['no_id' => old('no_id')])
                    </div>
                    <div>
                        <label for="pob" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                        <input type="text" id="pob" name="pob" value="{{ old('pob') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            required>
                    </div>
                    <div>
                        <label for="dob" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                        <input type="date" id="dob" name="dob" value="{{ old('dob') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            required>
                    </div>
                    <div id="gender_container">
                        <label for="gender" class="block mb-2 text-sm font-medium text-gray-900">Jenis Kelamin</label>
                        <select id="gender" name="gender"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option selected disabled>Choose an option...</option>
                            <option value="Laki - Laki" {{ old('gender') == 'Laki - Laki' ? 'selected' : '' }}>Laki - Laki
                            </option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div id="marrietal_status_container">
                        <label for="marrietal_status" class="block mb-2 text-sm font-medium text-gray-900">Status
                            Pernikahan</label>
                        <select id="marrietal_status" name="marrietal_status"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option selected disabled>Choose an option...</option>
                            <option value="Lajang" {{ old('marrietal_status') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                            <option value="Menikah" {{ old('marrietal_status') == 'Menikah' ? 'selected' : '' }}>Menikah
                            </option>
                            <option value="Cerai Mati" {{ old('marrietal_status') == 'Cerai Mati' ? 'selected' : '' }}>Cerai
                                Mati</option>
                            <option value="Cerai Hidup" {{ old('marrietal_status') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai
                                Hidup</option>
                        </select>
                    </div>
                    <div>
                        <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900">Nomor Telepon</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            required>
                    </div>
                    <div>
                        <label for="job" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan</label>
                        <input type="text" id="job" name="job" value="{{ old('job') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div id="mother_name_container">
                        <label for="mother_name" class="block mb-2 text-sm font-medium text-gray-900">Nama Gadis Ibu
                            Kandung</label>
                        <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            required>
                    </div>
                    <div id="education_container">
                        <label for="education" class="block mb-2 text-sm font-medium text-gray-900">Pendidikan
                            Terakhir</label>
                        <select id="education" name="education"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option selected disabled>Choose an option...</option>
                            <option value="Sekolah Dasar" {{ old('education') == 'Sekolah Dasar' ? 'selected' : '' }}>Sekolah
                                Dasar</option>
                            <option value="Sekolah Menengah Pertama" {{ old('education') == 'Sekolah Menengah Pertama' ? 'selected' : '' }}>Sekolah Menengah Pertama</option>
                            <option value="Sekolah Menengah Atas" {{ old('education') == 'Sekolah Menengah Atas' ? 'selected' : '' }}>Sekolah Menengah Atas</option>
                            <option value="Diploma I" {{ old('education') == 'Diploma I' ? 'selected' : '' }}>Diploma I
                            </option>
                            <option value="Diploma II" {{ old('education') == 'Diploma II' ? 'selected' : '' }}>Diploma II
                            </option>
                            <option value="Diploma III" {{ old('education') == 'Diploma III' ? 'selected' : '' }}>Diploma III
                            </option>
                            <option value="Diploma IV" {{ old('education') == 'Diploma IV' ? 'selected' : '' }}>Diploma IV
                            </option>
                            <option value="Strata 1" {{ old('education') == 'Strata 1' ? 'selected' : '' }}>Strata 1</option>
                            <option value="Strata 2" {{ old('education') == 'Strata 2' ? 'selected' : '' }}>Strata 2</option>
                            <option value="Strata 3" {{ old('education') == 'Strata 3' ? 'selected' : '' }}>Strata 3</option>
                            <option value="Lainnya" {{ old('education') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="emergency_contact" class="block mb-2 text-sm font-medium text-gray-900">Kontak
                            Darurat</label>
                        <input type="text" id="emergency_contact" name="emergency_contact"
                            value="{{ old('emergency_contact') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            placeholder="e.g. Budi Santoso / 082123456789">
                    </div>

                    <div class="col-span-full">
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Alamat Lengkap (Jalan,
                            RT/RW)</label>
                        <textarea id="address" name="address" rows="3"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white/50 backdrop-blur-sm"
                            placeholder="e.g. Jl. Mawar No. 123, RT 01 / RW 02" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-full">
                        <div>
                            <label for="village_display" class="block mb-2 text-sm font-medium text-gray-900">Kelurahan /
                                Desa</label>
                            <input type="text" id="village_display" value="{{ old('village') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                readonly>
                            <input type="hidden" id="village" name="village" value="{{ old('village') }}">
                        </div>
                        <div>
                            <label for="district_display"
                                class="block mb-2 text-sm font-medium text-gray-900">Kecamatan</label>
                            <input type="text" id="district_display" value="{{ old('district') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                readonly>
                            <input type="hidden" id="district" name="district" value="{{ old('district') }}">
                        </div>
                        <div>
                            <label for="regency_display" class="block mb-2 text-sm font-medium text-gray-900">Kota /
                                Kabupaten</label>
                            <input type="text" id="regency_display" value="{{ old('regency') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                readonly>
                            <input type="hidden" id="regency" name="regency" value="{{ old('regency') }}">
                        </div>
                        <div>
                            <label for="province_display"
                                class="block mb-2 text-sm font-medium text-gray-900">Provinsi</label>
                            <input type="text" id="province_display" value="{{ old('province') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                readonly>
                            <input type="hidden" id="province" name="province" value="{{ old('province') }}">
                        </div>
                    </div>

                    <!-- Leaflet Location Integration -->
                    <div class="col-span-full space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-900">Location (Pin on map)</label>
                            <button type="button" id="get-location-btn"
                                class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Read Current Location
                            </button>
                        </div>

                        <div id="map"
                            style="height: 400px; width: 100%; border-radius: 0.5rem; z-index: 0; border: 1px solid #d1d5db; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        </div>

                        <!-- Distance Alert -->
                        <div id="distance-alert" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert">
                            <span class="font-medium" id="distance-alert-title"></span> <span
                                id="distance-alert-message"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block mb-2 text-sm font-medium text-gray-900">Latitude</label>
                                <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    readonly>
                            </div>
                            <div>
                                <label for="longitude"
                                    class="block mb-2 text-sm font-medium text-gray-900">Longitude</label>
                                <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="location_image" id="location_image">
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" id="next-btn"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 focus:outline-none">Next:
                        Documents</button>
                </div>
            </div>

            <!-- Part 2: Documents -->
            <div id="part-2" class="space-y-6 hidden">
                <h2 class="text-xl font-semibold text-gray-900 border-b pb-2">Bagian 2 : Hubungan Debitur</h2>

                <div id="spouse_container" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden border-b pb-6 mb-6">
                    <h3 class="col-span-full text-lg font-medium text-gray-900">Identitas Pasangan</h3>
                    <div>
                        <label for="spouse_name" class="block mb-2 text-sm font-medium text-gray-900">Nama Pasangan</label>
                        <input type="text" id="spouse_name" name="spouse_name" value="{{ old('spouse_name') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="spouse_no_id" class="block mb-2 text-sm font-medium text-gray-900">No KTP
                            Pasangan</label>
                        <input type="text" id="spouse_no_id" name="spouse_no_id" value="{{ old('spouse_no_id') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="spouse_pob" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir
                            Pasangan</label>
                        <input type="text" id="spouse_pob" name="spouse_pob" value="{{ old('spouse_pob') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="spouse_dob" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir
                            Pasangan</label>
                        <input type="date" id="spouse_dob" name="spouse_dob" value="{{ old('spouse_dob') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="spouse_relation" class="block mb-2 text-sm font-medium text-gray-900">Relasi dengan
                            Debitur</label>
                        <input type="text" id="spouse_relation" name="spouse_relation" value="{{ old('spouse_relation') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            placeholder="e. g. Istri / Suami">
                    </div>
                    <div>
                        <label for="spouse_description"
                            class="block mb-2 text-sm font-medium text-gray-900">Keterangan</label>
                        <textarea id="spouse_description" name="spouse_description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">{{ old('spouse_description') }}</textarea>
                    </div>
                    <div>
                        <label for="spouse_job" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan
                            Pasangan</label>
                        <input type="text" id="spouse_job" name="spouse_job" value="{{ old('spouse_job') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="spouse_education" class="block mb-2 text-sm font-medium text-gray-900">Pendidikan
                            Terakhir Pasangan</label>
                        <select id="spouse_education" name="spouse_education"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option selected disabled>Choose an option...</option>
                            <option value="Sekolah Dasar" {{ old('spouse_education') == 'Sekolah Dasar' ? 'selected' : '' }}>
                                Sekolah Dasar</option>
                            <option value="Sekolah Menengah Pertama" {{ old('spouse_education') == 'Sekolah Menengah Pertama' ? 'selected' : '' }}>Sekolah Menengah Pertama</option>
                            <option value="Sekolah Menengah Atas" {{ old('spouse_education') == 'Sekolah Menengah Atas' ? 'selected' : '' }}>Sekolah Menengah Atas</option>
                            <option value="Diploma I" {{ old('spouse_education') == 'Diploma I' ? 'selected' : '' }}>Diploma I
                            </option>
                            <option value="Diploma II" {{ old('spouse_education') == 'Diploma II' ? 'selected' : '' }}>Diploma
                                II</option>
                            <option value="Diploma III" {{ old('spouse_education') == 'Diploma III' ? 'selected' : '' }}>
                                Diploma III</option>
                            <option value="Diploma IV" {{ old('spouse_education') == 'Diploma IV' ? 'selected' : '' }}>Diploma
                                IV</option>
                            <option value="Strata 1" {{ old('spouse_education') == 'Strata 1' ? 'selected' : '' }}>Strata 1
                            </option>
                            <option value="Strata 2" {{ old('spouse_education') == 'Strata 2' ? 'selected' : '' }}>Strata 2
                            </option>
                            <option value="Strata 3" {{ old('spouse_education') == 'Strata 3' ? 'selected' : '' }}>Strata 3
                            </option>
                            <option value="Lainnya" {{ old('spouse_education') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="spouse_notelp" class="block mb-2 text-sm font-medium text-gray-900">Nomor Telepon
                            Pasangan</label>
                        <input type="text" id="spouse_notelp" name="spouse_notelp" value="{{ old('spouse_notelp') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                    </div>
                </div>



                <div class="col-span-full">
                    <label class="block mb-2 text-sm font-medium text-gray-900" for="document">Upload Dokumen Pendukung
                        (PDF/Image)</label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none bg-white/50 backdrop-blur-sm"
                        id="document" name="document" type="file">
                    <p class="mt-1 text-sm text-gray-500" id="file_input_help">PDF, DOCX, JPG, PNG (MAX. 10MB).</p>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" id="prev-btn"
                        class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Back</button>
                    <button type="submit"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 focus:outline-none">Create
                        Customer</button>
                </div>
            </div>
            <!-- 
                                === EXAMPLE INPUTS REFERENCE (Copy & Paste to use) === 

                                1. Number Input
                                <div>
                                    <label for="example_number" class="block mb-2 text-sm font-medium text-gray-900">Age / Quantity (Number)</label>
                                    <input type="number" id="example_number" name="example_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm" placeholder="e.g. 25">
                                </div>

                                2. Date Input
                                <div>
                                    <label for="example_date" class="block mb-2 text-sm font-medium text-gray-900">Date of Birth</label>
                                    <input type="date" id="example_date" name="example_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                                </div>

                                3. Select Dropdown
                                <div>
                                    <label for="example_select" class="block mb-2 text-sm font-medium text-gray-900">Gender</label>
                                    <select id="example_select" name="example_select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                                        <option selected>Choose an option...</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>

                                4. Radio Buttons
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                                    <div class="flex items-center mb-4">
                                        <input id="radio_1" type="radio" value="active" name="example_radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2">
                                        <label for="radio_1" class="ml-2 text-sm font-medium text-gray-900">Active</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="radio_2" type="radio" value="inactive" name="example_radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2">
                                        <label for="radio_2" class="ml-2 text-sm font-medium text-gray-900">Inactive</label>
                                    </div>
                                </div>

                                5. Checkbox
                                <div class="flex items-start mb-6">
                                    <div class="flex items-center h-5">
                                        <input id="example_checkbox" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300" required>
                                    </div>
                                    <label for="example_checkbox" class="ml-2 text-sm font-medium text-gray-900">I agree to the <a href="#" class="text-blue-600 hover:underline">terms and conditions</a>.</label>
                                </div>
                            -->
        </form>
    </div>

    <!-- Cropper Modal -->
    <div id="cropper-modal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Panel Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                <!-- Modal Panel -->
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all w-full max-w-2xl flex flex-col max-h-[90vh]">

                    <!-- Sticky Header with Buttons -->
                    <div
                        class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 sm:px-6 flex justify-between items-center shadow-sm">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Crop Photo (2:3 Ratio)
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

                    <!-- Scrollable Content (Image) -->
                    <div class="p-4 sm:p-6 bg-gray-50 flex-grow overflow-y-auto flex items-center justify-center">
                        <div class="relative w-full" style="height: 500px; max-height: 60vh;">
                            <img id="cropper-image" src="" alt="Crop Preview"
                                class="block max-w-full h-full object-contain mx-auto">
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 text-center border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            Drag to adjust. The selection is locked to the required report format.
                        </p>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Form Logic ---
                const part1 = document.getElementById('part-1');
                const part2 = document.getElementById('part-2');
                const nextBtn = document.getElementById('next-btn');
                const prevBtn = document.getElementById('prev-btn');

                const photoInput = document.getElementById('photo');
                const photoPreview = document.getElementById('photo-preview');
                const photoPlaceholder = document.getElementById('photo-placeholder');

                const step1Indicator = document.getElementById('step-indicator-1');
                const step2Indicator = document.getElementById('step-indicator-2');

                // Cropper Elements
                const cropperModal = document.getElementById('cropper-modal');
                const cropperImage = document.getElementById('cropper-image');
                const cropBtn = document.getElementById('crop-btn');
                const cancelCropBtn = document.getElementById('cancel-crop-btn');
                let cropper = null;

                // Photo Input Change (Trigger Cropper)
                photoInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Check if it's an image
                        if (!file.type.match('image.*')) {
                            alert('Please select an image file.');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            // Show Modal
                            cropperModal.classList.remove('hidden');
                            cropperImage.src = e.target.result;

                            // Init Cropper
                            if (cropper) {
                                cropper.destroy();
                            }
                            cropper = new Cropper(cropperImage, {
                                aspectRatio: 2 / 3, // 4x6 cm ratio
                                viewMode: 1, // Restrict crop box to canvas
                                dragMode: 'move',
                                autoCropArea: 1,
                                background: false
                            });
                        }
                        reader.readAsDataURL(file);

                        // Reset input in case cancel is clicked (so change event fires again for same file)
                        // actually we shouldn't reset it yet, wait for user action
                    }
                });

                // Cancel Crop
                cancelCropBtn.addEventListener('click', function () {
                    cropperModal.classList.add('hidden');
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    photoInput.value = ''; // Clear selection
                    photoPreview.classList.add('hidden');
                    photoPlaceholder.classList.remove('hidden');
                });

                // Confirm Crop
                cropBtn.addEventListener('click', function () {
                    if (cropper) {
                        const canvas = cropper.getCroppedCanvas({
                            // Optional: specify resolution if needed, e.g.
                            // width: 600,
                            // height: 900
                        });

                        canvas.toBlob(function (blob) {
                            // Create a new File from the blob
                            const croppedFile = new File([blob], 'cropped_photo.jpg', { type: 'image/jpeg' });

                            // Update file input using DataTransfer
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(croppedFile);
                            photoInput.files = dataTransfer.files;

                            // Update Preview
                            const dataUrl = canvas.toDataURL("image/jpeg");
                            photoPreview.src = dataUrl;
                            document.getElementById('photo_base64').value = dataUrl;
                            photoPreview.classList.remove('hidden');
                            photoPlaceholder.classList.add('hidden');

                            // Close Modal
                            cropperModal.classList.add('hidden');
                            cropper.destroy();
                            cropper = null;
                        }, 'image/jpeg');
                    }
                });

                // --- Conditional Logic for 'Jenis Debitur' ---
                const typeSelect = document.getElementById('customer_type');
                const noIdLabel = document.getElementById('no_id_label');
                const noIdContainer = document.getElementById('no_id_container');
                const genderContainer = document.getElementById('gender_container');
                const marrietalContainer = document.getElementById('marrietal_status_container');
                const motherContainer = document.getElementById('mother_name_container');
                const educationContainer = document.getElementById('education_container');
                const spouseContainer = document.getElementById('spouse_container');

                const genderInput = document.getElementById('gender');
                const marrietalInput = document.getElementById('marrietal_status');
                const motherInput = document.getElementById('mother_name');
                const educationInput = document.getElementById('education');
                const spouseInputs = spouseContainer.querySelectorAll('input, textarea');

                function toggleSpouseFields() {
                    if (marrietalInput.value === 'Menikah') {
                        spouseContainer.classList.remove('hidden');
                        // spouseInputs.forEach(input => input.setAttribute('required', 'required'));
                    } else {
                        spouseContainer.classList.add('hidden');
                        spouseInputs.forEach(input => {
                            input.removeAttribute('required');
                            input.value = ''; // Clean up if hidden
                        });
                    }
                }

                function toggleDebiturFields() {
                    if (typeSelect.value === 'Badan') {
                        // Change Label
                        noIdLabel.textContent = 'NPWP';

                        // Hide Fields
                        genderContainer.classList.add('hidden');
                        marrietalContainer.classList.add('hidden');
                        motherContainer.classList.add('hidden');
                        educationContainer.classList.add('hidden');

                        // Remove required attributes
                        genderInput.removeAttribute('required');
                        marrietalInput.removeAttribute('required');
                        motherInput.removeAttribute('required');
                        educationInput.removeAttribute('required');

                        // Reset Marital Status and hide spouse fields
                        marrietalInput.value = '';
                        toggleSpouseFields();

                    } else {
                        // Restore Label (Perorangan or Default)
                        noIdLabel.textContent = 'No KTP';

                        // Show Fields
                        genderContainer.classList.remove('hidden');
                        marrietalContainer.classList.remove('hidden');
                        motherContainer.classList.remove('hidden');
                        educationContainer.classList.remove('hidden');

                        // Restore required attributes
                        motherInput.setAttribute('required', 'required');
                        // educationInput.setAttribute('required', 'required'); // Optional
                    }
                }

                // Listen for Marital Status Change
                marrietalInput.addEventListener('change', toggleSpouseFields);



                // Init and Listen
                typeSelect.addEventListener('change', toggleDebiturFields);
                toggleDebiturFields(); // Run on load
                toggleSpouseFields(); // Ensure spouse fields check runs on load

                // Validation & Navigation
                nextBtn.addEventListener('click', function () {
                    // Check validation for Part 1
                    const requiredInputs = part1.querySelectorAll('input[required], textarea[required]');
                    let valid = true;
                    requiredInputs.forEach(input => {
                        if (!input.value) {
                            valid = false;
                            input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                            input.addEventListener('input', function () {
                                input.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                            }, { once: true });
                        }
                    });

                    if (valid) {
                        part1.classList.add('hidden');
                        part2.classList.remove('hidden');

                        // Update Step Indicator
                        step1Indicator.classList.remove('text-blue-600');
                        step1Indicator.classList.add('text-green-600');
                        step2Indicator.classList.add('text-blue-600');
                        step2Indicator.querySelector('span').classList.add('border-blue-600');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ada Form yang Belum Terisi',
                            text: 'Mohon Periksa Kembali Formulir Anda.',
                        });
                    }
                });

                prevBtn.addEventListener('click', function () {
                    part2.classList.add('hidden');
                    part1.classList.remove('hidden');

                    // Revert Step Indicator
                    step1Indicator.classList.add('text-blue-600');
                    step1Indicator.classList.remove('text-green-600');
                    step2Indicator.classList.remove('text-blue-600');
                    step2Indicator.querySelector('span').classList.remove('border-blue-600');
                });


                // --- Form Submission Confirmation ---
                const customerForm = document.getElementById('customer-form');
                customerForm.addEventListener('submit', function (e) {
                    e.preventDefault(); // Prevent default submission

                    Swal.fire({
                        title: 'Yakin Data untuk Disimpan ?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Center map on marker if exists to ensure it's in the middle
                            if (typeof marker !== 'undefined' && marker) {
                                map.setView(marker.getLatLng(), map.getZoom(), { animate: false });
                            }

                            // Show loading
                            Swal.fire({
                                title: 'Memproses Lokasi...',
                                text: 'Mohon tunggu sebentar...',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // 3 seconds delay
                            setTimeout(() => {
                                const mapElement = document.getElementById('map');
                                html2canvas(mapElement, {
                                    useCORS: true,
                                    allowTaint: false,
                                    ignoreElements: (element) => {
                                        if (element.classList && element.classList.contains('leaflet-control-zoom')) {
                                            return true;
                                        }
                                        return false;
                                    },
                                    onclone: (clonedDoc) => {
                                        // Helper to remove non-leaflet styles to avoid Tailwind oklch colors
                                        const links = clonedDoc.querySelectorAll('link[rel="stylesheet"], style');
                                        links.forEach(tag => {
                                            // Keep Leaflet CSS
                                            if (tag.tagName === 'LINK' && tag.href && tag.href.includes('leaflet')) {
                                                return;
                                            }
                                            // Remove everything else
                                            tag.remove();
                                        });
                                    },
                                    scale: 1
                                }).then(canvas => {
                                    // Create a temporary canvas for cropping
                                    const croppedCanvas = document.createElement('canvas');
                                    const targetWidth = 830;
                                    const targetHeight = 400;
                                    croppedCanvas.width = targetWidth;
                                    croppedCanvas.height = targetHeight;
                                    const ctx = croppedCanvas.getContext('2d');

                                    // Calculate center crop with robust handling for different sizes
                                    // map center is at canvas center
                                    const sx = (canvas.width - targetWidth) / 2;
                                    const sy = (canvas.height - targetHeight) / 2;

                                    // Destination coordinates
                                    let dx = 0;
                                    let dy = 0;
                                    let dWidth = targetWidth;
                                    let dHeight = targetHeight;

                                    // Source coordinates
                                    let sX = sx;
                                    let sY = sy;
                                    let sWidth = targetWidth;
                                    let sHeight = targetHeight;

                                    // Adjust if source x is negative (map smaller than target width)
                                    if (sx < 0) {
                                        dx = -sx; // Shift destination to center
                                        sX = 0;   // Start from 0 source
                                        sWidth = canvas.width; // Draw full source width
                                        dWidth = canvas.width; // Draw full source width
                                    }

                                    // Adjust if source y is negative (map smaller than target height)
                                    if (sy < 0) {
                                        dy = -sy;
                                        sY = 0;
                                        sHeight = canvas.height;
                                        dHeight = canvas.height;
                                    }

                                    // Draw white background first
                                    ctx.fillStyle = "#FFFFFF";
                                    ctx.fillRect(0, 0, targetWidth, targetHeight);

                                    // Draw cropped/centered image
                                    ctx.drawImage(canvas, sX, sY, sWidth, sHeight, dx, dy, dWidth, dHeight);

                                    const image = croppedCanvas.toDataURL("image/png");

                                    // Debug: Check image size
                                    if (image.length < 1000) {
                                        Swal.fire('Error', 'Map image is empty or too small', 'error');
                                        return;
                                    }
                                    document.getElementById('location_image').value = image;
                                    customerForm.submit();
                                }).catch(err => {
                                    console.error("Map capture failed:", err);
                                    Swal.fire('Map Capture Failed', 'Error: ' + err.message, 'error');
                                    // Fallback: Submit without map? Or let user retry?
                                    // customerForm.submit(); 
                                });
                            }, 3000);
                        }
                    });
                });

                // --- Load Old Photo if exists ---
                const oldPhotoBase64 = document.getElementById('photo_base64').value;
                if (oldPhotoBase64) {
                    photoPreview.src = oldPhotoBase64;
                    photoPreview.classList.remove('hidden');
                    photoPlaceholder.classList.add('hidden');

                    fetch(oldPhotoBase64)
                        .then(res => res.blob())
                        .then(blob => {
                            const file = new File([blob], 'cropped_photo.jpg', { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            photoInput.files = dataTransfer.files;
                        });
                }

                // --- Leaflet Map Logic ---
                const oldLat = "{{ old('latitude') }}";
                const oldLng = "{{ old('longitude') }}";
                const defaultLat = oldLat ? parseFloat(oldLat) : -7.4704747;
                const defaultLng = oldLng ? parseFloat(oldLng) : 112.4401329;

                const map = L.map('map', {
                    center: [defaultLat, defaultLng],
                    zoom: 13,
                    layers: [] // Default will be added below
                });

                // Basemaps
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

                // Set default layer
                osm.addTo(map);

                const baseMaps = {
                    "OpenStreetMap": osm,
                    "Google Streets": googleStreets,
                    "Google Satellite": googleHybrid,
                    "Esri Satellite": esriSatellite
                };

                L.control.layers(baseMaps).addTo(map);

                L.Control.geocoder({
                    defaultMarkGeocode: false
                })
                    .on('markgeocode', function (e) {
                        var bbox = e.geocode.bbox;
                        var poly = L.polygon([
                            bbox.getSouthEast(),
                            bbox.getNorthEast(),
                            bbox.getNorthWest(),
                            bbox.getSouthWest()
                        ]);
                        map.fitBounds(poly.getBounds());

                        updateMarker(e.geocode.center.lat, e.geocode.center.lng);
                        fetchAddress(e.geocode.center.lat, e.geocode.center.lng);
                    })
                    .addTo(map);

                let marker;
                if (oldLat && oldLng) {
                    marker = L.marker([defaultLat, defaultLng]).addTo(map);
                    updateDistanceAlert(defaultLat, defaultLng);
                }

                function updateMarker(lat, lng) {
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                    document.getElementById('latitude').value = lat.toFixed(8);
                    document.getElementById('longitude').value = lng.toFixed(8);
                    map.setView([lat, lng], 16);
                    updateDistanceAlert(lat, lng);
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

                    const alertBox = document.getElementById('distance-alert');
                    const alertTitle = document.getElementById('distance-alert-title');
                    const alertMessage = document.getElementById('distance-alert-message');

                    alertBox.classList.remove('hidden', 'text-green-800', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400', 'text-yellow-800', 'bg-yellow-50', 'dark:text-yellow-300', 'text-red-800', 'bg-red-50', 'dark:text-red-400');

                    if (distance < 12) {
                        alertBox.classList.add('text-green-800', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
                        alertTitle.textContent = 'Jarak Aman!';
                        alertMessage.textContent = `Jarak dari ${userOfficeBranch}: ${distance.toFixed(2)} km. Lokasi dalam jangkauan ideal.`;
                    } else if (distance >= 12 && distance <= 40) {
                        alertBox.classList.add('text-yellow-800', 'bg-yellow-50', 'dark:bg-gray-800', 'dark:text-yellow-300');
                        alertTitle.textContent = 'Perhatian!';
                        alertMessage.textContent = `Jarak dari ${userOfficeBranch}: ${distance.toFixed(2)} km. Lokasi cukup jauh, pastikan akses mudah.`;
                    } else {
                        alertBox.classList.add('text-red-800', 'bg-red-50', 'dark:bg-gray-800', 'dark:text-red-400');
                        alertTitle.textContent = 'Risiko Tinggi!';
                        alertMessage.textContent = `Jarak dari ${userOfficeBranch}: ${distance.toFixed(2)} km. Lokasi rumah debitur terlalu jauh, risiko pemantauan dan potensi kredit macet meningkat.`;
                    }
                }



                async function fetchAddress(lat, lng) {
                    try {
                        // Using OpenStreetMap Nominatim API
                        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

                        // Add a loading state if needed, or simple toast
                        // Swal.showLoading(); 

                        const response = await fetch(url, {
                            headers: {
                                'User-Agent': 'EvaluationApp/1.0' // Good practice for OSM API
                            }
                        });

                        if (!response.ok) throw new Error('Failed to fetch address');

                        const data = await response.json();
                        const addr = data.address;

                        // Populate fields - mapping OSM fields to our inputs
                        // Note: OSM fields vary by region. We try standard Indonesian mappings.

                        // Village/Desa
                        const village = addr.village || addr.suburb || addr.hamlet || '';
                        document.getElementById('village_display').value = village;
                        document.getElementById('village').value = village;

                        // District/Kecamatan
                        // hierarchy is usually: town/city -> locality(district) -> village
                        // In Indo OSM: county/municipality is often used for Regencies, and district/suburb for Kecamatan.
                        // Let's check keys: 'county', 'district', 'suburb', 'town', 'city'
                        const district = addr.city_district || addr.district || addr.county || '';
                        // Careful: addr.district in OSM often maps to Kabupaten in some places, but usually it's Kecamatan.
                        // In Indonesia structure:
                        // Province = state
                        // City/Regency = city / county
                        // District = district (Kecamatan)
                        // Village = village (Kelurahan/Desa)

                        // A better fallback chain:
                        const finalDistrict = addr.city_district || addr.district || addr.suburb || '';
                        document.getElementById('district_display').value = finalDistrict;
                        document.getElementById('district').value = finalDistrict;

                        // Regency/City
                        const regency = addr.city || addr.town || addr.county || '';
                        document.getElementById('regency_display').value = regency;
                        document.getElementById('regency').value = regency;

                        // Province
                        const finalProvince = addr.state || addr.region || '';
                        document.getElementById('province_display').value = finalProvince;
                        document.getElementById('province').value = finalProvince;

                        // Swal.close();

                    } catch (error) {
                        console.error('Error fetching address:', error);
                        // Swal.fire('Error', 'Could not get address details', 'error');
                    }
                }

                map.on('click', function (e) {
                    updateMarker(e.latlng.lat, e.latlng.lng);
                    fetchAddress(e.latlng.lat, e.latlng.lng);
                });

                // Read Current Location
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
                                locationBtn.innerHTML = `
                                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                                    </svg> Current Location Found`;
                                locationBtn.disabled = false;
                                locationBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                                locationBtn.classList.add('bg-green-600', 'hover:bg-green-700');

                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'success',
                                    title: 'Location found',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            },
                            function (error) {
                                console.error(error);
                                locationBtn.innerHTML = 'Read Current Location';
                                locationBtn.disabled = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Location Error',
                                    text: 'Unable to retrieve your location. Note: Geolocation requires a secure connection (HTTPS) when accessed via IP.',
                                });
                            }
                        );
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Supported',
                            text: 'Geolocation is not supported by your browser.',
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection