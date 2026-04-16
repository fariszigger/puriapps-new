<div>
    {{-- Monthly Summary Cards --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </span>
                Ringkasan Pencairan
                @if($viewMode === 'period' && $filterMonth && $filterMonthEnd)
                    <span class="text-sm font-normal text-gray-500">
                        — {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }} s/d {{ \Carbon\Carbon::parse($filterMonthEnd . '-01')->translatedFormat('F Y') }}
                    </span>
                @elseif($filterMonth)
                    <span class="text-sm font-normal text-gray-500">
                        — {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}
                    </span>
                @endif
            </h3>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-bold text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-500">/ Rp {{ number_format($totalTarget, 0, ',', '.') }}</span>
                    @if($totalTarget > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                            {{ ($grandTotal / $totalTarget * 100) >= 100 ? 'bg-emerald-100 text-emerald-700' : (($grandTotal / $totalTarget * 100) >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ round($grandTotal / $totalTarget * 100, 1) }}%
                        </span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">({{ $aoCount }} AO)</span>
            </div>
        </div>

        @if($aoSummary->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($aoSummary as $summary)
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl border border-white/50 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $summary['code'] }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $summary['name'] }}</p>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 rounded-full
                                {{ $summary['percentage'] >= 100 ? 'bg-emerald-100 text-emerald-700' : ($summary['percentage'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $summary['percentage'] }}%
                            </span>
                        </div>
                        <p class="text-lg font-black text-gray-900 mb-1">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                            <div class="h-2 rounded-full transition-all duration-500
                                {{ $summary['percentage'] >= 100 ? 'bg-emerald-500' : ($summary['percentage'] >= 50 ? 'bg-yellow-500' : 'bg-red-400') }}"
                                style="width: {{ min(100, $summary['percentage']) }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-500">
                            {{ $summary['total_count'] }} pencairan · Target: Rp {{ number_format($summary['limit'], 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white/40 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                <p class="text-sm text-gray-500 font-medium">Belum ada data pencairan untuk periode ini.</p>
            </div>
        @endif
    </div>

    {{-- Advanced Toolbar --}}
    <div class="bg-white/50 backdrop-blur-md rounded-2xl border border-white/50 p-4 shadow-xl mb-8 space-y-4">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            {{-- Left: Search & View Mode --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                {{-- Search bar --}}
                <div class="relative w-full sm:w-72 group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-emerald-500 transition-transform group-focus-within:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="search"
                        class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 border border-gray-200 rounded-xl bg-white/80 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all shadow-inner"
                        placeholder="Cari nasabah atau AO...">
                </div>

                {{-- Mode Toggle --}}
                <div class="flex p-1 bg-gray-100/50 rounded-xl border border-gray-200 shadow-inner w-full sm:w-auto">
                    <button wire:click="$set('viewMode', 'monthly')" 
                        class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300 {{ $viewMode === 'monthly' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 hover:text-emerald-600' }}">
                        Bulanan
                    </button>
                    <button wire:click="$set('viewMode', 'period')" 
                        class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300 {{ $viewMode === 'period' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 hover:text-emerald-600' }}">
                        Periode
                    </button>
                    <button wire:click="$set('viewMode', 'yearly')" 
                        class="flex-1 sm:flex-none px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300 {{ $viewMode === 'yearly' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 hover:text-emerald-600' }}">
                        Tahunan
                    </button>
                </div>
            </div>

            {{-- Right: Filters & Actions --}}
            <div class="flex flex-wrap items-center justify-center lg:justify-end gap-3 w-full lg:w-auto border-t lg:border-t-0 pt-4 lg:pt-0 border-gray-100">
                <div class="flex flex-wrap items-center gap-2">
                    @if($viewMode === 'monthly')
                        <div class="relative min-w-[140px]">
                            <input type="month" wire:model.live="filterMonth"
                                class="w-full pl-4 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                        </div>
                    @elseif($viewMode === 'period')
                        <div class="flex items-center gap-2">
                            <div class="relative min-w-[140px]">
                                <span class="absolute -top-4 left-2 text-[8px] font-bold text-emerald-600 uppercase">Dari</span>
                                <input type="month" wire:model.live="filterMonth"
                                    class="w-full pl-4 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                            </div>
                            <div class="relative min-w-[140px]">
                                <span class="absolute -top-4 left-2 text-[8px] font-bold text-emerald-600 uppercase">Sampai</span>
                                <input type="month" wire:model.live="filterMonthEnd"
                                    class="w-full pl-4 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                            </div>
                        </div>
                    @else
                        <div class="relative min-w-[140px]">
                            <select wire:model.live="filterMonth"
                                class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}-01">{{ $y }}</option>
                                @endfor
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- AO Selector --}}
                <select wire:model.live="filterAo"
                    class="pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer min-w-[180px]">
                    <option value="">Semua AO</option>
                    @foreach($aoUsers as $user)
                        <option value="{{ $user->id }}">[{{ $user->code }}] {{ $user->name }}</option>
                    @endforeach
                </select>

                {{-- Show Per Page --}}
                <select wire:model.live="perPage"
                    class="pl-3 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer w-20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>

                {{-- Print Button --}}
                <a href="{{ route('credit-disbursements.print', ['month' => $filterMonth, 'month_end' => $filterMonthEnd, 'ao' => $filterAo, 'view_mode' => $viewMode]) }}" 
                    target="_blank"
                    class="flex items-center gap-2 px-5 py-2.5 bg-white font-black text-[10px] uppercase tracking-widest text-emerald-700 border-2 border-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm group">
                    <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak
                </a>

                {{-- Excel Export Button --}}
                <a href="{{ route('credit-disbursements.export', ['month' => $filterMonth, 'month_end' => $filterMonthEnd, 'ao' => $filterAo, 'view_mode' => $viewMode]) }}"
                    class="flex items-center gap-2 px-5 py-2.5 bg-white font-black text-[10px] uppercase tracking-widest text-green-700 border-2 border-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>

                {{-- Analytics Button --}}
                <a href="{{ route('credit-disbursements.analytics') }}"
                    class="flex items-center gap-2 px-5 py-2.5 bg-white font-black text-[10px] uppercase tracking-widest text-blue-700 border-2 border-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm group">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Analisis
                </a>

                {{-- Add Button --}}
                @can('create credit-disbursements')
                <a href="{{ route('credit-disbursements.create') }}"
                    class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 font-black text-[10px] uppercase tracking-widest text-white rounded-xl hover:bg-emerald-700 transition-all shadow-lg hover:shadow-emerald-500/30 group">
                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-white/40">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 backdrop-blur-sm">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">SPK</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="px-6 py-3">AO</th>
                    <th scope="col" class="px-6 py-3">Nasabah & Alamat</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">Plafond</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">Tenor</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">Bunga</th>
                    <th scope="col" class="px-6 py-3 whitespace-nowrap">Angsuran</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    @if(auth()->user()->can('edit credit-disbursements') || auth()->user()->can('delete credit-disbursements'))
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($disbursements as $item)
                    <tr wire:key="disbursement-{{ $item->id }}"
                        class="bg-white/40 border-b border-white/40 hover:bg-white/60 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $disbursements->total() - (($disbursements->currentPage() - 1) * $disbursements->perPage()) - $loop->index }}
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            {{ $item->nomor_spk ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $item->disbursement_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded bg-gray-100/80 text-gray-700 font-bold uppercase text-xs tracking-wider">
                                {{ $item->user->code ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->customer_name }}
                            @if($item->address)
                                <br><span class="text-xs text-gray-500 font-normal leading-tight">{{ $item->address }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-emerald-700">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $item->jangka_waktu }} bln
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ number_format($item->suku_bunga, 2, ',', '.') }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            Rp {{ number_format($item->angsuran, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php $status = strtolower($item->status); @endphp
                            @if($status == 'aktif' || $status == 'active' || empty($status))
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold uppercase tracking-wider">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold uppercase tracking-wider">Lunas</span>
                            @endif
                        </td>
                        @if(auth()->user()->can('edit credit-disbursements') || auth()->user()->can('delete credit-disbursements'))
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @can('edit credit-disbursements')
                                        <a href="{{ route('credit-disbursements.edit', $item->id) }}"
                                            class="p-2 text-orange-500 hover:bg-orange-100 rounded-lg transition-all duration-200 hover:scale-110"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete credit-disbursements')
                                        <button type="button"
                                            onclick="confirmDeleteDisbursement({{ $item->id }}, '{{ $item->customer_name }}')"
                                            class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all duration-200 hover:scale-110"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr class="bg-white/40 border-b border-white/40">
                        <td colspan="{{ (auth()->user()->can('edit credit-disbursements') || auth()->user()->can('delete credit-disbursements')) ? 11 : 10 }}" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-100/50 rounded-full flex items-center justify-center mb-1">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="max-w-xs mx-auto">
                                    <span class="block text-base font-bold text-gray-700">Tidak Ada Data</span>
                                    <span class="text-sm text-gray-400 font-medium">Belum ada data pencairan untuk periode ini.</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $disbursements->links() }}
    </div>

    <script>
        function confirmDeleteDisbursement(id, name) {
            Swal.fire({
                title: 'Hapus pencairan ' + name + '?',
                text: "Data pencairan akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            })
        }
    </script>
</div>
