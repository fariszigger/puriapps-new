@extends('layouts.dashboard')

@section('title', 'Analisis Pencairan')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <a href="{{ route('credit-disbursements.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Daftar Pencairan</a>
        </div>
    </li>
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Analisis</span>
        </div>
    </li>
@endsection

@section('content')
    <div x-data="analyticsPage()" x-init="fetchData()" class="mt-8 mb-8 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 rounded-xl text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Analisis Pencairan</h1>
                    <p class="text-sm text-gray-500">Grafik pertumbuhan dan perbandingan pencairan kredit antar AO.</p>
                </div>
            </div>
            <a href="{{ route('credit-disbursements.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white/60 border border-gray-300 rounded-xl hover:bg-white/80 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Filter Toolbar --}}
        <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/50 p-5 shadow-xl">
            <div class="flex flex-col gap-4">
                {{-- Row 1: Mode toggle + AO filter --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    {{-- Mode Toggle --}}
                    <div class="flex p-1 bg-gray-100/50 rounded-xl border border-gray-200 shadow-inner w-full sm:w-auto">
                        <button @click="viewMode = 'monthly'; fetchData()"
                            class="flex-1 sm:flex-none px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300"
                            :class="viewMode === 'monthly' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 hover:text-emerald-600'">
                            Bulanan
                        </button>
                        <button @click="viewMode = 'yearly'; fetchData()"
                            class="flex-1 sm:flex-none px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300"
                            :class="viewMode === 'yearly' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 hover:text-emerald-600'">
                            Tahunan
                        </button>
                    </div>

                    {{-- AO Filter --}}
                    <div class="relative w-full sm:w-auto">
                        <select x-model="filterAo" @change="fetchData()"
                            class="w-full sm:w-auto pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer min-w-[180px]">
                            <option value="">Semua AO</option>
                            @foreach($aoUsers as $user)
                                <option value="{{ $user->id }}">[{{ $user->code }}] {{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Row 2: Period pickers --}}
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    {{-- Period 1 --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg whitespace-nowrap">Periode 1</span>
                        <template x-if="viewMode === 'monthly'">
                            <input type="month" x-model="filterMonth" @change="fetchData()"
                                class="flex-1 sm:flex-none pl-4 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                        </template>
                        <template x-if="viewMode === 'yearly'">
                            <select x-model="filterMonth" @change="fetchData()"
                                class="flex-1 sm:flex-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}-01">{{ $y }}</option>
                                @endfor
                            </select>
                        </template>
                    </div>

                    {{-- VS badge --}}
                    <div class="hidden sm:flex items-center">
                        <span class="px-3 py-1.5 bg-gray-800 text-white text-[10px] font-black uppercase tracking-wider rounded-full shadow-lg">VS</span>
                    </div>

                    {{-- Period 2 --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-wider rounded-lg whitespace-nowrap">Periode 2</span>
                        <template x-if="viewMode === 'monthly'">
                            <input type="month" x-model="filterMonth2" @change="fetchData()"
                                class="flex-1 sm:flex-none pl-4 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500/50 transition-all cursor-pointer">
                        </template>
                        <template x-if="viewMode === 'yearly'">
                            <select x-model="filterMonth2" @change="fetchData()"
                                class="flex-1 sm:flex-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500/50 transition-all appearance-none cursor-pointer">
                                <option value="">— Tidak ada —</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}-01">{{ $y }}</option>
                                @endfor
                            </select>
                        </template>
                        <button x-show="filterMonth2" @click="filterMonth2 = ''; fetchData()"
                            class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div x-show="loading" x-transition class="flex items-center justify-center py-12">
            <div class="flex items-center gap-3 text-emerald-600">
                <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm font-bold">Memuat data...</span>
            </div>
        </div>

        <div x-show="!loading" x-transition class="space-y-6">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" :class="!data.period2 && 'lg:grid-cols-1'">
                {{-- Period 1 Summary --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-black uppercase tracking-wider text-emerald-700 flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span x-text="data.period1?.label || 'Periode 1'"></span>
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-lg">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pencairan</p>
                            <p class="text-lg font-black text-gray-900" x-text="'Rp ' + formatNumber(data.period1?.summary?.total_realization || 0)"></p>
                        </div>
                        <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-lg">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Target</p>
                            <p class="text-lg font-black text-gray-900" x-text="'Rp ' + formatNumber(data.period1?.summary?.total_target || 0)"></p>
                        </div>
                        <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-lg">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pencapaian</p>
                            <p class="text-lg font-black" :class="(data.period1?.summary?.achievement || 0) >= 100 ? 'text-emerald-700' : ((data.period1?.summary?.achievement || 0) >= 50 ? 'text-yellow-700' : 'text-red-600')"
                                x-text="(data.period1?.summary?.achievement || 0) + '%'"></p>
                        </div>
                        <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-lg">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah</p>
                            <p class="text-lg font-black text-gray-900" x-text="(data.period1?.summary?.total_count || 0) + ' trx'"></p>
                        </div>
                    </div>
                </div>

                {{-- Period 2 Summary --}}
                <template x-if="data.period2">
                    <div class="space-y-3">
                        <h3 class="text-sm font-black uppercase tracking-wider text-blue-700 flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span x-text="data.period2?.label || 'Periode 2'"></span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-blue-100/50 shadow-lg">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pencairan</p>
                                <p class="text-lg font-black text-gray-900" x-text="'Rp ' + formatNumber(data.period2?.summary?.total_realization || 0)"></p>
                                <template x-if="data.period1?.summary?.total_realization">
                                    <p class="text-[10px] font-bold mt-1"
                                        :class="(data.period2?.summary?.total_realization || 0) >= (data.period1?.summary?.total_realization || 0) ? 'text-emerald-600' : 'text-red-500'"
                                        x-text="getGrowthLabel(data.period1?.summary?.total_realization, data.period2?.summary?.total_realization)">
                                    </p>
                                </template>
                            </div>
                            <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-blue-100/50 shadow-lg">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Target</p>
                                <p class="text-lg font-black text-gray-900" x-text="'Rp ' + formatNumber(data.period2?.summary?.total_target || 0)"></p>
                            </div>
                            <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-blue-100/50 shadow-lg">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pencapaian</p>
                                <p class="text-lg font-black" :class="(data.period2?.summary?.achievement || 0) >= 100 ? 'text-emerald-700' : ((data.period2?.summary?.achievement || 0) >= 50 ? 'text-yellow-700' : 'text-red-600')"
                                    x-text="(data.period2?.summary?.achievement || 0) + '%'"></p>
                            </div>
                            <div class="p-4 bg-white/40 backdrop-blur-md rounded-xl border border-blue-100/50 shadow-lg">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah</p>
                                <p class="text-lg font-black text-gray-900" x-text="(data.period2?.summary?.total_count || 0) + ' trx'"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Chart 1: AO Comparison --}}
            <div class="p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl">
                <div class="flex items-center gap-2 mb-5">
                    <div class="p-1.5 bg-blue-100 rounded-lg text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Perbandingan AO vs Target</h3>
                    <span class="text-xs text-gray-400 font-medium" x-text="viewMode === 'yearly' ? '(Tahunan)' : '(Bulanan)'"></span>
                </div>
                <div class="relative" style="height: 380px;">
                    <canvas id="aoComparisonChart"></canvas>
                </div>
            </div>

            {{-- Chart 2: Growth / Cumulative --}}
            <div class="p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl">
                <div class="flex items-center gap-2 mb-5">
                    <div class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Pertumbuhan Kumulatif</h3>
                    <span class="text-xs text-gray-400 font-medium" x-text="viewMode === 'yearly' ? '(Per Bulan)' : '(Per Hari)'"></span>
                </div>
                <div class="relative" style="height: 380px;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function analyticsPage() {
        return {
            viewMode: 'monthly',
            filterMonth: new Date().toISOString().slice(0, 7),
            filterMonth2: '',
            filterAo: '',
            loading: false,
            data: {},
            aoChartInstance: null,
            growthChartInstance: null,

            formatNumber(num) {
                return Number(num).toLocaleString('id-ID');
            },

            getGrowthLabel(oldVal, newVal) {
                if (!oldVal || oldVal === 0) return '';
                const pct = ((newVal - oldVal) / oldVal * 100).toFixed(1);
                return pct >= 0 ? '↑ ' + pct + '%' : '↓ ' + Math.abs(pct) + '%';
            },

            async fetchData() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        json: '1',
                        view_mode: this.viewMode,
                        month: this.filterMonth,
                    });
                    if (this.filterAo) params.set('ao', this.filterAo);
                    if (this.filterMonth2) params.set('month2', this.filterMonth2);

                    const res = await fetch('{{ route("credit-disbursements.analytics") }}?' + params.toString());
                    this.data = await res.json();

                    this.$nextTick(() => {
                        this.renderAoChart();
                        this.renderGrowthChart();
                    });
                } catch (e) {
                    console.error('Failed to fetch analytics:', e);
                }
                this.loading = false;
            },

            renderAoChart() {
                const ctx = document.getElementById('aoComparisonChart');
                if (!ctx) return;
                if (this.aoChartInstance) this.aoChartInstance.destroy();

                const p1 = this.data.period1?.aoChart;
                if (!p1) return;

                const datasets = [];

                // Period 1 realization
                const bgColors1 = p1.percentages.map(p =>
                    p >= 100 ? 'rgba(16, 185, 129, 0.7)' : (p >= 50 ? 'rgba(245, 158, 11, 0.7)' : 'rgba(239, 68, 68, 0.7)')
                );
                const borderColors1 = p1.percentages.map(p =>
                    p >= 100 ? 'rgb(16, 185, 129)' : (p >= 50 ? 'rgb(245, 158, 11)' : 'rgb(239, 68, 68)')
                );

                datasets.push({
                    label: this.data.period1?.label || 'Periode 1',
                    data: p1.realization,
                    backgroundColor: bgColors1,
                    borderColor: borderColors1,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                });

                // Period 2 realization (if exists)
                const p2 = this.data.period2?.aoChart;
                if (p2) {
                    datasets.push({
                        label: this.data.period2?.label || 'Periode 2',
                        data: p2.realization,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    });
                }

                // Target bars
                datasets.push({
                    label: 'Target',
                    data: p1.targets,
                    backgroundColor: 'rgba(148, 163, 184, 0.15)',
                    borderColor: 'rgba(148, 163, 184, 0.6)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    borderDash: [5, 5],
                });

                this.aoChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: p1.labels, datasets },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { usePointStyle: true, pointStyle: 'rectRounded', padding: 20, font: { size: 11, weight: 'bold' } },
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)',
                                padding: 14,
                                cornerRadius: 10,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(ctx) {
                                        const val = ctx.raw || 0;
                                        return ctx.dataset.label + ': Rp ' + Number(val).toLocaleString('id-ID');
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { size: 11 },
                                    callback: function(val) {
                                        if (val >= 1000000000) return (val / 1000000000).toFixed(1) + ' M';
                                        if (val >= 1000000) return (val / 1000000).toFixed(0) + ' Jt';
                                        return val;
                                    }
                                },
                                grid: { color: 'rgba(0,0,0,0.04)' },
                            },
                            x: {
                                ticks: { font: { size: 11, weight: 'bold' } },
                                grid: { display: false },
                            }
                        },
                    },
                });
            },

            renderGrowthChart() {
                const ctx = document.getElementById('growthChart');
                if (!ctx) return;
                if (this.growthChartInstance) this.growthChartInstance.destroy();

                const p1 = this.data.period1?.growthChart;
                if (!p1) return;

                const datasets = [];

                // Period 1 bars
                datasets.push({
                    label: (this.data.period1?.label || 'Periode 1') + (this.viewMode === 'yearly' ? ' (Bulanan)' : ' (Harian)'),
                    data: p1.daily,
                    backgroundColor: 'rgba(16, 185, 129, 0.3)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    order: 3,
                });

                // Period 1 cumulative line
                datasets.push({
                    label: (this.data.period1?.label || 'Periode 1') + ' Kumulatif',
                    data: p1.cumulative,
                    type: 'line',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.3,
                    order: 1,
                });

                // Period 2 (if exists)
                const p2 = this.data.period2?.growthChart;
                if (p2) {
                    // Period 2 bars
                    datasets.push({
                        label: (this.data.period2?.label || 'Periode 2') + (this.viewMode === 'yearly' ? ' (Bulanan)' : ' (Harian)'),
                        data: p2.daily,
                        backgroundColor: 'rgba(59, 130, 246, 0.25)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                        order: 4,
                    });

                    // Period 2 cumulative line
                    datasets.push({
                        label: (this.data.period2?.label || 'Periode 2') + ' Kumulatif',
                        data: p2.cumulative,
                        type: 'line',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.3,
                        order: 2,
                    });
                }

                // Target line
                datasets.push({
                    label: 'Target',
                    data: p1.target_line,
                    type: 'line',
                    borderColor: 'rgba(239, 68, 68, 0.5)',
                    borderWidth: 2,
                    borderDash: [8, 4],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    fill: false,
                    order: 0,
                });

                // Use the longer label set for x-axis
                const labels = p2 && p2.labels.length > p1.labels.length ? p2.labels : p1.labels;

                this.growthChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: { labels, datasets },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { usePointStyle: true, pointStyle: 'rectRounded', padding: 15, font: { size: 10, weight: 'bold' } },
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)',
                                padding: 14,
                                cornerRadius: 10,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(ctx) {
                                        const val = ctx.raw || 0;
                                        return ctx.dataset.label + ': Rp ' + Number(val).toLocaleString('id-ID');
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { size: 11 },
                                    callback: function(val) {
                                        if (val >= 1000000000) return (val / 1000000000).toFixed(1) + ' M';
                                        if (val >= 1000000) return (val / 1000000).toFixed(0) + ' Jt';
                                        return val;
                                    }
                                },
                                grid: { color: 'rgba(0,0,0,0.04)' },
                            },
                            x: {
                                ticks: { font: { size: 10 }, maxRotation: 0 },
                                grid: { display: false },
                            }
                        },
                    },
                });
            }
        }
    }
</script>
@endpush
