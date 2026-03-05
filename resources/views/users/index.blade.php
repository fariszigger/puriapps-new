@extends('layouts.dashboard')

@section('title', 'User Settings')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">User Settings</span>
        </div>
    </li>
@endsection

@section('content')
    <div x-data="{
        activeTab: window.location.hash === '#roles' ? 'roles' : 'users',
        editingRole: null,
        showCreateForm: false,
        init() {
            window.addEventListener('hashchange', () => {
                this.activeTab = window.location.hash === '#roles' ? 'roles' : 'users';
            });
        }
    }">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-1 -mb-px" aria-label="Tabs">
                <button @click="activeTab = 'users'; window.location.hash = ''"
                    :class="activeTab === 'users'
                        ? 'border-blue-500 text-blue-600 bg-blue-50/50'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 px-5 py-3 border-b-2 text-sm font-semibold transition-all rounded-t-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Users
                </button>
                <button @click="activeTab = 'roles'; window.location.hash = '#roles'"
                    :class="activeTab === 'roles'
                        ? 'border-blue-500 text-blue-600 bg-blue-50/50'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="inline-flex items-center gap-2 px-5 py-3 border-b-2 text-sm font-semibold transition-all rounded-t-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Manajemen Role
                </button>
            </nav>
        </div>

        {{-- Tab 1: Users --}}
        <div x-show="activeTab === 'users'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="p-4 sm:p-6 lg:p-8">
                <livewire:users-table />
            </div>
        </div>

        {{-- Tab 2: Manajemen Role --}}
        <div x-show="activeTab === 'roles'" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="max-w-5xl mx-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Role</h2>
                        <p class="mt-1 text-sm text-gray-500">Kelola role dan permission untuk setiap user.</p>
                    </div>
                    <button @click="showCreateForm = !showCreateForm; editingRole = null"
                        :class="showCreateForm ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-700 hover:bg-blue-800'"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white rounded-lg focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-4 h-4 mr-2 transition-transform duration-200" :class="showCreateForm ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span x-text="showCreateForm ? 'Batal' : 'Tambah Role'"></span>
                    </button>
                </div>

                {{-- Create Form --}}
                <div x-show="showCreateForm" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4"
                    class="mb-6 p-6 bg-white/60 backdrop-blur-md rounded-xl border border-white/50 shadow-xl">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </span>
                        Tambah Role Baru
                    </h3>
                    <form action="{{ route('roles.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="create-name" class="block mb-2 text-sm font-medium text-gray-900">Nama
                                Role</label>
                            <input type="text" id="create-name" name="name" value="{{ old('name') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Contoh: Supervisor" required>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Permissions</h4>
                            <div class="space-y-3">
                                @foreach ($permissions as $group => $groupPermissions)
                                    <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-200">
                                        <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                                            {{ ucfirst($group) }}</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($groupPermissions as $permission)
                                                <label class="flex items-center gap-2 cursor-pointer group">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                    <span
                                                        class="text-sm text-gray-700 group-hover:text-gray-900">
                                                        {{ ucfirst(str_replace(' ' . $group, '', $permission->name)) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end pt-2 space-x-3">
                            <button type="button" @click="showCreateForm = false"
                                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</button>
                            <button type="submit"
                                class="text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-sm px-5 py-2.5">Buat
                                Role</button>
                        </div>
                    </form>
                </div>

                {{-- Roles Table --}}
                <div class="bg-white/50 backdrop-blur-md rounded-xl border border-white/50 shadow-xl overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Permissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Users</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($roles as $role)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center rounded-md px-2.5 py-1 text-sm font-semibold ring-1 ring-inset
                                            {{ $role->name === 'Admin'
                                                ? 'bg-purple-50 text-purple-700 ring-purple-600/20'
                                                : ($role->name === 'Kabag'
                                                    ? 'bg-blue-50 text-blue-700 ring-blue-600/20'
                                                    : ($role->name === 'AO'
                                                        ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                        : 'bg-gray-50 text-gray-700 ring-gray-600/20')) }}">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($role->permissions->take(5) as $permission)
                                                <span
                                                    class="px-1.5 py-0.5 text-[10px] bg-gray-100 text-gray-600 rounded">{{ $permission->name }}</span>
                                            @endforeach
                                            @if ($role->permissions->count() > 5)
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
                                            <button @click="editingRole = editingRole === {{ $role->id }} ? null : {{ $role->id }}; showCreateForm = false"
                                                class="p-2 text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors"
                                                title="Edit Role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            @if ($role->users()->count() === 0)
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Hapus role {{ $role->name }}?')"
                                                        class="p-2 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                                        title="Hapus Role">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Inline Edit Form Row --}}
                                <tr x-show="editingRole === {{ $role->id }}" x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100">
                                    <td colspan="4" class="px-6 py-4 bg-amber-50/50">
                                        <form action="{{ route('roles.update', $role) }}" method="POST"
                                            class="space-y-5">
                                            @csrf
                                            @method('PUT')
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-md bg-yellow-100 flex items-center justify-center">
                                                        <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </span>
                                                    Edit Role: {{ $role->name }}
                                                </h4>
                                                <div class="flex gap-2">
                                                    <button type="button"
                                                        onclick="this.closest('td').querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = true)"
                                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">Pilih
                                                        Semua</button>
                                                    <span class="text-gray-300">|</span>
                                                    <button type="button"
                                                        onclick="this.closest('td').querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = false)"
                                                        class="text-xs text-red-600 hover:text-red-800 font-medium">Hapus
                                                        Semua</button>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-900">Nama
                                                    Role</label>
                                                <input type="text" name="name"
                                                    value="{{ old('name', $role->name) }}"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                    required>
                                            </div>
                                            <div class="space-y-3">
                                                @php $rolePermissionIds = $role->permissions->pluck('id')->toArray(); @endphp
                                                @foreach ($permissions as $group => $groupPermissions)
                                                    <div class="bg-white/70 p-4 rounded-lg border border-gray-200">
                                                        <h5
                                                            class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                                                            {{ ucfirst($group) }}</h5>
                                                        <div
                                                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                            @foreach ($groupPermissions as $permission)
                                                                <label
                                                                    class="flex items-center gap-2 cursor-pointer group">
                                                                    <input type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                        {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}
                                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                    <span
                                                                        class="text-sm text-gray-700 group-hover:text-gray-900">
                                                                        {{ ucfirst(str_replace(' ' . $group, '', $permission->name)) }}
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="flex justify-end pt-2 space-x-3">
                                                <button type="button" @click="editingRole = null"
                                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</button>
                                                <button type="submit"
                                                    class="text-white bg-gray-800 hover:bg-gray-900 font-medium rounded-lg text-sm px-5 py-2.5">Simpan
                                                    Perubahan</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-user-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    })
                });
            });
        </script>
    @endpush
@endsection