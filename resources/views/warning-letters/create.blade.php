@extends('layouts.dashboard')

@section('title', 'Buat Surat')

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
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Buat Surat</span>
        </div>
    </li>
@endsection

@section('content')
    <div x-data="warningLetterForm()" class="w-full max-w-4xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-24">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Buat Surat</h1>
            @php
                $typeBadges = [
                    'sp1' => ['label' => 'Surat Peringatan I', 'color' => 'yellow'],
                    'sp2' => ['label' => 'Surat Peringatan II', 'color' => 'orange'],
                    'sp3' => ['label' => 'Surat Peringatan III', 'color' => 'red'],
                    'panggilan' => ['label' => 'Surat Panggilan', 'color' => 'purple'],
                ];
                $badge = $typeBadges[$type] ?? ['label' => $type, 'color' => 'gray'];
            @endphp
            <span class="px-4 py-2 text-sm font-bold rounded-xl bg-{{ $badge['color'] }}-100 text-{{ $badge['color'] }}-800 border border-{{ $badge['color'] }}-300">
                {{ $badge['label'] }}
            </span>
        </div>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Qualification Status --}}
        @if($customer && $qualification)
            <div class="p-4 mb-6 rounded-xl border-2 {{ $qualification['qualified'] ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                <div class="flex items-center gap-3">
                    @if($qualification['qualified'])
                        <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-bold text-green-800">✅ {{ $qualification['reason'] }}</span>
                    @else
                        <svg class="w-6 h-6 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-bold text-red-800">❌ {{ $qualification['reason'] }}</span>
                    @endif
                </div>
            </div>
        @endif

        <form action="{{ route('warning-letters.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            @if($previousLetter)
                <input type="hidden" name="previous_letter_id" value="{{ $previousLetter->id }}">
                <input type="hidden" name="previous_letter_number" value="{{ $previousLetter->letter_number }}">
                <input type="hidden" name="previous_letter_date" value="{{ $previousLetter->letter_date->format('Y-m-d') }}">
                <input type="hidden" name="previous_letter_amount" value="{{ $previousLetter->tunggakan_amount }}">
                <input type="hidden" name="previous_letter_deadline" value="{{ $previousLetter->deadline_date ? $previousLetter->deadline_date->format('Y-m-d') : '' }}">
            @endif

            <div class="space-y-8">

                {{-- Customer Selection --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Data Nasabah</h2>

                    @if($customer)
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Nama</span>
                                    <span class="text-base font-bold text-gray-900">{{ $customer->name }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">KTP</span>
                                    <span class="font-medium text-gray-700 font-mono">{{ $customer->identity_number ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-blue-500 uppercase tracking-wider mb-1">Alamat</span>
                                    <span class="font-medium text-gray-700">{{ $customer->address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($customers->isEmpty())
                            <div class="p-4 rounded-xl bg-orange-50 border border-orange-200 flex items-center gap-3">
                                <svg class="w-6 h-6 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-bold text-orange-800">Tidak ada nasabah yang memenuhi syarat</h3>
                                    <p class="text-sm text-orange-700 mt-1">Saat ini tidak ada nasabah yang memenuhi kriteria untuk pembuatan {{ $badge['label'] }}.</p>
                                </div>
                            </div>
                        @else
                            <div>
                                <label for="customer_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Nasabah (Yang Memenuhi Syarat)</label>
                                <select name="customer_id" id="customer_id" required x-model="selectedCustomerId" @change="checkQualification()"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                                    <option value="">-- Pilih Nasabah --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->identity_number ?? 'No KTP' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Letter Details --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Detail Surat</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="letter_number" class="block mb-2 text-sm font-medium text-gray-900">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" value="{{ old('letter_number', $generatedLetterNumber) }}" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="letter_date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Surat <span class="text-red-500">*</span></label>
                            <input type="date" id="letter_date" name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                {{-- Credit Info --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Informasi Kredit</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="credit_agreement_number" class="block mb-2 text-sm font-medium text-gray-900">Nomor Perjanjian Kredit</label>
                            <input type="text" id="credit_agreement_number" name="credit_agreement_number" 
                                value="{{ old('credit_agreement_number', $previousLetter ? $previousLetter->credit_agreement_number : '') }}"
                                {{ $previousLetter ? 'readonly' : '' }}
                                class="bg-gray-50 border border-gray-300 {{ $previousLetter ? 'text-gray-500 bg-gray-100 cursor-not-allowed' : 'text-gray-900' }} text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm"
                                placeholder="No. Perjanjian Kredit">
                        </div>
                        <div>
                            <label for="credit_agreement_date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Perjanjian Kredit</label>
                            <input type="date" id="credit_agreement_date" name="credit_agreement_date" 
                                value="{{ old('credit_agreement_date', $previousLetter ? $previousLetter->credit_agreement_date->format('Y-m-d') : '') }}"
                                {{ $previousLetter ? 'readonly' : '' }}
                                class="bg-gray-50 border border-gray-300 {{ $previousLetter ? 'text-gray-500 bg-gray-100 cursor-not-allowed' : 'text-gray-900' }} text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                {{-- Tunggakan --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Tunggakan & Batas Waktu</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tunggakan_display" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Tunggakan (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                <input type="text" id="tunggakan_display" x-model="tunggakanDisplay"
                                    @input="updateTunggakan($event.target.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 bg-white/50 backdrop-blur-sm"
                                    placeholder="0">
                                <input type="hidden" name="tunggakan_amount" :value="tunggakanRaw">
                            </div>
                        </div>
                        <div>
                            <label for="tunggakan_date" class="block mb-2 text-sm font-medium text-gray-900">Posisi Tanggal Tunggakan</label>
                            <input type="date" id="tunggakan_date" name="tunggakan_date" value="{{ old('tunggakan_date', date('Y-m-d')) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                        </div>
                        <div>
                            <label for="deadline_date" class="block mb-2 text-sm font-medium text-gray-900">Paling Lambat Tanggal</label>
                            <input type="date" id="deadline_date" name="deadline_date" value="{{ old('deadline_date') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 bg-white/50 backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900 border-b-2 border-gray-100 pb-2">Catatan Tambahan</h2>
                    <textarea id="notes" name="notes" rows="3"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white/50 backdrop-blur-sm"
                        placeholder="Catatan tambahan (opsional)...">{{ old('notes') }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end pt-6 mt-8 border-t-2 border-gray-100 gap-3">
                    <a href="{{ route('warning-letters.index') }}"
                        class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-bold rounded-xl text-sm px-6 py-3 transition-all">
                        Batal
                    </a>
                    <button type="submit" {{ ($customer && $qualification && !$qualification['qualified']) ? 'disabled' : '' }}
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-lg px-10 py-4 focus:outline-none inline-flex items-center gap-3 shadow-xl hover:shadow-blue-500/40 transition-all transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Surat
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function warningLetterForm() {
                return {
                    selectedCustomerId: '{{ $customer->id ?? '' }}',
                    tunggakanRaw: '{{ old("tunggakan_amount", "0") }}',
                    tunggakanDisplay: '',

                    init() {
                        if (this.tunggakanRaw > 0) {
                            this.tunggakanDisplay = this.formatNumber(this.tunggakanRaw);
                        }
                    },

                    updateTunggakan(value) {
                        const n = parseInt(value.replace(/\D/g, '')) || 0;
                        this.tunggakanRaw = n;
                        this.tunggakanDisplay = n ? this.formatNumber(n) : '';
                    },

                    formatNumber(value) {
                        if (!value || value === '0') return '';
                        return new Intl.NumberFormat('id-ID').format(value);
                    },

                    checkQualification() {
                        if (this.selectedCustomerId) {
                            window.location.href = `{{ route('warning-letters.create') }}?type={{ $type }}&customer_id=${this.selectedCustomerId}`;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
