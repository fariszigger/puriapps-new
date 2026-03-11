@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <!-- Hero Header -->
    <div class="w-full p-6 md:p-8 rounded-xl shadow-xl mt-8 mb-8 relative overflow-hidden bg-center bg-cover"
        style="background-image: url('{{ asset('build/assets/header.png') }}');">

        <!-- Overlay for text readability -->
        <div class="absolute inset-0 bg-white/50 backdrop-blur-sm"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-2">Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p class="text-lg text-gray-800 font-medium">Anda saat ini sedang login dengan role
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}. Status
                    Anda sekarang <span class="text-green-600 font-bold drop-shadow-sm">Online</span>.
                </p>
            </div>

            <div class="mt-4 md:mt-0 shrink-0">
                <a href="#"
                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-gray-800 rounded-lg hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 transition-all shadow-lg hover:shadow-gray-500/30 transform hover:-translate-y-0.5">
                    Lebih lanjut
                    <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Section with Filters -->
    <div x-data="dashboardStats()" x-init="init()">
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Periode:</label>
                <input type="month" x-model="selectedMonth" @change="fetchStats()"
                    class="bg-white/60 border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :max="currentMonth">
            </div>
            <button @click="selectedMonth = ''; fetchStats()"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
                :class="selectedMonth === '' ? 'bg-gray-800 text-white shadow-lg' : 'bg-white/60 border border-gray-300 text-gray-700 hover:bg-white'">
                Semua Waktu
            </button>
            <span x-show="loading" class="text-sm text-gray-400 italic ml-2">
                <svg class="animate-spin inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Memuat...
            </span>

            @if(auth()->user()->hasRole('AO'))
                <!-- Total Pencairan Placeholder for AO -->
                <div class="ml-auto inline-flex items-center bg-gray-50/90 backdrop-blur-sm border border-emerald-200/60 shadow-sm px-4 py-1.5 rounded-full gap-2">
                    <div class="w-7 h-7 flex items-center justify-center bg-emerald-100/80 rounded-lg text-emerald-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 pr-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span class="text-[17px] font-black text-emerald-700 leading-none tracking-tight">Rp 400.000.000</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <template x-for="card in cards" :key="card.key">
                <div @click="toggleChart(card.key)"
                    class="p-4 bg-white/40 backdrop-blur-md rounded-xl border-2 shadow-lg flex items-center justify-between hover:bg-white/50 transition-all cursor-pointer transform hover:-translate-y-0.5"
                    :style="activeChart === card.key ? 'border-color:' + card.borderColor + '; box-shadow: 0 0 0 3px ' + card.ringColor + '; background: rgba(255,255,255,0.6)' : 'border-color: rgba(255,255,255,0.5)'">
                    <div>
                        <p class="text-sm font-medium text-gray-600" x-text="card.label"></p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats[card.key] ?? 0"></p>
                    </div>
                    <div class="p-2 rounded-lg" :style="'background:' + card.iconBg + '; color:' + card.iconColor">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"></path>
                        </svg>
                    </div>
                </div>
            </template>
        </div>

        <!-- Chart Area -->
        <div x-show="activeChart" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900" x-text="activeChartTitle"></h3>
                <button @click="activeChart = null" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="statsChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            function dashboardStats() {
                return {
                    selectedMonth: '',
                    currentMonth: new Date().toISOString().slice(0, 7),
                    loading: false,
                    activeChart: null,
                    chartInstance: null,
                    chartData: {},
                    stats: {
                        totalCustomers: {{ $totalCustomers }},
                        totalEvaluations: {{ $totalEvaluations }},
                        approvedCount: {{ $approvedCount }},
                        rejectedCount: {{ $rejectedCount }},
                        totalVisits: {{ $totalVisits }},
                    },
                    cards: [
                        { key: 'totalCustomers', label: 'Jumlah Debitur', color: 'blue', chartKey: 'customers', borderColor: 'rgb(96,165,250)', ringColor: 'rgba(96,165,250,0.3)', iconBg: 'rgba(219,234,254,0.5)', iconColor: 'rgb(37,99,235)', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z' },
                        { key: 'totalEvaluations', label: 'Jumlah Evaluasi', color: 'purple', chartKey: 'evaluations', borderColor: 'rgb(192,132,252)', ringColor: 'rgba(192,132,252,0.3)', iconBg: 'rgba(243,232,255,0.5)', iconColor: 'rgb(147,51,234)', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                        { key: 'approvedCount', label: 'Diterima', color: 'green', chartKey: 'approved', borderColor: 'rgb(74,222,128)', ringColor: 'rgba(74,222,128,0.3)', iconBg: 'rgba(220,252,231,0.5)', iconColor: 'rgb(22,163,74)', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                        { key: 'rejectedCount', label: 'Ditolak', color: 'red', chartKey: 'rejected', borderColor: 'rgb(248,113,113)', ringColor: 'rgba(248,113,113,0.3)', iconBg: 'rgba(254,226,226,0.5)', iconColor: 'rgb(220,38,38)', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
                        { key: 'totalVisits', label: 'Jumlah Kunjungan', color: 'pink', chartKey: 'visits', borderColor: 'rgb(244,114,182)', ringColor: 'rgba(244,114,182,0.3)', iconBg: 'rgba(252,231,243,0.5)', iconColor: 'rgb(219,39,119)', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' },
                    ],

                    get activeChartTitle() {
                        const card = this.cards.find(c => c.key === this.activeChart);
                        if (!card) return '';
                        if (this.selectedMonth) {
                            const [y, m] = this.selectedMonth.split('-');
                            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            return card.label + ' - Harian ' + monthNames[parseInt(m) - 1] + ' ' + y;
                        }
                        return 'Tren ' + card.label + ' (12 Bulan Terakhir)';
                    },

                    init() {
                        this.fetchStats();
                    },

                    async fetchStats() {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams();
                            if (this.selectedMonth) params.set('month', this.selectedMonth);

                            const res = await fetch('{{ route("dashboard.stats") }}?' + params.toString());
                            const data = await res.json();

                            this.stats = data.stats;
                            this.chartData = data.chart;

                            if (this.activeChart) {
                                this.$nextTick(() => this.renderChart());
                            }
                        } catch (e) {
                            console.error('Failed to fetch stats:', e);
                        }
                        this.loading = false;
                    },

                    toggleChart(key) {
                        if (this.activeChart === key) {
                            this.activeChart = null;
                            return;
                        }
                        this.activeChart = key;
                        this.$nextTick(() => this.renderChart());
                    },

                    renderChart() {
                        const card = this.cards.find(c => c.key === this.activeChart);
                        if (!card || !this.chartData.labels) return;

                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                        }

                        const ctx = document.getElementById('statsChart');
                        if (!ctx) return;

                        const colorMap = {
                            blue: { bg: 'rgba(59, 130, 246, 0.15)', border: 'rgb(59, 130, 246)' },
                            purple: { bg: 'rgba(147, 51, 234, 0.15)', border: 'rgb(147, 51, 234)' },
                            green: { bg: 'rgba(34, 197, 94, 0.15)', border: 'rgb(34, 197, 94)' },
                            red: { bg: 'rgba(239, 68, 68, 0.15)', border: 'rgb(239, 68, 68)' },
                            pink: { bg: 'rgba(236, 72, 153, 0.15)', border: 'rgb(236, 72, 153)' },
                        };

                        const colors = colorMap[card.color] || colorMap.blue;

                        this.chartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: this.chartData.labels,
                                datasets: [{
                                    label: card.label,
                                    data: this.chartData[card.chartKey],
                                    backgroundColor: colors.bg,
                                    borderColor: colors.border,
                                    borderWidth: 2,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        titleFont: { size: 13, weight: 'bold' },
                                        bodyFont: { size: 12 },
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { precision: 0, font: { size: 11 } },
                                        grid: { color: 'rgba(0,0,0,0.05)' },
                                    },
                                    x: {
                                        ticks: { font: { size: 10 }, maxRotation: 45 },
                                        grid: { display: false },
                                    }
                                }
                            }
                        });
                    }
                }
            }
        </script>
    @endpush

    <!-- 7 Hari Ke Depan -->
    <div class="mb-8" x-data="{ 
            filter: 'all', 
            limit: 8, 
            events: {{ json_encode($next7Events->toArray()) }},
            get filteredEvents() {
                return this.filter === 'all' ? this.events : this.events.filter(e => e.type === this.filter);
            }
        }">
        <div
            class="p-5 bg-gradient-to-r from-teal-50/80 to-blue-50/80 backdrop-blur-md rounded-xl border border-teal-200/50 shadow-lg transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">7 Hari Ke Depan</h3>
                    <span class="px-2 py-0.5 text-xs font-bold text-teal-700 bg-teal-100 rounded-full"
                        x-text="filteredEvents.length + ' jadwal'"></span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button @click="filter = 'all'; limit = 8"
                        :class="filter === 'all' ? 'bg-teal-600 text-white shadow-md' : 'bg-white/80 text-teal-700 hover:bg-white'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all border border-teal-200">Semua</button>
                    <button @click="filter = 'visit'; limit = 8"
                        :class="filter === 'visit' ? 'bg-green-600 text-white shadow-md' : 'bg-white/80 text-green-700 hover:bg-white'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all border border-green-200">Kunjungan</button>
                    <button @click="filter = 'dob'; limit = 8"
                        :class="filter === 'dob' ? 'bg-blue-600 text-white shadow-md' : 'bg-white/80 text-blue-700 hover:bg-white'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all border border-blue-200">Ultah</button>
                    <button @click="filter = 'janji_bayar'; limit = 8"
                        :class="filter === 'janji_bayar' ? 'bg-orange-600 text-white shadow-md' : 'bg-white/80 text-orange-700 hover:bg-white'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all border border-orange-200">Janji
                        Bayar</button>
                    <button @click="filter = 'sp'; limit = 8"
                        :class="filter === 'sp' ? 'bg-red-600 text-white shadow-md' : 'bg-white/80 text-red-700 hover:bg-white'"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all border border-red-200">SP</button>
                    <a href="{{ route('calendar.index') }}"
                        class="ml-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition-all shadow-md flex items-center gap-1">
                        Kalender
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2"
                x-show="filteredEvents.length > 0">
                <template x-for="(event, index) in filteredEvents.slice(0, limit)" :key="index">
                    <div
                        class="bg-white/70 backdrop-blur-sm rounded-lg border border-white/50 p-3 flex items-center gap-3 hover:shadow-md transition-shadow">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm"
                            :class="event.type === 'dob' ? 'bg-blue-100' : (event.type === 'visit' ? 'bg-green-100' : (event.type === 'sp' ? 'bg-red-100' : 'bg-orange-100'))">
                            <span x-text="event.type === 'dob' ? '🎂' : (event.type === 'visit' ? '📍' : (event.type === 'sp' ? '📄' : '💰'))"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate" x-text="event.name"></p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold uppercase"
                                    :class="event.type === 'dob' ? 'bg-blue-100 text-blue-700' : (event.type === 'visit' ? 'bg-green-100 text-green-700' : (event.type === 'sp' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700'))"
                                    x-text="event.type === 'dob' ? 'Ultah' : (event.type === 'visit' ? 'Kunjungan' : (event.type === 'sp' ? 'Follow Up SP' : 'Janji Bayar'))">
                                </span>
                                <span class="text-[10px] text-gray-500" x-text="event.display_date"></span>
                                <span x-show="event.type === 'dob' && event.age" class="text-[10px] px-1.5 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700" x-text="'ke-' + event.age"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button @click="limit += 8" x-show="filteredEvents.length > limit" style="display: none;"
                class="w-full text-xs text-teal-700 hover:text-teal-800 font-bold mt-3 text-center py-2 bg-white/50 hover:bg-white/80 rounded-lg transition-colors border border-teal-100">
                + Tampilkan <span x-text="Math.min(8, filteredEvents.length - limit)"></span> jadwal lainnya
            </button>

            <p x-show="filteredEvents.length === 0" style="display: none;"
                class="text-sm text-gray-500 font-medium text-center py-6 bg-white/40 rounded-lg border border-dashed border-gray-300">
                Tidak ada jadwal <span x-text="filter !== 'all' ? 'untuk filter ini ' : ''"></span>dalam 7 hari ke depan.
            </p>
        </div>
    </div>

    <!-- Antrian Persetujuan (Kabag / Admin Only) -->
    @if(auth()->user()->can('approve evaluations') && $pendingEvaluations->count() > 0)
        <div class="p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-3">
                    <span class="bg-yellow-100 text-yellow-600 p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Antrian Persetujuan
                </h2>
                <span
                    class="inline-flex items-center justify-center px-3 py-1 text-sm font-bold text-yellow-800 bg-yellow-100 rounded-full">
                    {{ $pendingEvaluations->count() }} Menunggu
                </span>
            </div>

            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                                Pengajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AO</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasabah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plafon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jangka
                                Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingEvaluations as $pending)
                            <tr class="hover:bg-yellow-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $pending->loan_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pending->user->code ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pending->customer->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp
                                    {{ number_format($pending->loan_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pending->loan_term_months }} Bulan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pending->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('evaluations.edit', $pending->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Tinjau
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Daftar Janji Bayar (AO Only) -->
    @if(!auth()->user()->can('view all data') && isset($pendingJanjiBayar) && $pendingJanjiBayar->count() > 0)
        <div class="p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-3">
                    <span class="bg-orange-100 text-orange-600 p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </span>
                    Daftar Janji Bayar
                </h2>
                <span
                    class="inline-flex items-center justify-center px-3 py-1 text-sm font-bold text-orange-800 bg-orange-100 rounded-full shadow-sm">
                    {{ $pendingJanjiBayar->count() }} Menunggu
                </span>
            </div>

            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasabah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                                Janji Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah
                                (Rp)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingJanjiBayar as $janji)
                            <tr class="hover:bg-orange-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $janji->customer->name ?? '-' }}
                                    <div class="text-[11px] font-normal text-gray-500 mt-1">Visit:
                                        {{ $janji->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $tgl = \Carbon\Carbon::parse($janji->tanggal_janji_bayar);
                                        $isToday = $tgl->isToday();
                                        $isPast = $tgl->isPast() && !$isToday;
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full font-semibold text-[11px] tracking-wide 
                                                                                                                                                                                                                                                                    {{ $isPast ? 'bg-red-100 text-red-700' : ($isToday ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $tgl->format('d M Y') }}
                                        @if($isToday) (Hari Ini)
                                        @elseif($isPast) (Terlewat) @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                    {{ $janji->jumlah_bayar ? number_format($janji->jumlah_bayar, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('calendar.index') }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-colors shadow-sm">
                                        Lihat di Kalender
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- List Customer Card -->
        <a href="{{ route('customers.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-blue-700 transition-colors">
                    Daftar Nasabah</h5>
                <div class="p-3 bg-blue-100/50 rounded-full text-blue-600 group-hover:bg-blue-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Lihat dan tinjau data nasabah.</p>
        </a>



        <!-- List Evaluation Card -->
        <a href="{{ route('evaluations.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-red-700 transition-colors">
                    Daftar Evaluasi</h5>
                <div class="p-3 bg-red-100/50 rounded-full text-red-600 group-hover:bg-red-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Lihat dan tinjau evaluasi.</p>
        </a>

        <!-- List Kunjungan Card -->
        <a href="{{ route('customer-visits.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-pink-600 transition-colors">
                    Daftar Kunjungan</h5>
                <div class="p-3 bg-pink-100/50 rounded-full text-pink-600 group-hover:bg-pink-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Lihat dan tinjau kunjungan.</p>
        </a>

        <!-- History Penagihan Card -->
        <a href="{{ route('collection-history.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-orange-600 transition-colors">
                    History Penagihan</h5>
                <div class="p-3 bg-orange-100/50 rounded-full text-orange-600 group-hover:bg-orange-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Lihat histori penagihan dan surat tiap nasabah.</p>
        </a>

        @can('view performance reports')
            <!-- Laporan Hasil Kinerja Card -->
            <a href="{{ route('reports.performance') }}"
                class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-indigo-600 transition-colors">
                        Laporan Hasil Kinerja</h5>
                    <div
                        class="p-3 bg-indigo-100/50 rounded-full text-indigo-600 group-hover:bg-indigo-200/50 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="font-normal text-gray-700">Lihat laporan hasil kinerja kunjungan AO.</p>
            </a>
        @endcan
    </div>

    {{-- Separator: Administrasi Surat --}}
    @can('view warning-letters')
        <div class="flex items-center justify-center my-8">
            <div class="h-px bg-gray-300 w-full md:w-1/3"></div>
            <span class="px-4 text-sm text-gray-400 font-medium uppercase tracking-wider whitespace-nowrap">Administrasi Surat</span>
            <div class="h-px bg-gray-300 w-full md:w-1/3"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Daftar Pencairan Card Placeholder -->
            <a href="#" class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group opacity-75">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-emerald-700 transition-colors">
                        Daftar Pencairan</h5>
                    <div class="p-3 bg-emerald-100/50 rounded-full text-emerald-600 group-hover:bg-emerald-200/50 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="font-normal text-gray-700 italic">(Segera Hadir) Kelola pencairan nasabah.</p>
            </a>

            <!-- Daftar Surat Card -->
            <a href="{{ route('warning-letters.index') }}"
                class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-red-700 transition-colors">
                        Daftar Surat</h5>
                    <div class="p-3 bg-red-100/50 rounded-full text-red-600 group-hover:bg-red-200/50 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="font-normal text-gray-700">Kelola Surat Peringatan dan Surat Panggilan nasabah.</p>
            </a>
        </div>
    @endcan

    <!-- Separator -->
    <div class="flex items-center justify-center my-8">
        <div class="h-px bg-gray-300 w-full md:w-1/3"></div>
        <span class="px-4 text-sm text-gray-400 font-medium uppercase tracking-wider">Pemetaan & Analisis</span>
        <div class="h-px bg-gray-300 w-full md:w-1/3"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Global Map Card -->
        <a href="{{ route('map.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-amber-600 transition-colors">
                    Global Map</h5>
                <div class="p-3 bg-amber-100/50 rounded-full text-amber-600 group-hover:bg-amber-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Peta sebaran debitur dan aset.</p>
        </a>

        <!-- Calendar Card -->
        <a href="{{ route('calendar.index') }}"
            class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-teal-600 transition-colors">
                    Kalender</h5>
                <div class="p-3 bg-teal-100/50 rounded-full text-teal-600 group-hover:bg-teal-200/50 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="font-normal text-gray-700">Lihat jadwal kunjungan, ulang tahun, dan janji bayar.</p>
        </a>
    </div>


    <!-- Additional Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @can('manage users')
            <!-- User Settings Card -->
            <a href="{{ route('users.index') }}"
                class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-purple-700 transition-colors">
                        User Settings</h5>
                    <div
                        class="p-3 bg-purple-100/50 rounded-full text-purple-600 group-hover:bg-purple-200/50 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="font-normal text-gray-700">Manage user accounts and roles.</p>
            </a>
        @endcan
        @can('manage gps')
            <!-- GPS Management Card -->
            <a href="{{ route('gps-trackers.index') }}"
                class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-cyan-700 transition-colors">
                        Manajemen GPS</h5>
                    <div class="p-3 bg-cyan-100/50 rounded-full text-cyan-600 group-hover:bg-cyan-200/50 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="font-normal text-gray-700">Tambahkan dan kelola perangkat GPS tracker.</p>
            </a>
        @endcan
    </div>
@endsection