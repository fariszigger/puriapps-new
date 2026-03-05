@extends('layouts.dashboard')

@section('title', 'Manajemen Role')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Manajemen Role</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Role</h1>
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Role
            </a>
        </div>

        <div class="bg-white/50 backdrop-blur-md rounded-xl border border-white/50 shadow-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $role)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-sm font-semibold ring-1 ring-inset
                                                    {{ $role->name === 'Admin' ? 'bg-purple-50 text-purple-700 ring-purple-600/20' :
                        ($role->name === 'Kabag' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' :
                            ($role->name === 'AO' ? 'bg-green-50 text-green-700 ring-green-600/20' :
                                'bg-gray-50 text-gray-700 ring-gray-600/20')) }}">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(5) as $permission)
                                                <span
                                                    class="px-1.5 py-0.5 text-[10px] bg-gray-100 text-gray-600 rounded">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 5)
                                                <span
                                                    class="px-1.5 py-0.5 text-[10px] bg-blue-100 text-blue-600 rounded font-medium">+{{ $role->permissions->count() - 5 }}
                                                    lainnya</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $role->users()->count() }} user
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('roles.edit', $role) }}"
                                                class="p-2 text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors"
                                                title="Edit Role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @if($role->users()->count() === 0)
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Hapus role {{ $role->name }}?')"
                                                        class="p-2 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                                        title="Hapus Role">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection