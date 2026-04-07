@extends('layouts.dashboard')

@section('title', 'Tambah Pencairan')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('credit-disbursements.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Daftar Pencairan</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full max-w-2xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2.5 bg-emerald-100 rounded-xl text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Tambah Pencairan</h1>
                <p class="text-sm text-gray-500">Catat data pencairan kredit baru.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form x-data="calculator()" action="{{ route('credit-disbursements.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid flex-col grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900">Account Officer (AO) <span class="text-red-500">*</span></label>
                    <select id="user_id" name="user_id" required
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm">
                        <option value="">Pilih AO</option>
                        @foreach($aoUsers as $ao)
                            <option value="{{ $ao->id }}" {{ old('user_id') == $ao->id ? 'selected' : '' }}>
                                {{ $ao->name }} {{ $ao->code ? '(' . $ao->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nomor_spk" class="block mb-2 text-sm font-medium text-gray-900">Nomor SPK</label>
                    <input type="text" id="nomor_spk" name="nomor_spk" value="{{ old('nomor_spk') }}"
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                        placeholder="Contoh: SPK/2026/001">
                </div>
            </div>

            <div class="grid flex-col grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="customer_name" class="block mb-2 text-sm font-medium text-gray-900">Nama Nasabah <span class="text-red-500">*</span></label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                        placeholder="Masukkan nama nasabah">
                </div>
                <div>
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <textarea id="address" name="address" rows="1"
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                        placeholder="Alamat domisili nasabah">{{ old('address') }}</textarea>
                </div>
            </div>

            <div>
                <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pencairan (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 text-sm font-medium">Rp</span>
                    </div>
                    <input x-model="plafon" @input="calculate" type="number" id="amount" name="amount" required min="0" step="1"
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 pl-10 backdrop-blur-sm"
                        placeholder="0">
                </div>
                <p class="mt-1 text-xs text-gray-500">Target pencairan per AO: Rp 400.000.000 / bulan</p>
            </div>

            <div class="grid flex-col grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="jangka_waktu" class="block mb-2 text-sm font-medium text-gray-900">Jangka Waktu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input x-model="jangkaWaktu" @input="calculate" type="number" id="jangka_waktu" name="jangka_waktu" required min="1" step="1"
                            class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 pr-16 backdrop-blur-sm"
                            placeholder="e.g. 12">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">Bulan</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="suku_bunga" class="block mb-2 text-sm font-medium text-gray-900">Suku Bunga / Tahun <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input x-model="sukuBunga" @input="calculate" type="number" id="suku_bunga" name="suku_bunga" required min="0" step="0.01"
                            class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 pr-10 backdrop-blur-sm"
                            placeholder="e.g. 12">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid flex-col grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="jenis_pinjaman" class="block mb-2 text-sm font-medium text-gray-900">Jenis Pinjaman <span class="text-red-500">*</span></label>
                    <select x-model="jenisPinjaman" @change="calculate" id="jenis_pinjaman" name="jenis_pinjaman" required
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm">
                        <option value="flat">Flat</option>
                        <option value="anuitas">Anuitas</option>
                        <option value="musiman">Musiman</option>
                    </select>
                </div>

                <div>
                    <label for="angsuran" class="block mb-2 text-sm font-medium text-gray-900">Angsuran per Bulan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">Rp</span>
                        </div>
                        <input x-model="angsuran" type="number" id="angsuran" name="angsuran" required min="0" step="1"
                            class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 pl-10 backdrop-blur-sm"
                            placeholder="0">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Angsuran dihitung otomatis namun dapat diubah jika perlu.</p>
                </div>
            </div>

            <div>
                <label for="disbursement_date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pencairan <span class="text-red-500">*</span></label>
                <input type="date" id="disbursement_date" name="disbursement_date" value="{{ old('disbursement_date', date('Y-m-d')) }}" required
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm">
            </div>

            <div>
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                    placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 transition-all shadow-lg hover:shadow-emerald-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </button>
                <a href="{{ route('credit-disbursements.index') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white/60 border border-gray-300 rounded-lg hover:bg-white/80 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calculator', () => ({
            plafon: '{{ old('amount', '') }}',
            jangkaWaktu: '{{ old('jangka_waktu', '') }}',
            sukuBunga: '{{ old('suku_bunga', '0') }}',
            jenisPinjaman: '{{ old('jenis_pinjaman', 'flat') }}',
            angsuran: '{{ old('angsuran', '') }}',

            calculate() {
                const p = parseFloat(this.plafon.toString().replace(/\D/g, '')) || 0;
                const t = parseInt(this.jangkaWaktu) || 0;
                const r = parseFloat(this.sukuBunga) || 0;
                
                if (p === 0 || t === 0) return;
                
                const monthlyRate = (r / 100) / 12;
                let installment = 0;
                
                if (this.jenisPinjaman === 'flat') {
                    const principal = p / t;
                    const interest = p * monthlyRate;
                    installment = principal + interest;
                } else if (this.jenisPinjaman === 'anuitas') {
                    if (monthlyRate === 0) {
                        installment = p / t;
                    } else {
                        const numerator = p * monthlyRate * Math.pow(1 + monthlyRate, t);
                        const denominator = Math.pow(1 + monthlyRate, t) - 1;
                        installment = numerator / denominator;
                    }
                } else if (this.jenisPinjaman === 'musiman') {
                    installment = p * monthlyRate;
                }
                
                this.angsuran = Math.round(installment);
            }
        }))
    });
</script>
@endpush
