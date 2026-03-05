<div>
    <div x-show="showUserOverlay" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="absolute bottom-[calc(100%+1rem)] right-0 w-72 max-h-96 z-[60]" x-cloak
        @click.away="showUserOverlay = false">

        <!-- Triangle Tip -->
        <div
            class="absolute -bottom-2 right-10 w-4 h-4 bg-white/70 backdrop-blur-lg rotate-45 border-r border-b border-white/50">
        </div>

        <div
            class="bg-white/70 backdrop-blur-xl border border-white/50 rounded-2xl shadow-2xl overflow-hidden flex flex-col h-full">
            <!-- Header -->
            <div class="p-4 border-b border-white/50 bg-white/30 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Users Online
                </h3>
                <span class="px-2 py-0.5 text-[10px] font-semibold bg-green-100 text-green-700 rounded-full">
                    {{ $users->count() }} Online
                </span>
            </div>

            <!-- User List -->
            <div class="overflow-y-auto p-2 space-y-1 custom-scrollbar" style="max-height: 300px;">
                @forelse($users as $user)
                    <div class="flex items-center p-2 rounded-xl hover:bg-white/40 transition-colors group">
                        <div class="relative">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ $user->initials() }}
                            </div>
                            <div
                                class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)]">
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-xs font-semibold text-gray-900 leading-tight">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-500">@ {{ $user->username }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500">
                        <p class="text-xs">No users online right now</p>
                    </div>
                @endforelse
            </div>
            @can('manage users')
                <!-- Footer -->
                <a href="{{ route('users.index') }}"
                    class="p-3 bg-gray-50/50 hover:bg-white/50 border-t border-white/50 text-center text-[10px] font-bold text-gray-600 uppercase tracking-widest transition-colors">
                    View All User Settings
                </a>
            @endcan
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.1);
        }
    </style>
</div>