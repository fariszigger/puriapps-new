@extends('layouts.dashboard')

@section('title', 'Edit Kunjungan Nasabah')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
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
                <input type="hidden" id="kondisi_saat_ini_hidden" name="kondisi_saat_ini"
                    value="{{ old('kondisi_saat_ini', $visit->kondisi_saat_ini) }}">
                <input type="hidden" id="rencana_penyelesaian_hidden" name="rencana_penyelesaian"
                    value="{{ old('rencana_penyelesaian', $visit->rencana_penyelesaian) }}">

                <div class="space-y-8">

                    {{-- ================= 1. CUSTOMER INFO (Read-Only) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">1. Data Nasabah</h2>

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

                    {{-- ================= 2. ADDRESS (Read-Only) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">2. Alamat & Lokasi
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
                    <div class="space-y-4" x-data="{ kol: '{{ old('kolektibilitas', $visit->kolektibilitas) }}' }">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">3. Kolektibilitas
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
                        <div x-show="['3','4','5'].includes(kol)" x-transition class="mt-3"
                            x-data="{
                                bakiRaw: '{{ old('baki_debet', $visit->baki_debet ?? '') }}',
                                bakiDisplay: '',
                                init() { if(this.bakiRaw) this.bakiDisplay = new Intl.NumberFormat('id-ID').format(this.bakiRaw); },
                                updateBaki(v) {
                                    const n = parseInt(v.replace(/\D/g,'')) || 0;
                                    this.bakiRaw = n;
                                    this.bakiDisplay = n ? new Intl.NumberFormat('id-ID').format(n) : '';
                                }
                            }">
                            <label for="baki_debet_display" class="block mb-2 text-sm font-medium text-gray-900">Baki Debet (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                <input type="text" id="baki_debet_display" x-model="bakiDisplay"
                                    @input="updateBaki($event.target.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 bg-white/50 backdrop-blur-sm"
                                    placeholder="0">
                                <input type="hidden" name="baki_debet" :value="bakiRaw">
                            </div>
                        </div>
                    </div>

                    {{-- ================= 4. KETEMU DENGAN ================= --}}
                    <div class="space-y-4" x-data="{ ketemuDengan: '{{ old('ketemu_dengan', $visit->ketemu_dengan) }}' }">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">4. Ketemu Dengan
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

                        <div x-show="ketemuDengan && ketemuDengan !== 'Debitur'" x-transition class="mt-3">
                            <label for="nama_orang_ditemui" class="block mb-2 text-sm font-medium text-gray-900">Nama Orang
                                yang Ditemui</label>
                            <input type="text" id="nama_orang_ditemui" name="nama_orang_ditemui"
                                value="{{ old('nama_orang_ditemui', $visit->nama_orang_ditemui) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                placeholder="Masukkan nama orang yang ditemui...">
                        </div>
                    </div>

                    {{-- ================= 5. KONDISI SAAT INI (Rich Text) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">5. Kondisi Saat Ini
                        </h2>
                        <div id="kondisi-editor" class="bg-white rounded-lg">
                            {!! old('kondisi_saat_ini', $visit->kondisi_saat_ini) !!}</div>
                    </div>

                    {{-- ================= 6. RENCANA PENYELESAIAN (Rich Text) ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">6. Rencana
                            Penyelesaian</h2>
                        <div id="rencana-editor" class="bg-white rounded-lg">
                            {!! old('rencana_penyelesaian', $visit->rencana_penyelesaian) !!}</div>
                    </div>

                    {{-- ================= 7. HASIL PENAGIHAN ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">7. Hasil Penagihan
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center gap-6">
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

                            <div x-show="hasilPenagihan === 'janji_bayar'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Janji Bayar</label>
                                <input type="date" name="tanggal_janji_bayar"
                                    value="{{ old('tanggal_janji_bayar', $visit->tanggal_janji_bayar) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ================= 8. PHOTO ================= --}}
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">8. Foto Kunjungan
                        </h2>

                        <div class="flex justify-center">
                            <div id="photo-upload-area"
                                class="w-112 h-72 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group relative"
                                onclick="document.getElementById('photo').click()">
                                @if($visit->photo_path)
                                    <img id="photo-preview" class="w-full h-full object-cover"
                                        src="{{ route('media.customer-visits', ['type' => 'photos', 'filename' => basename($visit->photo_path)]) }}"
                                        alt="Photo Preview">
                                @else
                                    <img id="photo-preview" class="w-full h-full object-cover hidden" alt="Photo Preview">
                                @endif
                                <div id="photo-placeholder"
                                    class="text-center text-gray-400 group-hover:text-blue-500 transition-colors {{ $visit->photo_path ? 'hidden' : '' }}">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs font-medium">Tap untuk Upload</p>
                                    <p class="text-[10px] mt-1">JPG, PNG • 16:9</p>
                                </div>
                            </div>
                            <input class="hidden" id="photo" name="photo" type="file" accept="image/*">
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
                                Crop Photo (16:9 Ratio)
                            </h3>
                            <div class="flex space-x-2">
                                <button type="button" id="cancel-crop-btn"
                                    class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="button" id="crop-btn"
                                    class="inline-flex justify-center rounded-lg border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
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
                            <p class="text-xs text-gray-500">Drag to adjust. The selection is locked to the required report
                                format.</p>
                        </div>
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
                    hasilPenagihan: '{{ old("hasil_penagihan", $visit->hasil_penagihan ?? "") }}',
                    jumlahBayar: {{ old('jumlah_bayar', $visit->jumlah_bayar ?? 0) ?: 0 }},
                    displayJumlahBayar: '',
                    init() {
                        if (this.jumlahBayar > 0) {
                            this.displayJumlahBayar = this.formatNumber(this.jumlahBayar);
                        }
                    },
                    formatNumber(n) {
                        return new Intl.NumberFormat('id-ID').format(n);
                    },
                    updateJumlahBayar(val) {
                        const num = parseInt(val.replace(/\D/g, '')) || 0;
                        this.jumlahBayar = num;
                        this.displayJumlahBayar = this.formatNumber(num);
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Quill editors
                const kondisiQuill = new Quill('#kondisi-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }]]
                    }
                });

                const rencanaQuill = new Quill('#rencana-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }]]
                    }
                });

                // CropperJS for photo
                let cropper = null;
                const photoInput = document.getElementById('photo');
                const cropperModal = document.getElementById('cropper-modal');
                const cropperImage = document.getElementById('cropper-image');

                photoInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        cropperImage.src = event.target.result;
                        cropperModal.classList.remove('hidden');

                        if (cropper) cropper.destroy();
                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 16 / 9,
                            viewMode: 1,
                            autoCropArea: 1,
                        });
                    };
                    reader.readAsDataURL(file);
                });

                document.getElementById('crop-btn').addEventListener('click', function () {
                    if (!cropper) return;
                    const canvas = cropper.getCroppedCanvas({ width: 800, height: 450 });

                    // Add watermark with AO name and datetime
                    const ctx = canvas.getContext('2d');
                    const aoName = '{{ auth()->user()->name }}';
                    const now = new Date();
                    const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + ' ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    const watermarkText = aoName + ' \u2014 ' + dateStr;

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

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    const preview = document.getElementById('photo-preview');
                    preview.src = dataUrl;
                    preview.classList.remove('hidden');
                    document.getElementById('photo-placeholder')?.classList.add('hidden');

                    canvas.toBlob(function (blob) {
                        const file = new File([blob], 'cropped_photo.jpg', { type: 'image/jpeg' });
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        photoInput.files = dt.files;
                    }, 'image/jpeg', 0.9);

                    cropperModal.classList.add('hidden');
                    cropper.destroy();
                    cropper = null;
                });

                document.getElementById('cancel-crop-btn').addEventListener('click', function () {
                    cropperModal.classList.add('hidden');
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    photoInput.value = '';
                });

                // Form submit with Swal confirmation
                const editFormEl = document.getElementById('edit-form');
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
                        document.getElementById('kondisi_saat_ini_hidden').value = kondisiQuill.root.innerHTML;
                        document.getElementById('rencana_penyelesaian_hidden').value = rencanaQuill.root.innerHTML;

                        editFormEl.submit();
                    });
                });
            });
        </script>
    @endpush
@endsection