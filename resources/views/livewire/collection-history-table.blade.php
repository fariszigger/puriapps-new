<div>
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <div class="w-full md:w-1/2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="search"
                    class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white/50 focus:ring-blue-500 focus:border-blue-500 backdrop-blur-sm transition-all"
                    placeholder="Cari berdasarkan Nama, KTP, atau Alamat..">
                
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm font-medium text-gray-700 whitespace-nowrap">Show:</label>
                <select wire:model.live="perPage" id="perPage"
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 backdrop-blur-sm transition-all">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    @unless(auth()->user()->hasRole('AO'))
    <!-- Period Filter Section -->
    <div class="flex flex-wrap items-center gap-3 bg-white/40 backdrop-blur-md p-3 rounded-xl border border-white/50 shadow-sm mb-6">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-blue-100 text-blue-600 rounded-lg border border-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <label class="text-sm font-bold text-gray-700">Periode:</label>
            <select wire:model.live="filter"
                class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 transition-all shadow-sm">
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
            </select>
        </div>

        @if($filter === 'daily')
            <div class="flex items-center gap-2 pl-3 border-l border-white/60">
                <label class="text-sm font-medium text-gray-600">Tanggal:</label>
                <input type="date" wire:model.live="selectedDate"
                    class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 transition-all shadow-sm">
            </div>
        @elseif($filter === 'weekly')
            <div class="flex items-center gap-2 pl-3 border-l border-white/60">
                <label class="text-sm font-medium text-gray-600">Bulan:</label>
                <input type="month" wire:model.live="selectedMonth"
                    class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 transition-all shadow-sm">
            </div>
            <div class="flex items-center gap-2 pl-3 border-l border-white/60">
                <label class="text-sm font-medium text-gray-600">Minggu Ke-:</label>
                <select wire:model.live="selectedWeek"
                    class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 transition-all shadow-sm">
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
            <div class="flex items-center gap-2 pl-3 border-l border-white/60">
                <label class="text-sm font-medium text-gray-600">Bulan:</label>
                <input type="month" wire:model.live="selectedMonth"
                    class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 transition-all shadow-sm">
            </div>
        @endif

        <div class="flex items-center gap-2 pl-3 border-l border-white/60">
            <div class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg border border-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <label class="text-sm font-bold text-gray-700">AO:</label>
            <select wire:model.live="aoCodeFilter"
                class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-all shadow-sm min-w-[120px]">
                <option value="">Semua AO</option>
                @foreach($aoUsers as $ao)
                    <option value="{{ $ao->code }}">{{ $ao->code }} - {{ $ao->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 pl-3 border-l border-white/60">
            <div class="p-1.5 bg-amber-100 text-amber-600 rounded-lg border border-amber-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
            </div>
            <label class="text-sm font-bold text-gray-700">Penagihan Ke:</label>
            <select wire:model.live="penagihanFilter"
                class="bg-white/70 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2 transition-all shadow-sm min-w-[80px]">
                <option value="">Semua</option>
                @for($i = 1; $i <= $maxPenagihan; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div wire:loading class="px-2">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>
    @endunless

    <!-- Table Section -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-white/40">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 backdrop-blur-sm">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Nasabah</th>
                    <th scope="col" class="px-6 py-3">Alamat</th>
                    <th scope="col" class="px-6 py-3">AO</th>
                    <th scope="col" class="px-6 py-3 text-center">Tindakan Terakhir</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr class="bg-white/40 border-b border-white/40 hover:bg-white/60 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $customer->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ Str::limit($customer->address, 50, '...') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $customer->tindakan_terakhir_ao ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $actionInfo = $customer->tindakan_terakhir;
                                $isLetter = str_contains(strtolower($actionInfo), 'surat');
                            @endphp
                            <div class="flex flex-col items-center justify-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm whitespace-nowrap {{ $isLetter ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-orange-100 text-orange-700 border border-orange-200' }}">
                                    {{ $actionInfo }}
                                </span>
                                @if($customer->tindakan_terakhir_tanggal)
                                    <span class="text-[10px] text-gray-500 mt-1 tracking-wider uppercase">
                                        {{ $customer->tindakan_terakhir_tanggal->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('collection-history.print', $customer->id) }}" target="_blank"
                                class="inline-flex items-center justify-center p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-all duration-200 hover:scale-110"
                                title="Print History Penagihan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                <span class="ml-2 text-sm font-bold">Print</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white/40 border-b border-white/40">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="text-lg font-medium text-gray-500 mb-1">Data Nasabah Tidak Ditemukan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $customers->links() }}
    </div>
</div>
