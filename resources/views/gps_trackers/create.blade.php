@extends('layouts.dashboard')

@section('title', 'Tambah GPS')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <a href="{{ route('gps-trackers.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Manajemen GPS</span>
        </a>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah GPS</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="px-4 py-6 md:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Tambah GPS Baru</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden max-w-2xl">
            <form action="{{ route('gps-trackers.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama/Label GPS</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('name') border-red-500 @enderror" placeholder="Contoh: Kendaraan Operasional 01" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="imei" class="block mb-2 text-sm font-medium text-gray-900">IMEI TrackSolidPro</label>
                    <input type="text" name="imei" id="imei" value="{{ old('imei') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 font-mono @error('imei') border-red-500 @enderror" placeholder="Contoh: 867010070425674" required>
                    <p class="mt-1 text-xs text-gray-500">IMEI harus berupa angka unik yang terdaftar di TrackSolidPro.</p>
                    @error('imei')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900">Assign/Pinjamkan Ke (Account Officer)</label>
                    <select name="user_id" id="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">-- Tidak Dipinjamkan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status GPS</label>
                    <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 border-t pt-6 bg-gray-50 -mx-6 -mb-6 px-6 pb-6">
                    <a href="{{ route('gps-trackers.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 shadow-sm">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-sm px-5 py-2.5 shadow-lg hover:shadow-blue-500/30">Simpan GPS Tracker</button>
                </div>
            </form>
        </div>
    </div>
@endsection
