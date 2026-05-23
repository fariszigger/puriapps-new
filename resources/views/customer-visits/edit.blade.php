@extends('layouts.dashboard')

@section('title', 'Edit Kunjungan Nasabah')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
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
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Kunjungan</span>
        </div>
    </li>
@endsection

@section('content')
    <div x-data="editForm()">
        <div
            class="w-full max-w-4xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-24">

            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6">Edit Kunjungan Nasabah</h1>

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer-visits.update', $visit->id) }}" method="POST" enctype="multipart/form-data"
                id="edit-form">
                @csrf
                @method('PUT')

                {{-- Hidden fields --}}
                <input type="hidden" name="photo_base64" id="photo_base64" value="{{ old('photo_base64') }}">
                <input type="hidden" name="photo_rumah_base64" id="photo_rumah_base64" value="{{ old('photo_rumah_base64') }}">
                <input type="hidden" name="photo_orang_base64" id="photo_orang_base64" value="{{ old('photo_orang_base64') }}">
                <input type="hidden" id="kondisi_saat_ini_hidden" name="kondisi_saat_ini"
                    value="{{ old('kondisi_saat_ini', $visit->kondisi_saat_ini) }}">
                <input type="hidden" id="rencana_penyelesaian_hidden" name="rencana_penyelesaian"
                    value="{{ old('rencana_penyelesaian', $visit->rencana_penyelesaian) }}">


                <div class="space-y-8">

                    {{-- ================= 1. CUSTOMER INFO (Read-Only) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Data Nasabah</h2>

                        <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-200 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 rounded-bl-full opacity-50"></div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-md font-bold text-blue-900">Data Nasabah</h3>
                                <span
                                    class="ml-auto px-3 py-1 text-xs font-bold bg-blue-200 text-blue-800 rounded-full">Penagihan
                                    Ke-{{ $visit->penagihan_ke }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white/60 rounded-lg p-3">
                                    <span
                                        class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Nama
                                        Lengkap</span>
                                    <span class="font-medium text-gray-700">{{ $visit->customer->name ?? '-' }}</span>
                                </div>
                                <div class="bg-white/60 rounded-lg p-3">
                                    <span
                                        class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">KTP
                                        / Identitas</span>
                                    <span
                                        class="font-medium text-gray-700 font-mono">{{ $visit->customer->identity_number ?? '-' }}</span>
                                </div>
                                <div class="bg-white/60 rounded-lg p-3">
                                    <span
                                        class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Alamat</span>
                                    <span class="font-medium text-gray-700">{{ $visit->customer->address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 1.5. SPK NUMBER ================= --}}
                    <div class="space-y-4">
                        <label for="spk_number" class="block mb-2 text-sm font-medium text-gray-900">Nomor SPK / Rekening
                            Kredit</label>
                        <input type="text" id="spk_number" name="spk_number"
                            value="{{ old('spk_number', $visit->spk_number) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                            placeholder="Masukkan Nomor SPK / Rekening Kredit">
                    </div>

                    {{-- ================= 1.7. AO PENDAMPING (Read-Only) ================= --}}
                    @if($visit->accompanying_names)
                    <div class="space-y-4 hidden">
                        <label class="block mb-2 text-sm font-medium text-gray-900">AO Pendamping</label>
                        <input type="text" value="{{ $visit->accompanying_names }}" readonly
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">
                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Data pendamping dicatat saat kunjungan dibuat dan tidak dapat diubah.
                        </p>
                    </div>
                    @endif

                    {{-- ================= 2. ADDRESS (Read-Only) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Alamat & Lokasi
                            Kunjungan</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Alamat Lengkap</label>
                                <textarea rows="3" readonly
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-300 cursor-not-allowed">{{ $visit->address }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Kelurahan / Desa</label>
                                    <input type="text" value="{{ $visit->village }}" readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Kecamatan</label>
                                    <input type="text" value="{{ $visit->district }}" readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Kota / Kabupaten</label>
                                    <input type="text" value="{{ $visit->regency }}" readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Provinsi</label>
                                    <input type="text" value="{{ $visit->province }}" readonly
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                </div>
                            </div>

                            @if($visit->latitude && $visit->longitude)
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Latitude</label>
                                        <input type="text" value="{{ $visit->latitude }}" readonly
                                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Longitude</label>
                                        <input type="text" value="{{ $visit->longitude }}" readonly
                                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ================= 3. KOLEKTIBILITAS ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Kolektibilitas
                        </h2>
                        <select name="kolektibilitas" required x-model="kol"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            <option value="" disabled>Pilih Kolektibilitas...</option>
                            <option value="1">1 - Lancar</option>
                            <option value="2">2 - DPK</option>
                            <option value="3">3 - Kurang Lancar</option>
                            <option value="4">4 - Diragukan</option>
                            <option value="5">5 - Macet</option>
                        </select>

                        {{-- Baki Debet (only for KL/Diragukan/Macet) --}}
                        <div x-transition class="mt-3">
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
                            <option value="Tidak ada yang bisa ditemui">Tidak ada yang bisa ditemui</option>
                        </select>

                        <div x-show="ketemuDengan && ketemuDengan !== 'Debitur' && ketemuDengan !== 'Tidak ada yang bisa ditemui'" x-transition class="mt-3">
                            <label for="nama_orang_ditemui" class="block mb-2 text-sm font-medium text-gray-900">Nama Orang
                                yang Ditemui</label>
                            <input type="text" id="nama_orang_ditemui" name="nama_orang_ditemui"
                                value="{{ old('nama_orang_ditemui', $visit->nama_orang_ditemui) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                placeholder="Masukkan nama orang yang ditemui...">
                        </div>
                    </div>

                    {{-- ================= 5. KONDISI SAAT INI (Rich Text) ================= --}}
                    <div class="space-y-4" x-transition>
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
                        <div id="kondisi-editor" class="bg-white rounded-lg">
                            {!! old('kondisi_saat_ini', $visit->kondisi_saat_ini) !!}
                        </div>
                    </div>

                    {{-- ================= 6. RENCANA PENYELESAIAN (Rich Text) ================= --}}
                    <div class="space-y-4" x-transition>
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
                        <div id="rencana-editor" class="bg-white rounded-lg">
                            {!! old('rencana_penyelesaian', $visit->rencana_penyelesaian) !!}
                        </div>
                    </div>

                    {{-- ================= 7. HASIL PENAGIHAN ================= --}}
                    <div class="space-y-4" x-transition>
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Hasil Penagihan
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center gap-6 flex-wrap">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="hasil_penagihan" value="bayar" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Bayar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="hasil_penagihan" value="janji_bayar" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Janji Bayar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="hasil_penagihan" value="tidak_ada_janji" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Tidak Ada Janji</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="hasil_penagihan" value="janji_lainnya" x-model="hasilPenagihan"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-900">Janji Lainnya</span>
                                </label>
                            </div>

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

                            <div x-show="hasilPenagihan === 'janji_bayar'" x-transition class="space-y-3">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Janji Bayar</label>
                                    <input type="date" name="tanggal_janji_bayar"
                                        value="{{ old('tanggal_janji_bayar', $visit->tanggal_janji_bayar) }}"
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

                            {{-- Janji Lainnya: Show text input --}}
                            <div x-show="hasilPenagihan === 'janji_lainnya'" x-transition class="space-y-3">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi Janji Lainnya</label>
                                    <textarea name="janji_lainnya_desc" rows="3"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                        placeholder="Tuliskan janji nasabah di sini...">{{ old('janji_lainnya_desc', $visit->janji_lainnya_desc) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= FOTO SECTIONS ================= --}}
                    @php
                        $canEditPhotos = auth()->id() === $visit->user_id && $visit->created_at->isToday();
                        $showMaxDate = $visit->created_at->isoFormat('D MMMM YYYY') . ' 23:59';
                    @endphp

                    {{-- ================= 8. FOTO KUNJUNGAN ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Kunjungan</h2>

                        <div class="flex justify-center">
                            <div class="w-full max-w-md space-y-3">
                                {{-- Preview --}}
                                <div class="h-64 border-2 {{ $canEditPhotos ? 'border-blue-300' : 'border-gray-300' }} rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 relative" id="preview-wrap-photo">
                                    @if($visit->photo_path)
                                        <img class="w-full h-full object-cover" id="img-preview-photo"
                                            src="{{ route('media.customer-visits', ['type' => 'photos', 'filename' => basename($visit->photo_path)]) }}"
                                            alt="Foto Kunjungan">
                                    @else
                                        <div class="text-center text-gray-400" id="placeholder-photo">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-xs font-medium">Tidak ada foto kunjungan</p>
                                        </div>
                                    @endif
                                    @if(!$canEditPhotos)
                                        <div class="absolute top-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none">
                                            Hanya Lihat
                                        </div>
                                    @else
                                        <div class="absolute top-2 right-2 bg-blue-600/80 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
                                            Bisa Diubah
                                        </div>
                                    @endif
                                </div>

                                @if($canEditPhotos)
                                    {{-- Upload input --}}
                                    <label for="photo"
                                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-blue-300 border-dashed rounded-lg cursor-pointer bg-blue-50 hover:bg-blue-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            <p class="text-xs text-blue-600 font-semibold">Klik untuk ganti foto kunjungan</p>
                                            <p class="text-xs text-gray-400">JPEG, PNG, GIF — maks. 10MB</p>
                                        </div>
                                        <input id="photo" name="photo" type="file" class="hidden photo-input" data-target="photo" accept="image/*" capture="environment">
                                    </label>
                                    <p class="text-xs text-amber-600 flex items-center gap-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Foto hanya dapat diperbarui maksimum {{ $showMaxDate }}. Lewat waktu ini opsi ini akan terkunci.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ================= 9. FOTO RUMAH DEBITUR ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Rumah Debitur</h2>

                        <div class="flex justify-center">
                            <div class="w-full max-w-md space-y-3">
                                <div class="h-64 border-2 {{ $canEditPhotos ? 'border-blue-300' : 'border-gray-300' }} rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 relative" id="preview-wrap-rumah">
                                    @if($visit->photo_rumah_path)
                                        <img class="w-full h-full object-cover" id="img-preview-rumah"
                                            src="{{ route('media.customer-visits', ['type' => 'photos', 'filename' => basename($visit->photo_rumah_path)]) }}"
                                            alt="Foto Rumah">
                                    @else
                                        <div class="text-center text-gray-400" id="placeholder-rumah">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-xs font-medium">Tidak ada foto rumah</p>
                                        </div>
                                    @endif
                                    @if(!$canEditPhotos)
                                        <div class="absolute top-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none">
                                            Hanya Lihat
                                        </div>
                                    @else
                                        <div class="absolute top-2 right-2 bg-blue-600/80 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
                                            Bisa Diubah
                                        </div>
                                    @endif
                                </div>

                                @if($canEditPhotos)
                                    <label for="photo_rumah"
                                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-blue-300 border-dashed rounded-lg cursor-pointer bg-blue-50 hover:bg-blue-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            <p class="text-xs text-blue-600 font-semibold">Klik untuk ganti foto rumah debitur</p>
                                            <p class="text-xs text-gray-400">JPEG, PNG, GIF — maks. 10MB</p>
                                        </div>
                                        <input id="photo_rumah" name="photo_rumah" type="file" class="hidden photo-input" data-target="photo_rumah" accept="image/*" capture="environment">
                                    </label>
                                    <p class="text-xs text-amber-600 flex items-center gap-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Foto hanya dapat diperbarui maksimum {{ $showMaxDate }}. Lewat waktu ini opsi ini akan terkunci.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ================= 10. FOTO ORANG YANG DITEMUI ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Foto Orang yang Ditemui</h2>

                        <div class="flex justify-center">
                            <div class="w-full max-w-md space-y-3">
                                <div class="h-64 border-2 {{ $canEditPhotos ? 'border-blue-300' : 'border-gray-300' }} rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 relative" id="preview-wrap-orang">
                                    @if($visit->photo_orang_path)
                                        <img class="w-full h-full object-cover" id="img-preview-orang"
                                            src="{{ route('media.customer-visits', ['type' => 'photos', 'filename' => basename($visit->photo_orang_path)]) }}"
                                            alt="Foto Orang">
                                    @else
                                        <div class="text-center text-gray-400" id="placeholder-orang">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-xs font-medium">Tidak ada foto orang yang ditemui</p>
                                        </div>
                                    @endif
                                    @if(!$canEditPhotos)
                                        <div class="absolute top-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none">
                                            Hanya Lihat
                                        </div>
                                    @else
                                        <div class="absolute top-2 right-2 bg-blue-600/80 text-white text-xs px-2 py-1 rounded backdrop-blur-sm pointer-events-none flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
                                            Bisa Diubah
                                        </div>
                                    @endif
                                </div>

                                @if($canEditPhotos)
                                    <label for="photo_orang"
                                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-blue-300 border-dashed rounded-lg cursor-pointer bg-blue-50 hover:bg-blue-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            <p class="text-xs text-blue-600 font-semibold">Klik untuk ganti foto orang yang ditemui</p>
                                            <p class="text-xs text-gray-400">JPEG, PNG, GIF — maks. 10MB</p>
                                        </div>
                                        <input id="photo_orang" name="photo_orang" type="file" class="hidden photo-input" data-target="photo_orang" accept="image/*" capture="environment">
                                    </label>
                                    <p class="text-xs text-amber-600 flex items-center gap-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Foto hanya dapat diperbarui maksimum {{ $showMaxDate }}. Lewat waktu ini opsi ini akan terkunci.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ================= SUBMIT ================= --}}
                    <div class="flex justify-end pt-6 mt-8 border-t-2 border-gray-100 gap-3">
                        <a href="{{ route('customer-visits.index') }}"
                            class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-bold rounded-xl text-lg px-8 py-4 inline-flex items-center gap-2 transition-all">
                            Batal
                        </a>
                        <button type="submit" id="submit-btn"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-lg px-10 py-4 focus:outline-none inline-flex items-center gap-3 shadow-xl hover:shadow-blue-500/40 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Perbarui Kunjungan
                        </button>
                    </div>
                </div>
            </form>

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



    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            function editForm() {
                return {
                    showTemplateModal: false,
                    activeTemplateTarget: 'kondisi',
                    kol: '{{ old('kolektibilitas', $visit->kolektibilitas) }}',
                    ketemuDengan: '{{ old('ketemu_dengan', $visit->ketemu_dengan) }}',
                    bakiRaw: '{{ old('baki_debet', $visit->baki_debet ?? '0') }}',
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
                    hasilPenagihan: '{{ old("hasil_penagihan", $visit->hasil_penagihan ?? "") }}',
                    jumlahBayar: {{ old('jumlah_bayar', $visit->jumlah_bayar ?? 0) ?: 0 }},
                    displayJumlahBayar: '',
                    jumlahPembayaran: {{ old('jumlah_pembayaran', $visit->jumlah_pembayaran ?? 0) ?: 0 }},
                    displayJumlahPembayaran: '',
                    init() {
                        if (this.jumlahBayar > 0) {
                            this.displayJumlahBayar = this.formatNumber(this.jumlahBayar);
                        }
                        if (this.jumlahPembayaran > 0) {
                            this.displayJumlahPembayaran = this.formatNumber(this.jumlahPembayaran);
                        }
                        if (this.bakiRaw > 0) {
                            this.bakiDisplay = this.formatNumber(this.bakiRaw);
                        }
                    },
                    formatNumber(n) {
                        return new Intl.NumberFormat('id-ID').format(n);
                    },
                    updateJumlahBayar(val) {
                        const num = parseInt(val.replace(/\D/g, '')) || 0;
                        this.jumlahBayar = num;
                        this.displayJumlahBayar = this.formatNumber(num);
                    },
                    updateJumlahPembayaran(val) {
                        const num = parseInt(val.replace(/\D/g, '')) || 0;
                        this.jumlahPembayaran = num;
                        this.displayJumlahPembayaran = this.formatNumber(num);
                    },
                    updateBaki(v) {
                        const n = parseInt(v.replace(/\D/g, '')) || 0;
                        this.bakiRaw = n;
                        this.bakiDisplay = n ? this.formatNumber(n) : '';
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Form submit with Swal confirmation (needed to reference quill later)
                const editFormEl = document.getElementById('edit-form');

                // Quill editors (assign to window to be accessible from Alpine method and form submit block)
                window.kondisiQuill = new Quill('#kondisi-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }]]
                    }
                });

                window.rencanaQuill = new Quill('#rencana-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }]]
                    }
                });

                // ---- CropperJS Logic ----
                const cropperModal = document.getElementById('cropper-modal');
                const cropperImage = document.getElementById('cropper-image');
                const cropBtn = document.getElementById('crop-btn');
                const cancelCropBtn = document.getElementById('cancel-crop-btn');
                let cropper = null;
                let activeTarget = null;

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
                        input.value = '';
                    }
                    activeTarget = null;
                });

                cropBtn.addEventListener('click', function () {
                    if (cropper && activeTarget) {
                        const canvas = cropper.getCroppedCanvas();
                        const input = document.getElementById(activeTarget);
                        const suffix = activeTarget === 'photo_rumah' ? 'rumah' : (activeTarget === 'photo_orang' ? 'orang' : 'photo');
                        const wrapper = document.getElementById('preview-wrap-' + suffix);
                        let preview = document.getElementById('img-preview-' + suffix);
                        let placeholder = document.getElementById('placeholder-' + suffix);
                        const base64Input = document.getElementById(activeTarget + '_base64');

                        // Add watermark
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
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.id = 'img-preview-' + suffix;
                                preview.className = 'w-full h-full object-cover';
                                wrapper.prepend(preview);
                            }
                            preview.src = dataUrl;
                            if (base64Input) base64Input.value = dataUrl;
                            if (placeholder) placeholder.classList.add('hidden');

                            cropperModal.classList.add('hidden');
                            cropper.destroy();
                            cropper = null;
                            activeTarget = null;
                        }, 'image/jpeg');
                    }
                });

                // Load old photo base64 values if exist
                ['photo', 'photo_rumah', 'photo_orang'].forEach(id => {
                    const base64Input = document.getElementById(id + '_base64');
                    const base64 = base64Input ? base64Input.value : null;
                    if (base64) {
                        const suffix = id === 'photo_rumah' ? 'rumah' : (id === 'photo_orang' ? 'orang' : 'photo');
                        const preview = document.getElementById('img-preview-' + suffix);
                        const placeholder = document.getElementById('placeholder-' + suffix);
                        if (preview) {
                            preview.src = base64;
                            preview.classList.remove('hidden');
                        }
                        if (placeholder) placeholder.classList.add('hidden');
                    }
                });

                editFormEl.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Perbarui Kunjungan?',
                        text: 'Pastikan semua data sudah benar sebelum menyimpan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1d4ed8',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Perbarui',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        // Sync Quill content
                        document.getElementById('kondisi_saat_ini_hidden').value = window.kondisiQuill.root.innerHTML;
                        document.getElementById('rencana_penyelesaian_hidden').value = window.rencanaQuill.root.innerHTML;

                        editFormEl.submit();
                    });
                });
            });
        </script>
    @endpush
@endsection