@extends('layouts.dashboard')

@section('title', 'Tambah Role')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('roles.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">Manajemen Role</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tambah Role</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-6">Tambah Role Baru</h1>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Role</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    placeholder="Contoh: Supervisor" required>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-4">Permissions</h3>
                <div class="space-y-4">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">{{ ucfirst($group) }}</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="text-sm text-gray-700 group-hover:text-gray-900">
                                            {{ ucfirst(str_replace(' ' . $group, '', $permission->name)) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end pt-4 space-x-3">
                <a href="{{ route('roles.index') }}"
                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
                <button type="submit"
                    class="text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-sm px-5 py-2.5">Buat Role</button>
            </div>
        </form>
    </div>
@endsection
