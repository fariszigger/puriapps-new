@extends('layouts.dashboard')

@section('title', 'Laporan Hasil Kinerja')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Laporan Hasil Kinerja</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-indigo-100/50 rounded-xl text-indigo-600 border border-indigo-200 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-gray-900">Laporan Hasil Kinerja</h1>
                    <p class="text-gray-600 mt-1">Pantau statistik kunjungan Account Officer (AO) berdasarkan periode waktu.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <livewire:performance-report />
    </div>
@endsection