<div>
    {{-- Tab Navigation --}}
    <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-4">
        @php
            $tabs = [
                'sp1' => ['label' => 'SP-1', 'color' => 'yellow', 'icon' => '⚠️'],
                'sp2' => ['label' => 'SP-2', 'color' => 'orange', 'icon' => '🔶'],
                'sp3' => ['label' => 'SP-3', 'color' => 'red', 'icon' => '🔴'],
                'panggilan' => ['label' => 'Panggilan', 'color' => 'purple', 'icon' => '📋'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <button wire:click="switchTab('{{ $key }}')"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl transition-all duration-200
                    {{ $activeTab === $key
                        ? 'bg-' . $tab['color'] . '-100 text-' . $tab['color'] . '-800 border-2 border-' . $tab['color'] . '-300 shadow-md'
                        : 'bg-white/60 text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                <span>{{ $tab['icon'] }}</span>
                {{ $tab['label'] }}
                @if($counts[$key] > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full
                        {{ $activeTab === $key ? 'bg-' . $tab['color'] . '-200 text-' . $tab['color'] . '-900' : 'bg-gray-200 text-gray-700' }}">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Search & Controls --}}
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
                    placeholder="Cari berdasarkan Nama Nasabah, No. Surat...">
            </div>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm font-medium text-gray-700 whitespace-nowrap">Show:</label>
                <select wire:model.live="perPage"
                    class="bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 backdrop-blur-sm transition-all">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                </select>
            </div>

            @can('create warning-letters')
                <a href="{{ route('warning-letters.create', ['type' => $activeTab]) }}"
                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30 whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Surat
                </a>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-white/40">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 backdrop-blur-sm">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Nasabah</th>
                    <th scope="col" class="px-6 py-3">No. Surat</th>
                    <th scope="col" class="px-6 py-3">No. Perjanjian</th>
                    <th scope="col" class="px-6 py-3">Tunggakan</th>
                    <th scope="col" class="px-6 py-3">Tanggal Surat</th>
                    <th scope="col" class="px-6 py-3">Batas Waktu</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    <tr wire:key="letter-{{ $letter->id }}"
                        class="bg-white/40 border-b border-white/40 hover:bg-white/60 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $letters->total() - (($letters->currentPage() - 1) * $letters->perPage()) - $loop->index }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $letter->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                            {{ $letter->letter_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            {{ $letter->credit_agreement_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($letter->tunggakan_amount)
                                <span class="font-bold text-red-600">Rp {{ number_format($letter->tunggakan_amount, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $letter->letter_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($letter->deadline_date)
                                @php
                                    $isPast = $letter->deadline_date->isPast();
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $isPast ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $letter->deadline_date->format('d M Y') }}
                                    @if($isPast) (Lewat) @endif
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('warning-letters.show', $letter->id) }}"
                                    target="_blank"
                                    class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-all duration-200 hover:scale-110"
                                    title="Download Word">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                                @can('create warning-letters')
                                    <a href="{{ route('warning-letters.edit', $letter->id) }}"
                                        class="p-2 text-orange-500 hover:bg-orange-100 rounded-lg transition-all duration-200 hover:scale-110"
                                        title="Ubah Surat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button"
                                        onclick="confirmDeleteLetter({{ $letter->id }}, '{{ $letter->customer->name ?? '' }}')"
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
                    </tr>
                @empty
                    <tr class="bg-white/40 border-b border-white/40">
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium">Belum ada surat untuk kategori ini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $letters->links() }}
    </div>

    <script>
        function confirmDeleteLetter(id, name) {
            Swal.fire({
                title: 'Hapus surat untuk ' + name + '?',
                text: "Data surat akan dihapus.",
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
