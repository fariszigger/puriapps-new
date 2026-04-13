@extends('layouts.dashboard')

@section('title', 'Daftar Debitur')

@section('breadcrumb-items')
  <li class="inline-flex items-center">
    <div class="flex items-center">
      <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 6 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
      </svg>
      <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Daftar Debitur</span>
    </div>
  </li>
@endsection

@section('content')
  <div class="w-full p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Daftar Debitur</h1>
      <div class="flex items-center gap-2">
        @can('restore customers')
          <button data-modal-target="restore-customer-modal" data-modal-toggle="restore-customer-modal"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 transition-all shadow-lg hover:shadow-amber-500/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
              </path>
            </svg>
            Restore Debitur
          </button>
        @endcan
        @can('view customers')
          <a href="{{ route('customers.export-xls') }}"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 transition-all shadow-lg hover:shadow-green-500/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Excel
          </a>
          <a href="{{ route('customers.export-pdf') }}"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 transition-all shadow-lg hover:shadow-red-500/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1m4 0h1m-5 4h5m-5 4h5"></path>
            </svg>
            PDF
          </a>
        @endcan
        @can('create customers')
          <a href="{{ route('customers.create') }}"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Debitur Baru
          </a>
        @endcan
      </div>
    </div>

    <livewire:customer-table />
  </div>

  <!-- Restore Modal -->
  @can('restore customers')
    <div id="restore-customer-modal" tabindex="-1" aria-hidden="true"
      class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
      <div class="relative w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <!-- Modal header -->
          <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
              Restore Debitur Terhapus
            </h3>
            <button type="button"
              class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
              data-modal-hide="restore-customer-modal">
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
            @if(isset($deletedCustomers) && $deletedCustomers->count() > 0)
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3">Nama</th>
                      <th scope="col" class="px-6 py-3">No. KTP</th>
                      <th scope="col" class="px-6 py-3">No. Telp</th>
                      <th scope="col" class="px-6 py-3">Tanggal Dihapus</th>
                      <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($deletedCustomers as $deleted)
                      <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                          {{ $deleted->name }}
                        </td>
                        <td class="px-6 py-4">
                          {{ $deleted->identity_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                          {{ $deleted->phone_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                          {{ $deleted->deleted_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                          <form action="{{ route('customers.restore', $deleted->id) }}" method="POST">
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
            @else
              <p class="text-gray-500 text-center">Tidak ada data debitur yang dihapus.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endcan
@endsection

@push('scripts')
  <script>
    function confirmRestore(form) {
      Swal.fire({
        title: 'Pulihkan Debitur?',
        text: "Data debitur akan dikembalikan ke daftar aktif.",
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
  </script>
@endpush