<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') - PuriApps</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        #global-loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 60px;
            /* Matches navbar height roughly to peak out from behind */
            width: 0;
            border-bottom: 3px solid #2563eb;
            z-index: 50;
            /* On top of navbar (z-20) to ensure visibility */
            transition: width 0.3s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
            pointer-events: none;
        }

        #global-loading-bar.loading {
            opacity: 1;
            width: 90%;
        }

        #global-loading-bar.complete {
            width: 100%;
            opacity: 0;
        }
    </style>
</head>

<body
    class="min-h-screen bg-fixed bg-gray-50 dark:bg-gray-900 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 text-gray-900">

    <div id="global-loading-bar"></div>
    <nav
        class="bg-white/50 backdrop-blur-lg border-b border-white/40 px-4 lg:px-6 py-2.5 fixed w-full z-20 top-0 left-0 shadow-sm">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <a href="#" class="flex items-center">
                <img src="{{ asset('images/logo-icon.png') }}" class="mr-3 h-6 sm:h-9 drop-shadow-md"
                    alt="PuriApps Logo" />
                <span class="self-center text-xl font-semibold whitespace-nowrap drop-shadow-md">
                    <span class="text-blue-700">Puri</span><span class="text-red-600">Apps</span>
                </span>
            </a>
            <div class="flex items-center md:order-2">
                
                <livewire:navbar-notifications />

                <button type="button"
                    class="flex items-center mr-3 text-sm bg-white/50 backdrop-blur-md rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 ring-offset-2 ring-offset-gray-100 pr-3 border border-white/50"
                    id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                    data-dropdown-placement="bottom">
                    <span class="sr-only">Open user menu</span>
                    <div
                        class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold border border-white/50 mr-2">
                        {{ auth()->user()->initials() ?? substr(auth()->user()->username, 0, 2) }}
                    </div>
                    <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span>
                </button>
                <!-- Dropdown menu -->
                <div class="hidden z-50 my-4 text-base list-none bg-white/80 backdrop-blur rounded divide-y divide-gray-100 shadow-xl border border-white/50"
                    id="user-dropdown">
                    <div class="py-3 px-4">
                        <span class="block text-sm text-gray-900">{{ auth()->user()->name }}</span>
                        <span
                            class="block text-sm font-medium text-gray-500 truncate">{{ auth()->user()->email ?? auth()->user()->username }}</span>
                    </div>
                    <ul class="py-1" aria-labelledby="user-menu-button">
                        <li>
                            <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        </li>
                        @can('manage users')
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">User Settings</a>
                            </li>
                        @endcan
                        @can('manage gps')
                            <li>
                                <a href="{{ route('gps-trackers.index') }}"
                                    class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Manajemen GPS</a>
                            </li>
                        @endcan
                        <li>
                            <a href="{{ route('profile.edit') }}"
                                class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Sign
                                    out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <livewire:notification-marquee />

    <div class="pt-20 pb-8 px-4 md:px-8 max-w-screen-xl mx-auto h-full">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                @yield('breadcrumb-items')
            </ol>
        </nav>

        @yield('content')

        <footer class="mt-8 text-center">
            <p class="text-xs text-gray-500 italic">
                &copy; {{ date('Y') }} PuriApps. Created by Tim IT BPR Puriseger Sentosa.
            </p>
        </footer>
    </div>

    <div id="bottom-nav-bar" x-data="{ showUserOverlay: false, showAddMenu: false }"
        class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[95%] max-w-lg z-50 transition-transform duration-300">

        <livewire:user-status-overlay />

        <div
            class="bg-white/70 backdrop-blur-lg border border-white/50 rounded-2xl shadow-2xl p-2 flex items-center justify-around">
            <a href="{{ route('home') }}"
                class="flex flex-col items-center justify-center p-2 rounded-xl transition-all {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-100/50' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="text-[10px] font-medium uppercase tracking-wider">Home</span>
            </a>

            @canany(['create customers', 'create evaluations', 'create customer-visits'])
                <div class="relative flex flex-col items-center justify-center">
                    <!-- Add Menu (Popup) -->
                    <div x-show="showAddMenu" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.away="showAddMenu = false"
                        class="absolute bottom-[calc(100%+1rem)] left-1/2 -translate-x-1/2 w-72 max-h-96 z-[60]" x-cloak>

                        <!-- Triangle Tip -->
                        <div
                            class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white rotate-45 border-r border-b border-gray-200">
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden flex flex-col">
                            <!-- Menu Items -->
                            <div class="overflow-y-auto p-2 space-y-1 custom-scrollbar">
                                @can('create customers')
                                <a href="{{ route('customers.create') }}"
                                    class="flex items-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-semibold text-gray-900">Debitur</p>
                                        <p class="text-[10px] text-gray-500">Add new customer</p>
                                    </div>
                                </a>
                                @endcan

                                @can('create evaluations')
                                <a href="{{ route('evaluations.create') }}"
                                    class="flex items-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-semibold text-gray-900">Evaluasi</p>
                                        <p class="text-[10px] text-gray-500">Record evaluation</p>
                                    </div>
                                </a>
                                @endcan

                                @can('create customer-visits')
                                <a href="{{ route('customer-visits.create') }}"
                                    class="flex items-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 to-rose-500 flex items-center justify-center text-white shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-semibold text-gray-900">Kunjungan</p>
                                        <p class="text-[10px] text-gray-500">Record customer visit</p>
                                    </div>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Button -->
                    <button @click="showAddMenu = !showAddMenu"
                        :class="showAddMenu ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-100/50'"
                        class="flex flex-col items-center justify-center p-2 rounded-xl transition-all outline-none relative">
                        <svg class="w-6 h-6 mb-1 transition-transform duration-300" :class="showAddMenu ? 'rotate-45' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-[10px] font-medium uppercase tracking-wider">Add</span>
                    </button>
                </div>
            @endcanany

            <a href="{{ route('map.index') }}"
                class="flex flex-col items-center justify-center p-2 rounded-xl transition-all {{ request()->routeIs('map.index') ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-100/50' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                    </path>
                </svg>
                <span class="text-[10px] font-medium uppercase tracking-wider">Map</span>
            </a>

            @can('manage users')
                <button @click="showUserOverlay = !showUserOverlay"
                    :class="showUserOverlay ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-100/50'"
                    class="flex flex-col items-center justify-center p-2 rounded-xl transition-all outline-none">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                    <span class="text-[10px] font-medium uppercase tracking-wider">Users</span>
                </button>
            @endcan
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastConfig = {
                toast: true,
                position: 'bottom-start',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            };

            @if(session('success'))
                Swal.mixin({
                    ...toastConfig,
                    customClass: {
                        popup: 'bg-green-500 text-white rounded-lg shadow-lg border border-green-600',
                        timerProgressBar: 'bg-green-700'
                    }
                }).fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            @if(session('error'))
                Swal.mixin({
                    ...toastConfig,
                    customClass: {
                        popup: 'bg-red-500 text-white rounded-lg shadow-lg border border-red-600',
                        timerProgressBar: 'bg-red-700'
                    }
                }).fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif

            // Loading Bar Logic
            const loadingBar = document.getElementById('global-loading-bar');

            window.addEventListener('beforeunload', function () {
                loadingBar.classList.add('loading');
            });

            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    const target = this.getAttribute('target');

                    if (href && href !== '#' && !href.startsWith('javascript:') && !href.startsWith('#') && target !== '_blank') {
                        loadingBar.classList.add('loading');
                    }
                });
            });

            // If it's a redirect or back button, we might need to reset it
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    loadingBar.classList.remove('loading');
                    loadingBar.style.width = '0';
                }
            });

            // Bottom Nav Auto-hide on Scroll to Bottom
            const bottomNav = document.getElementById('bottom-nav-bar');
            window.addEventListener('scroll', function () {
                const scrollHeight = document.documentElement.scrollHeight;
                const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                const clientHeight = document.documentElement.clientHeight;

                // Threshold: 50px from bottom
                if (scrollTop + clientHeight >= scrollHeight - 50) {
                    bottomNav.classList.add('translate-y-[150%]'); // Hide
                } else {
                    bottomNav.classList.remove('translate-y-[150%]'); // Show
                }
            });

            // Livewire SweetAlert Event Listener
            window.addEventListener('swal:modal', event => {
                Swal.fire({
                    icon: event.detail[0].icon,
                    title: event.detail[0].title,
                    text: event.detail[0].text,
                });
            });
        });
    </script>
    @livewireScripts
    @stack('scripts')
</body>

</html>