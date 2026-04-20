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
                <input wire:model.live.debounce.300ms="search" type="search" id="visit-search"
                    class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white/50 focus:ring-blue-500 focus:border-blue-500 backdrop-blur-sm transition-all"
                    placeholder="Cari Nasabah, Alamat, Kolektibilitas, atau AO...">
            </div>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm font-medium text-gray-700 whitespace-nowrap">Show:</label>
                <select wire:model.live="perPage" id="perPage"
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 backdrop-blur-sm transition-all">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
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
                <option value="">Semua</option>
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

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-white/40">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 backdrop-blur-sm">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">SPK</th>
                    <th scope="col" class="px-6 py-3">Nasabah</th>
                    <th scope="col" class="px-6 py-3">Alamat</th>
                    <th scope="col" class="px-6 py-3">Kolektibilitas</th>
                    <th scope="col" class="px-6 py-3 text-right">Bakidebet</th>
                    <th scope="col" class="px-6 py-3">Ketemu</th>
                    <th scope="col" class="px-6 py-3">Hasil</th>
                    <th scope="col" class="px-6 py-3 text-center">AO</th>
                    <th scope="col" class="px-6 py-3 text-center">Ke-</th>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                    <tr wire:key="visit-{{ $visit->id }}"
                        class="bg-white/40 border-b border-white/40 hover:bg-white/60 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $visits->total() - (($visits->currentPage() - 1) * $visits->perPage()) - $loop->index }}
                        </td>
                        <td class="px-6 py-4 font-bold text-indigo-600 whitespace-nowrap">
                            {{ $visit->spk_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $visit->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate">{{ $visit->address ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $kolekMap = [
                                    '1' => ['label' => 'Lancar', 'color' => 'green'],
                                    '2' => ['label' => 'DPK', 'color' => 'yellow'],
                                    '3' => ['label' => 'Kurang Lancar', 'color' => 'orange'],
                                    '4' => ['label' => 'Diragukan', 'color' => 'red'],
                                    '5' => ['label' => 'Macet', 'color' => 'red'],
                                ];
                                $kolek = $kolekMap[$visit->kolektibilitas] ?? ['label' => $visit->kolektibilitas, 'color' => 'gray'];
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $kolek['color'] }}-100 text-{{ $kolek['color'] }}-800">
                                {{ $visit->kolektibilitas }} - {{ $kolek['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-red-600">
                            {{ $visit->baki_debet ? 'Rp ' . number_format($visit->baki_debet, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $visit->ketemu_dengan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($visit->hasil_penagihan === 'bayar')
                                <span class="text-green-600 font-semibold">Bayar Rp
                                    {{ number_format($visit->jumlah_bayar, 0, ',', '.') }}</span>
                            @elseif($visit->hasil_penagihan === 'janji_bayar')
                                <div class="flex flex-col gap-1">
                                    <span class="text-yellow-600 font-semibold">Janji
                                        {{ $visit->tanggal_janji_bayar ? \Carbon\Carbon::parse($visit->tanggal_janji_bayar)->format('d/m/Y') : '-' }}</span>
                                    @if($visit->jumlah_pembayaran)
                                        <span class="text-xs text-gray-500">Rp
                                            {{ number_format($visit->jumlah_pembayaran, 0, ',', '.') }}</span>
                                    @endif
                                    @if($visit->janji_bayar_fulfilled)
                                        <span class="inline-flex flex-col gap-0.5 px-2 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-800 w-fit shadow-sm mt-1">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Sudah Bayar Rp {{ number_format($visit->jumlah_pembayaran ?? 0, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] font-medium text-green-700">
                                                pd. {{ $visit->janji_bayar_fulfilled_at ? \Carbon\Carbon::parse($visit->janji_bayar_fulfilled_at)->format('d/m/Y') : '-' }}
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            @elseif($visit->hasil_penagihan === 'tidak_ada_janji')
                                <span class="text-red-500 font-semibold">Tidak Ada Janji</span>
                            @elseif($visit->hasil_penagihan === 'janji_lainnya')
                                <div class="flex flex-col gap-1">
                                    <span class="text-yellow-500 font-semibold">Janji Lainnya</span>
                                    @if($visit->janji_lainnya_desc)
                                        <span class="text-xs text-gray-500 max-w-[150px] truncate" title="{{ $visit->janji_lainnya_desc }}">{{ $visit->janji_lainnya_desc }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 rounded bg-gray-100/80 text-gray-700 font-bold uppercase text-xs tracking-wider" title="{{ $visit->user->name ?? '-' }}">
                                {{ $visit->user->code ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-center">{{ $visit->penagihan_ke }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $visit->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('customer-visits.report', $visit->id) }}" target="_blank"
                                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-all duration-200 hover:scale-110"
                                    title="Cetak Laporan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                </a>
                                @can('update customer-visits')
                                    <a href="{{ route('customer-visits.edit', $visit->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-all duration-200 hover:scale-110"
                                        title="Edit Kunjungan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                @endcan
                                @can('delete customer-visits')
                                    <button type="button"
                                        onclick="confirmDeleteVisit({{ $visit->id }}, '{{ $visit->customer->name ?? '' }}')"
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all duration-200 hover:scale-110"
                                        title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white/40 border-b border-white/40">
                        <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Belum ada data kunjungan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $visits->links() }}
    </div>

    <script>
        function confirmDeleteVisit(id, name) {
            Swal.fire({
                title: 'Yakin data kunjungan ' + name + ' untuk Dihapus ?',
                text: "Data akan dihapus (Soft Delete)",
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