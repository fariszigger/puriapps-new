@extends('layouts.dashboard')

@section('title', 'Daftar Debitur')

@section('breadcrumb-items')
  <li class="inline-flex items-center">
    <div class="flex items-center">
      <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 6 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
      </svg>
      <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Daftar Debitur</span>
    </div>
  </li>
@endsection

@section('content')
  <div class="w-full p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Daftar Debitur</h1>
      <div class="flex items-center gap-2">
        @can('create customers')
          <a href="{{ route('customers.create') }}"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Debitur Baru
          </a>
        @endcan
      </div>
    </div>

    <livewire:customer-table />
  </div>
@endsection