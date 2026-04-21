<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-3">
                Laporan Kunjungan AO
            </h2>
            <p class="text-sm text-gray-500 mt-1">Periode: <strong class="text-indigo-700">{{ $periodLabel }}</strong></p>
            <div class="mt-2 text-sm">
                @php
                    $overallPaid = 0;
                    foreach($aosGroups as $group) {
                        $overallPaid += $group->sum('direct_paid_sum') + $group->sum('fulfilled_paid_sum');
                    }
                    $overallPaid += $kabagUsers->sum('direct_paid_sum') + $kabagUsers->sum('fulfilled_paid_sum');
                @endphp
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-black shadow-sm border border-emerald-200">
                    Total Realisasi: Rp {{ number_format($overallPaid, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600 pl-2">Filter:</label>
                <select wire:model.live="filter"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all">
                    <option value="daily">Harian (Hari Ini)</option>
                    <option value="weekly">Mingguan (Minggu Ini)</option>
                    <option value="monthly">Bulanan</option>
                </select>
            </div>

            @if($filter === 'daily')
                <div class="flex items-center gap-2 border-l border-gray-200 pl-3">
                    <label class="text-sm font-medium text-gray-600">Tanggal:</label>
                    <input type="date" wire:model.live="selectedDate"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all">
                </div>
            @elseif($filter === 'weekly')
                <div class="flex items-center gap-2 border-l border-gray-200 pl-3">
                    <label class="text-sm font-medium text-gray-600">Bulan:</label>
                    <input type="month" wire:model.live="selectedMonth"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all">
                </div>
                <div class="flex items-center gap-2 pl-3">
                    <label class="text-sm font-medium text-gray-600">Minggu Ke-:</label>
                    <select wire:model.live="selectedWeek"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all">
                        @php
                            try {
                                $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
                            } catch (\Exception $e) {
                                $date = \Carbon\Carbon::now();
                            }
                            $weeksCount = ceil($date->daysInMonth / 7);
                        @endphp
                        @for ($i = 1; $i <= $weeksCount; $i++)
                            <option value="{{ $i }}">Minggu {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            @elseif($filter === 'monthly')
                <div class="flex items-center gap-2 border-l border-gray-200 pl-3">
                    <label class="text-sm font-medium text-gray-600">Bulan:</label>
                    <input type="month" wire:model.live="selectedMonth"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all">
                </div>
            @endif

            <div wire:loading class="px-2">
                <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            <a href="{{ route('reports.performance-recap', ['filter' => $filter, 'month' => $selectedMonth, 'date' => $selectedDate, 'week' => $selectedWeek]) }}"
                target="_blank"
                class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak Rekap Semua AO
            </a>
        </div>
    </div>

    @if($kabagUsers->count() > 0)
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Pejabat Eksekutif
            </h3>
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
                <div wire:loading.class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10"></div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider w-16">No</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Pejabat Eksekutif</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Kantor Cabang</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">Lancar</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">DPK</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">KL</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">D</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">M</th>
                                <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">Kunjungan</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-indigo-900 uppercase tracking-wider">Total Bayar</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-indigo-900 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($kabagUsers as $index => $ao)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                                                    {{ $ao->initials() }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $ao->name }}</div>
                                                <div class="text-xs text-gray-500">Kode: {{ $ao->code ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $ao->office_branch ?? 'Pusat' }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_1_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_1_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_2_count > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_2_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_3_count > 0 ? 'bg-orange-100 text-orange-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_3_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_4_count > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_4_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_5_count > 0 ? 'bg-red-200 text-red-900' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_5_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        {{ $ao->visits_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-emerald-700">
                                        Rp {{ number_format(($ao->direct_paid_sum ?? 0) + ($ao->fulfilled_paid_sum ?? 0), 0, ',', '.') }}

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('reports.performance-detail', ['user' => $ao->id, 'filter' => $filter, 'month' => $selectedMonth, 'date' => $selectedDate, 'week' => $selectedWeek]) }}"
                                            target="_blank"
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors font-bold text-xs uppercase tracking-tight">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @forelse($aosGroups as $branch => $aos)
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 {{ $loop->first ? '' : 'pt-6 border-t border-gray-200' }} flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                {{ $branch }}
            </h3>
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
                <div wire:loading.class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10"></div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider w-16">No</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Account Officer (AO)</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-indigo-900 uppercase tracking-wider">Kantor Cabang</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">Lancar</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">DPK</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">KL</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">D</th>
                                <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">M</th>
                                <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-indigo-900 uppercase tracking-wider">Kunjungan</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-indigo-900 uppercase tracking-wider">Total Bayar</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-indigo-900 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($aos as $index => $ao)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                                                    {{ $ao->initials() }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $ao->name }}</div>
                                                <div class="text-xs text-gray-500">Kode: {{ $ao->code ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $ao->office_branch ?? 'Pusat' }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_1_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_1_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_2_count > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_2_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_3_count > 0 ? 'bg-orange-100 text-orange-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_3_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_4_count > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_4_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold {{ $ao->visits_kol_5_count > 0 ? 'bg-red-200 text-red-900' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $ao->visits_kol_5_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        {{ $ao->visits_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-emerald-700">
                                        Rp {{ number_format(($ao->direct_paid_sum ?? 0) + ($ao->fulfilled_paid_sum ?? 0), 0, ',', '.') }}

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('reports.performance-detail', ['user' => $ao->id, 'filter' => $filter, 'month' => $selectedMonth, 'date' => $selectedDate, 'week' => $selectedWeek]) }}"
                                            target="_blank"
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
            <div class="px-6 py-10 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-1">Tidak ada Account Officer</p>
                <p class="text-sm">Belum ada user dengan role AO di sistem ini.</p>
            </div>
        </div>
    @endforelse
</div>