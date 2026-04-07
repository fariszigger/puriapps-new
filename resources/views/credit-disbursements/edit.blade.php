@extends('layouts.dashboard')

@section('title', 'Edit Pencairan')

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
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full max-w-2xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2.5 bg-orange-100 rounded-xl text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Pencairan</h1>
                <p class="text-sm text-gray-500">Perbarui data pencairan kredit.</p>
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

        <form action="{{ route('credit-disbursements.update', $disbursement->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900">Account Officer (AO) <span class="text-red-500">*</span></label>
                <select id="user_id" name="user_id" required
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm">
                    <option value="">Pilih AO</option>
                    @foreach($aoUsers as $ao)
                        <option value="{{ $ao->id }}" {{ old('user_id', $disbursement->user_id) == $ao->id ? 'selected' : '' }}>
                            {{ $ao->name }} {{ $ao->code ? '(' . $ao->code . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="customer_name" class="block mb-2 text-sm font-medium text-gray-900">Nama Nasabah <span class="text-red-500">*</span></label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $disbursement->customer_name) }}" required
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                    placeholder="Masukkan nama nasabah">
            </div>

            <div>
                <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pencairan (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 text-sm font-medium">Rp</span>
                    </div>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $disbursement->amount) }}" required min="0" step="1"
                        class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 pl-10 backdrop-blur-sm"
                        placeholder="0">
                </div>
                <p class="mt-1 text-xs text-gray-500">Target pencairan per AO: Rp 400.000.000 / bulan</p>
            </div>

            <div>
                <label for="disbursement_date" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pencairan <span class="text-red-500">*</span></label>
                <input type="date" id="disbursement_date" name="disbursement_date" value="{{ old('disbursement_date', $disbursement->disbursement_date->format('Y-m-d')) }}" required
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm">
            </div>

            <div>
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-3 backdrop-blur-sm"
                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $disbursement->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 transition-all shadow-lg hover:shadow-emerald-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui
                </button>
                <a href="{{ route('credit-disbursements.index') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white/60 border border-gray-300 rounded-lg hover:bg-white/80 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
