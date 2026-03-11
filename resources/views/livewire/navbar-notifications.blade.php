<div class="relative" x-data="{ open: false }" wire:poll.120s>
    <button @click="open = !open" @click.away="open = false" type="button" class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-700 bg-white/50 backdrop-blur-md rounded-full hover:bg-white/80 focus:ring-4 focus:outline-none focus:ring-blue-300 border border-white/50 shadow-sm transition-all mr-2">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 20">
            <path d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z"/>
        </svg>
        <span class="sr-only">Notifications</span>
        
        @if($totalCount > 0)
            <div class="absolute inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900 animate-pulse">
                {{ $totalCount > 99 ? '99+' : $totalCount }}
            </div>
        @endif
    </button>

    <!-- Dropdown menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute right-0 top-full mt-2 w-72 max-w-sm bg-white border border-gray-100 divide-y divide-gray-100 rounded-xl shadow-2xl z-50 overflow-hidden" 
         x-cloak>
         
        <div class="block px-4 py-3 font-medium text-center text-gray-700 rounded-t-xl bg-gray-50/80 backdrop-blur-sm border-b border-gray-100">
            Notifications
        </div>
        
        <div class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto custom-scrollbar">
            @if($totalCount == 0)
                <div class="flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 mb-3 text-gray-200 bg-gray-50 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Tidak ada notifikasi baru</p>
                </div>
            @else
                
                @if(auth()->user()->hasRole('kabag'))
                    @if($pendingEvaluationsCount > 0)
                        <a href="{{ route('evaluations.index') }}" class="flex px-4 py-3 hover:bg-blue-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-blue-600">{{ $pendingEvaluationsCount }} Evaluasi</span> menunggu persetujuan Anda</div>
                                <div class="text-xs text-blue-600 font-medium">Lihat Evaluasi &rarr;</div>
                            </div>
                        </a>
                    @endif

                    @if($kabagJanjiBayarCount > 0)
                        <a href="{{ route('customer-visits.index') }}" class="flex px-4 py-3 hover:bg-orange-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-orange-600">{{ $kabagJanjiBayarCount }} Janji Bayar</span> jatuh tempo atau lewat jatuh tempo dari seluruh AO</div>
                                <div class="text-xs text-orange-600 font-medium">Lihat Kunjungan &rarr;</div>
                            </div>
                        </a>
                    @endif

                    @if($kabagFollowUpSpCount > 0)
                        <a href="{{ route('warning-letters.index') }}" class="flex px-4 py-3 hover:bg-red-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-red-600">{{ $kabagFollowUpSpCount }} Follow-up SP</span> yang perlu ditindaklanjuti</div>
                                <div class="text-xs text-red-600 font-medium">Lihat SP &rarr;</div>
                            </div>
                        </a>
                    @endif
                @endif

                @if(auth()->user()->hasRole('ao'))
                    @if($aoJanjiBayarCount > 0)
                        <a href="{{ route('calendar.index') }}" class="flex px-4 py-3 hover:bg-orange-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-orange-600">{{ $aoJanjiBayarCount }} Janji Bayar</span> jatuh tempo untuk Anda tindaklanjuti</div>
                                <div class="text-xs text-orange-600 font-medium">Lihat Calendar &rarr;</div>
                            </div>
                        </a>
                    @endif

                    @if($aoFollowUpSpCount > 0)
                        <a href="{{ route('warning-letters.index') }}" class="flex px-4 py-3 hover:bg-red-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-red-600">{{ $aoFollowUpSpCount }} Follow-up SP</span> jatuh tempo hari ini atau sebelumnya</div>
                                <div class="text-xs text-red-600 font-medium">Lihat SP &rarr;</div>
                            </div>
                        </a>
                    @endif
                    
                    @if($aoEvaluationStatusCount > 0)
                        <a href="{{ route('evaluations.index') }}" class="flex px-4 py-3 hover:bg-green-50/50 transition-colors group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="w-full pl-3">
                                <div class="text-gray-900 text-sm mb-1.5 leading-snug"><span class="font-bold text-green-600">{{ $aoEvaluationStatusCount }} Evaluasi</span> Anda telah diproses tujuh hari terakhir (Disetujui/Ditolak)</div>
                                <div class="text-xs text-green-600 font-medium">Lihat Evaluasi &rarr;</div>
                            </div>
                        </a>
                    @endif

                @endif
                
            @endif
        </div>
    </div>
</div>
