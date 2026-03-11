@extends('layouts.dashboard')

@section('title', 'Kalender')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Kalender</span>
        </div>
    </li>
@endsection

@section('content')
    <div
        class="w-full bg-white/60 backdrop-blur-md rounded-2xl p-4 md:p-6 lg:p-8 shadow-xl border border-white/50 min-h-[80vh] flex flex-col">

        {{-- ═══════════════ TOP HEADER BAR ═══════════════ --}}
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-gray-200/50 pb-5">
            {{-- Left: Date & Time --}}
            <div class="flex items-center gap-5">
                <div
                    class="hidden md:flex flex-col items-center justify-center p-3 bg-orange-50 rounded-2xl text-orange-400">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" />
                    </svg>
                </div>
                <div class="flex items-baseline gap-3">
                    <h1 id="live-date" class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Maret 03</h1>
                    <span class="hidden md:inline text-gray-300 text-2xl font-light">|</span>
                    <span id="live-time" class="text-2xl md:text-3xl font-light text-gray-400 tracking-wide">20:28</span>
                </div>
            </div>

            {{-- Right: View Toggle + Navigation --}}
            <div class="flex items-center gap-2 flex-wrap">
                {{-- View Mode Toggle --}}
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button id="btn-weekly" onclick="setViewMode('weekly')"
                        class="px-3 py-1.5 text-sm font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700">Minggu</button>
                    <button id="btn-monthly" onclick="setViewMode('monthly')"
                        class="px-3 py-1.5 text-sm font-semibold rounded-md transition-all bg-white text-gray-800 shadow-sm">Bulan</button>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button onclick="navigate(-1)" class="px-2.5 py-1.5 hover:bg-gray-50 text-gray-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <span id="nav-range-label"
                        class="px-3 py-1.5 text-sm font-semibold text-gray-700 border-x border-gray-200 min-w-[120px] text-center">Mar
                        03 - 09</span>
                    <button onclick="navigate(1)" class="px-2.5 py-1.5 hover:bg-gray-50 text-gray-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <button onclick="goToToday()"
                    class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Hari
                    Ini</button>
            </div>
        </div>

        {{-- ═══════════════ WEEKLY VIEW ═══════════════ --}}
        <div id="view-weekly" class="flex-grow hidden">
            <div id="week-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5"></div>
        </div>

        {{-- ═══════════════ MONTHLY VIEW ═══════════════ --}}
        <div id="view-monthly" class="flex-grow">
            {{-- Day-of-week headers --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                <div class="text-center text-xs font-bold text-red-400 py-2">Min</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Sen</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Sel</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Rab</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Kam</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Jum</div>
                <div class="text-center text-xs font-bold text-gray-500 py-2">Sab</div>
            </div>
            <div id="month-grid" class="grid grid-cols-7 gap-1"></div>

            {{-- Day Detail Panel (shown when clicking a date) --}}
            <div id="month-detail" class="mt-4 bg-gray-50/60 rounded-xl border border-gray-100 p-5 hidden">
                <h3 id="month-detail-title" class="text-lg font-bold text-gray-800 mb-3"></h3>
                <div id="month-detail-events" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ 7 HARI KE DEPAN ═══════════════ --}}
    {{-- ═══════════════ 7 HARI KE DEPAN ═══════════════ --}}
    <div
        class="mt-6 bg-gradient-to-r from-teal-50/80 to-blue-50/80 backdrop-blur-md rounded-xl border border-teal-200/50 shadow-xl p-6"
        x-data="{ showAll7Days: false }">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">7 Hari Ke Depan</h2>
                @if($next7Events->count() > 0)
                    <span class="hidden sm:inline-block px-2.5 py-0.5 text-xs font-bold text-teal-700 bg-teal-100 rounded-full">{{ $next7Events->count() }} jadwal</span>
                @endif
            </div>
            @if($next7Events->count() > 6)
                <button @click="showAll7Days = !showAll7Days"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-white/80 border border-teal-200 text-teal-700 hover:bg-teal-50 shadow-sm">
                    <span x-text="showAll7Days ? 'Tampilkan Sedikit' : 'Lihat Semua ({{ $next7Events->count() }})'"></span>
                </button>
            @endif
        </div>
        @if($next7Events->count() > 0)
            @php 
                $groupedNext7 = $next7Events->groupBy(function($e) {
                    return $e['type'] === 'dob' 
                        ? \Carbon\Carbon::parse($e['date'])->setYear(now()->year)->format('Y-m-d')
                        : \Carbon\Carbon::parse($e['date'])->format('Y-m-d');
                })->sortKeys();
                $globalCount = 0;
            @endphp
            
            <div class="flex flex-col gap-6">
                @foreach($groupedNext7 as $dateString => $events)
                    @php 
                        $firstItemIndex = $globalCount;
                        $carbonDate = \Carbon\Carbon::parse($dateString);
                    @endphp
                    <div x-show="showAll7Days || {{ $firstItemIndex }} < 6" x-transition class="flex flex-col">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="font-bold text-teal-800 bg-teal-100/70 px-3 py-1 rounded-lg text-sm shadow-sm border border-teal-200/50">
                                {{ $carbonDate->translatedFormat('l, d F Y') }}
                            </h3>
                            <div class="h-px bg-teal-200/50 flex-grow"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($events as $event)
                                <div x-show="showAll7Days || {{ $globalCount }} < 6" x-transition
                                    class="bg-white/70 backdrop-blur-sm rounded-lg border border-white/50 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                                    @if($event['type'] === 'dob') bg-blue-100 text-blue-700
                                                    @elseif($event['type'] === 'visit') bg-green-100 text-green-700
                                                    @else bg-orange-100 text-orange-700 @endif">
                                            @if($event['type'] === 'dob') Ulang Tahun
                                            @elseif($event['type'] === 'visit') Kunjungan
                                            @elseif($event['type'] === 'sp') Follow Up SP
                                            @else Janji Bayar @endif
                                        </span>
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $event['name'] }}</p>
                                    @if($event['type'] === 'dob')
                                        <p class="text-xs text-gray-500 mt-1">Usia: {{ now()->year - \Carbon\Carbon::parse($event['date'])->year }} tahun</p>
                                    @endif
                                    @if($event['type'] === 'janji_bayar' && isset($event['jumlah']))
                                        <p class="text-xs text-orange-600 font-semibold mt-1">Rp {{ number_format($event['jumlah'], 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                @php $globalCount++; @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-6">Tidak ada jadwal dalam 7 hari ke depan.</p>
        @endif
    </div>

    {{-- ═══════════════ AGENDA BULAN INI ═══════════════ --}}
    <div class="mt-6 mb-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl p-6"
        x-data="{
            filterType: 'all',
            currentDateStr: '{{ now()->format('Y-m-d') }}',
            get allEvents() {
                const events = {{ Js::from($thisMonthEvents) }};
                return events.map(e => {
                    const d = new Date(e.date);
                    if(e.type === 'dob') d.setFullYear(new Date().getFullYear());
                    e.dateStr = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
                    return e;
                });
            },
            get filteredEvents() {
                if (this.filterType === 'all') return this.allEvents;
                return this.allEvents.filter(e => e.type === this.filterType);
            },
            get availableDates() {
                return [...new Set(this.filteredEvents.map(e => e.dateStr))].sort();
            },
            get currentEvents() {
                return this.filteredEvents.filter(e => e.dateStr === this.currentDateStr);
            },
            setFilter(type) {
                this.filterType = type;
                if(!this.availableDates.includes(this.currentDateStr) && this.availableDates.length > 0) {
                     const nextOrEqual = this.availableDates.find(d => d >= this.currentDateStr);
                     this.currentDateStr = nextOrEqual || this.availableDates[this.availableDates.length - 1];
                }
            },
            nextDate() {
                let idx = this.availableDates.findIndex(d => d > this.currentDateStr);
                if (idx !== -1) this.currentDateStr = this.availableDates[idx];
            },
            prevDate() {
                let arr = [...this.availableDates].reverse();
                let idx = arr.findIndex(d => d < this.currentDateStr);
                if (idx !== -1) this.currentDateStr = arr[idx];
            },
            get hasNext() {
                return this.availableDates.some(d => d > this.currentDateStr);
            },
            get hasPrev() {
                return this.availableDates.some(d => d < this.currentDateStr);
            },
            formatDateHeader(dateStr) {
                if(!dateStr) return '';
                const parts = dateStr.split('-');
                const day = parts[2];
                const monthIdx = parseInt(parts[1], 10) - 1;
                const year = parts[0];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                return day + ' ' + months[monthIdx] + ' ' + year;
            },
            getAge(date) {
                return new Date().getFullYear() - new Date(date).getFullYear();
            },
            formatRupiah(val) {
                return val ? Number(val).toLocaleString('id-ID') : '-';
            },
            init() {
                if(!this.availableDates.includes(this.currentDateStr) && this.availableDates.length > 0) {
                     const nextOrEqual = this.availableDates.find(d => d >= this.currentDateStr);
                     this.currentDateStr = nextOrEqual || this.availableDates[0];
                }
            }
        }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Agenda Bulan Ini</h2>
                <span
                    class="px-2.5 py-0.5 text-xs font-bold text-purple-700 bg-purple-100 rounded-full"
                    x-text="filteredEvents.length + ' kegiatan'"></span>
            </div>
            {{-- Type Filter --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <button @click="setFilter('all')" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="filterType === 'all' ? 'bg-gray-800 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    Semua
                </button>
                <button @click="setFilter('dob')" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="filterType === 'dob' ? 'bg-blue-600 text-white shadow' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'">
                    🎂 Ulang Tahun
                </button>
                <button @click="setFilter('visit')" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="filterType === 'visit' ? 'bg-green-600 text-white shadow' : 'bg-green-50 text-green-700 hover:bg-green-100'">
                    📍 Kunjungan
                </button>
                <button @click="setFilter('janji_bayar')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="filterType === 'janji_bayar' ? 'bg-orange-600 text-white shadow' : 'bg-orange-50 text-orange-700 hover:bg-orange-100'">
                    💰 Janji Bayar
                </button>
                <button @click="setFilter('sp')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="filterType === 'sp' ? 'bg-red-600 text-white shadow' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                    📄 Follow Up SP
                </button>
            </div>
        </div>
        @if($thisMonthEvents->count() > 0)
            {{-- Date Navigation --}}
            <div class="flex items-center justify-between bg-white/60 p-2 rounded-xl border border-gray-100 mb-4 shadow-sm" x-show="allEvents.length > 0">
                <button @click="prevDate()" :disabled="!hasPrev"
                    class="px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-semibold rounded-lg transition-all flex items-center gap-1 md:gap-2"
                    :class="!hasPrev ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-white shadow hover:shadow-md'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </button>
                
                <div class="flex flex-col items-center justify-center">
                    <span class="text-xs text-gray-500 font-medium mb-0.5 uppercase tracking-wider text-center">Menampilkan Tanggal</span>
                    <h3 class="text-base md:text-lg font-bold text-gray-900 text-center px-2 md:px-4" x-text="formatDateHeader(currentDateStr)"></h3>
                </div>
                
                <button @click="nextDate()" :disabled="!hasNext"
                    class="px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-semibold rounded-lg transition-all flex items-center gap-1 md:gap-2"
                    :class="!hasNext ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-white shadow hover:shadow-md'">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" x-show="currentEvents.length > 0">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(event, idx) in currentEvents" :key="idx">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                        :class="event.type === 'dob' ? 'bg-blue-100 text-blue-700' : event.type === 'visit' ? 'bg-green-100 text-green-700' : event.type === 'sp' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700'"
                                        x-text="event.type === 'dob' ? 'Ulang Tahun' : event.type === 'visit' ? 'Kunjungan' : event.type === 'sp' ? 'Follow Up SP' : 'Janji Bayar'"></span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-800" x-text="event.name"></td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <span x-show="event.type === 'dob'" x-text="'Usia ' + getAge(event.date) + ' tahun'"></span>
                                    <span x-show="event.type === 'janji_bayar' && event.jumlah" x-text="'Rp ' + formatRupiah(event.jumlah)"></span>
                                    <span x-show="event.type === 'visit' || (event.type === 'janji_bayar' && !event.jumlah)">-</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="currentEvents.length === 0" class="py-12 text-center bg-white/30 rounded-lg">
                    <p class="text-sm text-gray-500">Tidak ada kegiatan yang sesuai pada tanggal ini.</p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-6">Tidak ada agenda di bulan ini.</p>
        @endif
    </div>

    {{-- ═══════════════ REKAP KUNJUNGAN ═══════════════ --}}
    <div class="mt-6 mb-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Rekap Kunjungan Bulan Ini</h2>
                <span
                    class="px-2.5 py-0.5 text-xs font-bold text-indigo-700 bg-indigo-100 rounded-full">{{ $recapVisits->count() }}
                    kunjungan</span>
            </div>
            <a href="{{ route('calendar.recap', ['month' => now()->month, 'year' => now()->year]) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Rekap
            </a>
        </div>

        @if($recapVisits->count() > 0)
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                            </th>
                            @if(!$isAO)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AO</th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasabah
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kol</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketemu
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recapVisits as $visit)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                    {{ $visit->created_at->format('d M') }}</td>
                                @if(!$isAO)
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $visit->user->name ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $visit->customer->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @php
                                        $kolMap = ['1' => 'green', '2' => 'yellow', '3' => 'orange', '4' => 'red', '5' => 'red'];
                                        $kolColor = $kolMap[$visit->kolektibilitas] ?? 'gray';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $kolColor }}-100 text-{{ $kolColor }}-800">{{ $visit->kolektibilitas }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $visit->ketemu_dengan }}</td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    @if($visit->hasil_penagihan === 'bayar')
                                        <span class="text-green-600 font-semibold">Bayar</span>
                                        @if($visit->jumlah_bayar)
                                            <span class="text-xs text-gray-500">Rp
                                                {{ number_format($visit->jumlah_bayar, 0, ',', '.') }}</span>
                                        @endif
                                    @elseif($visit->hasil_penagihan === 'janji_bayar')
                                        <span class="text-orange-600 font-semibold">Janji
                                            {{ $visit->tanggal_janji_bayar ? \Carbon\Carbon::parse($visit->tanggal_janji_bayar)->format('d/m') : '' }}</span>
                                        @if($visit->jumlah_pembayaran)
                                            <span class="text-xs text-gray-500">Rp
                                                {{ number_format($visit->jumlah_pembayaran, 0, ',', '.') }}</span>
                                        @endif
                                        @if($visit->janji_bayar_fulfilled)
                                            <span
                                                class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 ml-1">✓
                                                Lunas</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-6">Belum ada kunjungan di bulan ini.</p>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // ─── Data ───
        const dobEvents = @json($dobEvents);
        const visitEvents = @json($visitEvents);
        const janjiBayarEvents = @json($janjiBayarEvents);
        const warningLetterEvents = @json($warningLetterEvents);
        const csrfToken = '{{ csrf_token() }}';
        const userInitials = '{{ auth()->user()->initials() ?? substr(auth()->user()->username, 0, 2) }}';

        const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const MONTHS_FULL = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const DAYS_FULL = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        let viewMode = 'monthly'; // 'weekly' | 'monthly'
        let weekStart = new Date('{{ $todayDate }}');
        let monthYear = new Date('{{ $todayDate }}'); // tracks which month we're viewing

        // ─── Pill Colors ───
        const PILL_COLORS = {
            dob: [{ bg: '#dbeafe', text: '#1e40af' }, { bg: '#e0e7ff', text: '#3730a3' }, { bg: '#ede9fe', text: '#5b21b6' }],
            visit: [{ bg: '#dcfce7', text: '#166534' }, { bg: '#d1fae5', text: '#065f46' }, { bg: '#fef9c3', text: '#854d0e' }],
            janji_bayar: [{ bg: '#ffe4e6', text: '#9f1239' }, { bg: '#ffedd5', text: '#9a3412' }, { bg: '#fce7f3', text: '#9d174d' }],
            sp: [{ bg: '#fee2e2', text: '#b91c1c' }, { bg: '#fef2f2', text: '#991b1b' }, { bg: '#fff1f2', text: '#9d174d' }],
        };
        const PILL_ICONS = { dob: '🎂', visit: '📍', janji_bayar: '💰', sp: '📄' };

        // ─── Init ───
        function init() {
            startLiveClock();
            render();
        }

        function startLiveClock() {
            function tick() {
                const now = new Date();
                document.getElementById('live-date').textContent = MONTHS_FULL[now.getMonth()] + ' ' + String(now.getDate()).padStart(2, '0');
                document.getElementById('live-time').textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            }
            tick(); setInterval(tick, 30000);
        }

        // ─── View Mode Toggle ───
        function setViewMode(mode) {
            viewMode = mode;
            document.getElementById('btn-weekly').className = mode === 'weekly'
                ? 'px-3 py-1.5 text-sm font-semibold rounded-md transition-all bg-white text-gray-800 shadow-sm'
                : 'px-3 py-1.5 text-sm font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700';
            document.getElementById('btn-monthly').className = mode === 'monthly'
                ? 'px-3 py-1.5 text-sm font-semibold rounded-md transition-all bg-white text-gray-800 shadow-sm'
                : 'px-3 py-1.5 text-sm font-semibold rounded-md transition-all text-gray-500 hover:text-gray-700';

            document.getElementById('view-weekly').classList.toggle('hidden', mode !== 'weekly');
            document.getElementById('view-monthly').classList.toggle('hidden', mode !== 'monthly');
            render();
        }

        // ─── Navigation ───
        function navigate(delta) {
            if (viewMode === 'weekly') {
                weekStart.setDate(weekStart.getDate() + delta * 7);
            } else {
                monthYear.setMonth(monthYear.getMonth() + delta);
            }
            render();
        }

        function goToToday() {
            const today = new Date('{{ $todayDate }}');
            weekStart = new Date(today);
            monthYear = new Date(today);
            render();
        }

        // ─── Shared: Get Events ───
        function getEventsForDate(dateStr) {
            const d = new Date(dateStr);
            const md = String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            const events = [];
            dobEvents.forEach(e => { if (e.month_day === md) events.push({ ...e, age: d.getFullYear() - new Date(e.date).getFullYear() }); });
            visitEvents.forEach(e => { if (e.date === dateStr) events.push(e); });
            janjiBayarEvents.forEach(e => { if (e.date === dateStr) events.push(e); });
            warningLetterEvents.forEach(e => { if (e.date === dateStr) events.push(e); });
            events.sort((a, b) => ({ 'janji_bayar': 1, 'sp': 2, 'dob': 3, 'visit': 4 }[a.type]) - ({ 'janji_bayar': 1, 'sp': 2, 'dob': 3, 'visit': 4 }[b.type]));
            return events;
        }

        function fmtDate(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        // ─── Main Render ───
        function render() {
            if (viewMode === 'weekly') renderWeekly();
            else renderMonthly();
        }

        // ══════════════════════════════════════
        //  WEEKLY VIEW
        // ══════════════════════════════════════
        function renderWeekly() {
            const grid = document.getElementById('week-grid');
            grid.innerHTML = '';

            const end = new Date(weekStart);
            end.setDate(weekStart.getDate() + 6);

            // Update nav label
            const sm = MONTHS[weekStart.getMonth()], em = MONTHS[end.getMonth()];
            const sd = String(weekStart.getDate()).padStart(2, '0'), ed = String(end.getDate()).padStart(2, '0');
            document.getElementById('nav-range-label').textContent = weekStart.getMonth() === end.getMonth()
                ? `${sm} ${sd} - ${ed}` : `${sm} ${sd} - ${em} ${ed}`;

            const todayStr = fmtDate(new Date());

            for (let i = 0; i < 7; i++) {
                const day = new Date(weekStart);
                day.setDate(weekStart.getDate() + i);
                const dateStr = fmtDate(day);
                const events = getEventsForDate(dateStr);
                const isToday = dateStr === todayStr;

                const col = document.createElement('div');
                col.className = 'flex flex-col';

                const dayBadge = isToday
                    ? `<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold">${String(day.getDate()).padStart(2, '0')}</span>`
                    : `<span class="text-gray-700 text-sm font-bold">${String(day.getDate()).padStart(2, '0')}</span>`;

                const evtCount = events.length > 0 ? `<span class="text-gray-400 text-xs">${events.length} kegiatan</span>` : '';

                col.innerHTML = `
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-500 w-7">${DAYS[day.getDay()]}</span>
                            ${dayBadge}
                        </div>
                        ${evtCount}
                    </div>
                    <div class="flex flex-col gap-2 flex-grow min-h-[120px]">
                        ${events.length > 0
                        ? events.map((e, idx) => renderPill(e, idx)).join('')
                        : '<div class="flex-grow flex items-center justify-center border-2 border-dashed border-gray-100 rounded-xl text-gray-300 text-xs font-medium min-h-[80px]">Kosong</div>'
                    }
                    </div>
                `;
                grid.appendChild(col);
            }

            // 8th column: Next Week
            const nwStart = new Date(weekStart); nwStart.setDate(weekStart.getDate() + 7);
            const nwEnd = new Date(nwStart); nwEnd.setDate(nwStart.getDate() + 6);
            const summary = document.createElement('div');
            summary.className = 'flex flex-col';
            summary.innerHTML = `
                <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                    <span class="text-sm font-bold text-gray-700">Minggu Depan</span>
                </div>
                <div class="flex-grow flex flex-col items-center justify-center text-center p-4 bg-gray-50/60 rounded-xl border border-gray-100 min-h-[120px]">
                    <p class="text-sm font-medium text-gray-400 mb-4">${MONTHS[nwStart.getMonth()]} ${String(nwStart.getDate()).padStart(2, '0')} – ${MONTHS[nwEnd.getMonth()]} ${String(nwEnd.getDate()).padStart(2, '0')}</p>
                    <button onclick="navigate(1)" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-all hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            `;
            grid.appendChild(summary);
        }

        // ══════════════════════════════════════
        //  MONTHLY VIEW
        // ══════════════════════════════════════
        function renderMonthly() {
            const grid = document.getElementById('month-grid');
            grid.innerHTML = '';

            const year = monthYear.getFullYear();
            const month = monthYear.getMonth();

            // Update nav label
            document.getElementById('nav-range-label').textContent = `${MONTHS_FULL[month]} ${year}`;

            const firstDayOfWeek = new Date(year, month, 1).getDay(); // 0=Sun
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const todayStr = fmtDate(new Date());

            // Empty cells before first day
            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'min-h-[70px] md:min-h-[90px]';
                grid.appendChild(empty);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                const events = getEventsForDate(dateStr);
                const isToday = dateStr === todayStr;
                const isSunday = new Date(year, month, day).getDay() === 0;

                const cell = document.createElement('div');
                cell.className = `min-h-[70px] md:min-h-[90px] p-1.5 md:p-2 rounded-xl cursor-pointer transition-all border border-transparent hover:border-gray-200 hover:bg-gray-50/50 ${isToday ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : ''}`;
                cell.onclick = () => showMonthDetail(dateStr);

                // Day number
                const dayNum = document.createElement('div');
                if (isToday) {
                    dayNum.className = 'mb-1 flex items-center gap-1.5';
                    dayNum.innerHTML = `<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold shadow-sm">${day}</span><span class="text-[10px] font-bold text-blue-500 uppercase">Hari Ini</span>`;
                } else {
                    dayNum.className = `text-sm font-bold mb-1 ${isSunday ? 'text-red-400' : 'text-gray-700'}`;
                    dayNum.textContent = day;
                }
                cell.appendChild(dayNum);

                // Event dots
                if (events.length > 0) {
                    const dotsRow = document.createElement('div');
                    dotsRow.className = 'flex flex-wrap gap-1 mb-1';
                    const types = [...new Set(events.map(e => e.type))];
                    types.forEach(type => {
                        if (type === 'dob') return; // User requested to hide blue circle
                        const dot = document.createElement('div');
                        const color = type === 'visit' ? '#22c55e' : '#f97316';
                        dot.className = 'w-2 h-2 rounded-full';
                        dot.style.backgroundColor = color;
                        dotsRow.appendChild(dot);
                    });
                    if (events.length > 1) {
                        const badge = document.createElement('span');
                        badge.className = 'text-[9px] text-gray-400 font-bold leading-none';
                        badge.textContent = events.length;
                        dotsRow.appendChild(badge);
                    }
                    cell.appendChild(dotsRow);

                    // Show first event name (truncated) on larger screens
                    const preview = document.createElement('div');
                    preview.className = 'hidden md:block text-[10px] text-gray-500 truncate leading-tight';
                    preview.textContent = events[0].name;
                    cell.appendChild(preview);
                }

                grid.appendChild(cell);
            }

            // Hide detail initially
            document.getElementById('month-detail').classList.add('hidden');
        }

        function showMonthDetail(dateStr) {
            const events = getEventsForDate(dateStr);
            const d = new Date(dateStr);
            const title = DAYS_FULL[d.getDay()] + ', ' + d.getDate() + ' ' + MONTHS_FULL[d.getMonth()] + ' ' + d.getFullYear();

            const panel = document.getElementById('month-detail');
            const titleEl = document.getElementById('month-detail-title');
            const eventsEl = document.getElementById('month-detail-events');

            titleEl.textContent = title;
            eventsEl.innerHTML = '';

            if (events.length === 0) {
                eventsEl.innerHTML = '<p class="text-sm text-gray-400 col-span-full text-center py-4">Tidak ada kegiatan pada tanggal ini.</p>';
            } else {
                events.forEach((event, idx) => {
                    eventsEl.innerHTML += renderDetailCard(event, idx);
                });
            }

            panel.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function renderDetailCard(event, idx) {
            const colors = PILL_COLORS[event.type] || PILL_COLORS.dob;
            const c = colors[idx % colors.length];
            const icon = PILL_ICONS[event.type];

            let detail = '';
            let extra = '';

            if (event.type === 'dob') {
                detail = `🎂 Usia ${event.age} tahun`;
            } else if (event.type === 'visit') {
                detail = `Kolektibilitas: ${event.kolektibilitas || '-'}`;
            } else if (event.type === 'janji_bayar') {
                detail = event.jumlah ? `Rp ${Number(event.jumlah).toLocaleString('id-ID')}` : '';
                extra = `<button onclick="event.stopPropagation();togglePromise(${event.visit_id},'${event.id}')" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all hover:shadow-sm" style="background:${c.text}15;color:${c.text}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Tandai Lunas
                </button>`;
            }

            const typeName = event.type === 'dob' ? 'Ulang Tahun' : event.type === 'visit' ? 'Kunjungan' : event.type === 'sp' ? 'Follow Up SP' : 'Janji Bayar';

            return `
                <div id="detail-${event.id}" class="rounded-xl p-3.5 transition-all" style="background:${c.bg};color:${c.text}">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="text-sm">${icon}</span>
                        <span class="text-[10px] font-bold uppercase tracking-wide opacity-70">${typeName}</span>
                    </div>
                    <p class="font-bold text-sm">${event.name}</p>
                    <p class="text-xs font-medium opacity-80 mt-0.5">${detail}</p>
                    ${extra}
                </div>`;
        }

        // ─── Pill for Weekly View ───
        function renderPill(event, idx) {
            const colors = PILL_COLORS[event.type] || PILL_COLORS.dob;
            const c = colors[idx % colors.length];
            const icon = PILL_ICONS[event.type];

            let detail = '';
            let checkBtn = '';

            if (event.type === 'dob') {
                detail = `Usia ${event.age} thn`;
            } else if (event.type === 'visit') {
                detail = `Kol: ${event.kolektibilitas || '-'}`;
            } else if (event.type === 'janji_bayar') {
                detail = event.jumlah ? `Rp ${Number(event.jumlah).toLocaleString('id-ID')}` : '';
                checkBtn = `<button onclick="event.stopPropagation();togglePromise(${event.visit_id},'${event.id}')" class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center transition-all shadow-sm hover:scale-110" style="background:${c.text}20;color:${c.text}" title="Tandai Lunas">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>`;
            }

            return `
                <div id="pill-${event.id}" class="rounded-2xl p-3 flex flex-col gap-1.5 transition-all hover:-translate-y-0.5 hover:shadow-md cursor-default" style="background:${c.bg};color:${c.text}">
                    <div class="flex items-start justify-between gap-1">
                        <div class="flex items-center gap-1.5 overflow-hidden min-w-0">
                            <span class="text-sm shrink-0">${icon}</span>
                            <span class="font-bold text-xs leading-tight truncate">${event.name}</span>
                        </div>
                        ${checkBtn}
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-semibold opacity-80">${detail}</span>
                        <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" style="background:${c.text}15">
                            <span class="text-[7px] font-bold uppercase" style="color:${c.text}">${userInitials}</span>
                        </div>
                    </div>
                </div>`;
        }

        // ─── Format number helper ───
        function formatRupiah(value) {
            const num = parseInt(String(value).replace(/\D/g, '')) || 0;
            return num ? num.toLocaleString('id-ID') : '';
        }

        // ─── Toggle Promise ───
        async function togglePromise(visitId, eventCardId) {
            const evt = janjiBayarEvents.find(e => e.visit_id === visitId);
            const existingAmount = evt && evt.jumlah ? Number(evt.jumlah) : 0;
            const custName = evt ? evt.name : '';

            const confirm = await Swal.fire({
                title: 'Konfirmasi Pelunasan / Janji Lagi',
                html: `<div class="text-left">
                    <p class="text-gray-600 mb-2">Apakah nasabah sudah membayar atau ingin menjadwalkan ulang?</p>
                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <p class="font-bold text-gray-800">${custName}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran / Janji Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium text-sm">Rp</span>
                            <input type="text" id="swal-jumlah-pembayaran" value="${existingAmount ? formatRupiah(existingAmount) : ''}" placeholder="0"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                oninput="this.value = this.value.replace(/\\D/g,'').replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.')">
                        </div>
                    </div>

                    <div id="reschedule-container" style="display:none;" class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Janji Baru</label>
                        <input type="date" id="swal-tanggal-janji-baru" 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <p class="text-[10px] text-gray-500 mt-2 italic">* Klik <b>"Sudah Lunas"</b> jika sudah bayar, atau <b>"Jadwalkan Ulang"</b> jika ada janji baru.</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#16a34a',
                denyButtonColor: '#f97316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Sudah Lunas',
                denyButtonText: 'Jadwalkan Ulang',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: () => {
                    const denyBtn = Swal.getDenyButton();
                    const rescheduleContainer = document.getElementById('reschedule-container');
                    const dateInput = document.getElementById('swal-tanggal-janji-baru');
                    
                    denyBtn.addEventListener('click', () => {
                        if (rescheduleContainer.style.display === 'none') {
                            rescheduleContainer.style.display = 'block';
                            // Focus date input
                            dateInput.focus();
                            // Prevent closing
                            return false;
                        }
                    });
                },
                preConfirm: () => {
                    const input = document.getElementById('swal-jumlah-pembayaran');
                    const raw = input ? parseInt(input.value.replace(/\D/g, '')) || 0 : 0;
                    return { jumlah_pembayaran: raw, type: 'fulfillment' };
                },
                preDeny: () => {
                    const dateInput = document.getElementById('swal-tanggal-janji-baru');
                    const amountInput = document.getElementById('swal-jumlah-pembayaran');
                    
                    const rescheduleContainer = document.getElementById('reschedule-container');
                    if (rescheduleContainer.style.display === 'none') {
                        rescheduleContainer.style.display = 'block';
                        dateInput.focus();
                        Swal.showValidationMessage('Silakan pilih tanggal janji baru');
                        return false;
                    }

                    if (!dateInput.value) {
                        Swal.showValidationMessage('Tanggal janji baru wajib diisi');
                        return false;
                    }
                    
                    const rawAmount = amountInput ? parseInt(amountInput.value.replace(/\D/g, '')) || 0 : 0;
                    return { 
                        tanggal_janji_baru: dateInput.value, 
                        jumlah_pembayaran: rawAmount,
                        type: 'reschedule' 
                    };
                }
            });

            if (confirm.isDismissed) return;

            const payload = {};
            if (confirm.isConfirmed) {
                payload.jumlah_pembayaran = confirm.value.jumlah_pembayaran;
            } else if (confirm.isDenied) {
                payload.tanggal_janji_baru = confirm.value.tanggal_janji_baru;
                payload.jumlah_pembayaran = confirm.value.jumlah_pembayaran;
            }

            try {
                const res = await fetch(`/calendar/toggle-promise/${visitId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                
                if (data.success) {
                    // Update UI: if fulfilled, hide. If rescheduled, refresh calendar.
                    if (data.fulfilled || data.rescheduled) {
                        // For simplicity in rescheduling (date might change), we reload or re-render 
                        // But for UI smoothness, let's just reload page or fetch new data
                        // Since this is a complex UI, window.location.reload() is safest to ensure all 
                        // views (weekly/monthly/agenda) are consistent.
                        
                        if (data.fulfilled) {
                             const el = document.getElementById('pill-' + eventCardId) || document.getElementById('detail-' + eventCardId);
                             if (el) {
                                el.style.transition = 'all 0.3s ease';
                                el.style.opacity = '0';
                                el.style.transform = 'scale(0.9) translateY(10px)';
                             }
                             setTimeout(() => {
                                window.location.reload(); 
                             }, 300);
                        } else {
                            window.location.reload();
                        }
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.mixin({
                            toast: true, position: 'bottom-start', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                            customClass: { popup: 'bg-green-500 text-white rounded-lg shadow-lg border border-green-600', timerProgressBar: 'bg-green-700' }
                        }).fire({ icon: 'success', title: data.message });
                    }
                }
            } catch (err) {
                console.error('Toggle error:', err);
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memproses permintaan.' });
            }
        }

        document.addEventListener('DOMContentLoaded', init);
    </script>
@endpush