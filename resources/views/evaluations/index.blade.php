@extends('layouts.dashboard')

@section('title', 'Daftar Evaluasi')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Daftar Evaluasi</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <livewire:evaluations-table />
    </div>

    <!-- Restore Modal -->
    @can('restore evaluations')
        <div id="restore-modal" tabindex="-1" aria-hidden="true"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Restore Evaluasi Terhapus
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="restore-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-6 space-y-6">
                        @if(isset($deletedEvaluations) && $deletedEvaluations->count() > 0)
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Nasabah</th>
                                                <th scope="col" class="px-6 py-3">No. Pengajuan</th>
                                                <th scope="col" class="px-6 py-3">Tanggal Dihapus</th>
                                                <th scope="col" class="px-6 py-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deletedEvaluations as $deleted)
                                                <tr class="bg-white border-b hover:bg-gray-50">
                                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                        {{ $deleted->customer->name ?? 'Unknown' }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        {{ $deleted->loan_number }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        {{ $deleted->deleted_at->format('d M Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <form action="{{ route('evaluations.restore', $deleted->id) }}" method="POST">
                                                            @csrf
                                                            <button type="button" onclick="confirmRestore(this.form)"
                                                                class="font-medium text-blue-600 hover:underline">Restore</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                        <p class="text-gray-500 text-center">Tidak ada data evaluasi yang dihapus.</p>
                    @endif
                </div>
            </div>
        </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data evaluasi akan dihapus secara soft delete. Anda mungkin bisa memulihkannya nanti.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmRestore(form) {
            Swal.fire({
                title: 'Pulihkan Evaluasi?',
                text: "Data evaluasi akan dikembalikan ke daftar aktif.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmSend(form) {
            Swal.fire({
                title: 'Kirim untuk Persetujuan?',
                text: "Evaluasi akan dikirim ke Kabag untuk ditinjau dan disetujui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmRevoke(form) {
            Swal.fire({
                title: 'Tarik Kembali Evaluasi?',
                text: "Evaluasi akan ditarik kembali ke status draft.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tarik Kembali!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush