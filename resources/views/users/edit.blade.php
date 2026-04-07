@extends('layouts.dashboard')

@section('title', 'Edit User: ' . $user->name)

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('users.index') }}"
                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 hover:text-blue-600">User Settings</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit User</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-6">Edit User: {{ $user->name }}</h1>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required>
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                </div>
                <div>
                    <label for="code" class="block mb-2 text-sm font-medium text-gray-900">Nickname (Code)</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $user->code) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            </div>

            <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Change Password (Leave blank to keep current)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">New Password</label>
                        <input type="password" id="password" name="password"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Confirm New
                            Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ selectedRole: '{{ old('role', $user->roles->first()?->name) }}' }">
                <div>
                    <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                    <select id="role" name="role" x-model="selectedRole"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                        @foreach(\Spatie\Permission\Models\Role::all() as $r)
                            <option value="{{ $r->name }}" {{ old('role', $user->roles->first()?->name) == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div x-show="selectedRole === 'AO'" x-transition class="md:col-span-2">
                    <label for="disbursement_target" class="block mb-2 text-sm font-medium text-gray-900">Limit Pencairan Bulanan (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="number" id="disbursement_target" name="disbursement_target"
                            value="{{ old('disbursement_target', $user->disbursement_target) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pl-10"
                            min="0" step="1000000">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Default: Rp 400.000.000. Untuk AO baru bisa disesuaikan ke Rp 250.000.000.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 space-x-3">
                <a href="{{ route('users.index') }}"
                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
                <button type="submit"
                    class="text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-sm px-5 py-2.5">Update
                    User</button>
            </div>
        </form>
    </div>
@endsection