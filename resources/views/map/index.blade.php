@extends('layouts.dashboard')

@section('title', 'Global Map Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: calc(100vh - 200px);
            /* Adjust based on header/footer */
            width: 100%;
            border-radius: 0.75rem;
            z-index: 0;
        }
    </style>
@endpush

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Global Map</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full p-4">
        <div
            class="mb-4 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-sm p-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Peta Global BPR Puriseger Sentosa</h1>
                <p class="text-sm text-gray-600">Menampilkan lokasi rumah debitur, tempat usaha, dan agunan dalam satu peta
                    interaktif.</p>
            </div>

            <div class="flex gap-4 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span> Rumah Debitur
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span> Tempat Usaha
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span> Agunan (Collateral)
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span> Kunjungan
                </div>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="map-search"
                    class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    placeholder="Cari Debitur, Usaha, Agunan, atau AO..." autocomplete="off">
                <!-- Search Results Dropdown -->
                <ul id="search-results"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                </ul>
            </div>
        </div>

        <!-- Map Container with Relative Positioning for absolute sidebar -->
        <div class="relative w-full h-full">
            <div id="map" class="shadow-xl border border-gray-200"></div>

            <!-- Floating Sidebar for Live Tracking -->
            @if(isset($gpsTrackers) && $gpsTrackers->count() > 0)
                <div
                    class="absolute top-4 right-4 z-[400] bg-white/95 backdrop-blur-md border border-gray-200 rounded-xl shadow-2xl w-72 max-h-[80%] flex flex-col overflow-hidden transition-all duration-300">
                    <div
                        class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                            <h3 class="font-bold text-gray-900 text-sm tracking-wide">Live Tracking AO</h3>
                        </div>
                    </div>
                    <div class="overflow-y-auto p-2 space-y-1 max-h-[300px] custom-scrollbar">
                        @foreach($gpsTrackers as $tracker)
                            <button
                                onclick="openLiveTracking('{{ $tracker->imei }}', '{{ addslashes($tracker->user->name) }}', '{{ addslashes($tracker->name) }}')"
                                class="w-full text-left p-3 rounded-lg hover:bg-blue-50 transition-colors border border-transparent hover:border-blue-100 group flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-gray-800 truncate group-hover:text-blue-700 transition-colors">
                                        {{ $tracker->user->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $tracker->name }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Live Tracking Modal -->
    <div id="tracking-modal"
        class="fixed inset-0 z-[1000] hidden items-center justify-center bg-gray-900/80 backdrop-blur-sm transition-opacity">
        <div class="relative bg-white rounded-2xl shadow-2xl w-[95%] max-w-6xl h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300"
            id="tracking-modal-content">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 leading-tight" id="tracking-modal-title">Live Tracking AO
                        </h3>
                        <p class="text-xs font-medium text-gray-500">TrackSolidPro Platform Integration</p>
                    </div>
                </div>
                <button onclick="closeLiveTracking()"
                    class="text-gray-400 bg-gray-50 hover:bg-red-50 hover:text-red-500 rounded-xl text-sm w-10 h-10 flex justify-center items-center transition-colors">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
            <!-- Modal Body (Iframe) -->
            <div class="flex-grow w-full relative bg-gray-50">
                <div id="tracking-loading"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm z-10 transition-opacity duration-300">
                    <div class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                    <p class="text-gray-600 font-medium">Menyambungkan ke TrackSolidPro...</p>
                    <p class="text-xs text-gray-400 mt-2">Pastikan perangkat GPS aktif dan terhubung ke jaringan.</p>
                </div>
                <iframe id="tracking-iframe" class="absolute inset-0 w-full h-full border-0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Basemaps
            const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            });

            const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri'
            });

            // Initial View (Centering roughly on East Java / Puri Office)
            const map = L.map('map', {
                center: [-7.487391, 112.440067],
                zoom: 10,
                layers: [osm] // Default layer
            });

            const baseMaps = {
                "OpenStreetMap": osm,
                "Google Streets": googleStreets,
                "Google Satellite": googleHybrid,
                "Esri Satellite": esriSatellite
            };

            L.control.layers(baseMaps).addTo(map);

            // Office Marker
            const officeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color: #facc15; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);'></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            L.marker([-7.487391, 112.440067], { icon: officeIcon }).addTo(map)
                .bindPopup("<b>Kantor Pusat</b><br>Titik Acuan");

            // Kantor Kas Mojosari Marker
            L.marker([-7.519722, 112.557222], { icon: officeIcon }).addTo(map)
                .bindPopup("<b>Kantor Kas Mojosari</b><br>Cabang");


            // Data from Controller
            const customers = @json($customers);
            const businesses = @json($businesses);
            const collaterals = @json($collaterals);
            const visits = @json($visits);

            // Store all markers for filtering
            let allMarkers = [];
            const markerLayerGroup = L.layerGroup().addTo(map);

            // Helper: Date Logic
            const getPopupFooter = (createdAt, updatedAt) => {
                const createdDate = new Date(createdAt);
                const updatedDate = new Date(updatedAt);
                const isUpdated = updatedDate.getTime() > createdDate.getTime();
                const date = isUpdated ? updatedDate : createdDate;
                const label = isUpdated ? 'Diupdate' : 'Ditambahkan';
                const formattedDate = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                return `<div class="mt-3 pt-2 border-t border-gray-100 text-right">
                                <p class="text-[10px] text-gray-400 italic">${label} ${formattedDate}</p>
                            </div>`;
            };

            // 1. Customers (Blue)
            customers.forEach(customer => {
                if (customer.latitude && customer.longitude) {
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color: #3b82f6; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 2px rgba(0,0,0,0.3);'></div>",
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    let evaluationsList = '';
                    let searchTerms = [customer.name, customer.address, 'Rumah Debitur'];

                    if (customer.evaluations && customer.evaluations.length > 0) {
                        evaluationsList = '<div class="mt-2 bg-blue-50/50 p-2 rounded-md"><p class="text-xs font-semibold text-blue-800 mb-1">Evaluasi:</p>';
                        customer.evaluations.forEach(ev => {
                            const aoName = ev.user ? ev.user.name : '-';
                            evaluationsList += `<div class="flex justify-between items-center text-[10px] text-gray-700 mb-0.5 last:mb-0">
                                    <span>${ev.application_id || 'Draft'}</span>
                                    <span class="text-gray-500 font-medium">${aoName}</span>
                                </div>`;
                            searchTerms.push(ev.application_id);
                            searchTerms.push(aoName);
                        });
                        evaluationsList += '</div>';
                    }

                    const footer = getPopupFooter(customer.created_at, customer.updated_at);
                    const distance = customer.path_distance ? `${customer.path_distance} km` : '-';

                    const addressParts = [
                        customer.address || '',
                        customer.village ? `Desa ${customer.village}` : '',
                        customer.district ? `Kec. ${customer.district}` : '',
                        customer.regency ? `Kab. ${customer.regency}` : '',
                        customer.province || ''
                    ];
                    const fullAddress = addressParts.filter(part => part).join(', ') || 'Alamat tidak tersedia';

                    const imageSrc = customer.photo_path ? customer.photo_path.split('/').pop() : '';
                    const customerImage = imageSrc
                        ? `<div class="relative w-full h-32 mb-3 rounded-lg overflow-hidden shadow-sm">
                                    <img src="/media/customers/photos/${imageSrc}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500" alt="Foto Debitur">
                               </div>`
                        : '';

                    const marker = L.marker([customer.latitude, customer.longitude], { icon: icon })
                        .bindPopup(`
                                <div class="font-sans text-sm min-w-[220px]">
                                    <div class="flex items-center gap-2 mb-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query=${customer.latitude},${customer.longitude}" target="_blank" class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 hover:underline cursor-pointer" title="Buka di Google Maps">
                                        Rumah Debitur
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                    <span class="ml-auto text-[10px] text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        ${distance}
                                    </span>
                                    </div>
                                    ${customerImage}
                                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">${customer.name}</h3>
                                    <p class="text-xs text-gray-500 leading-snug mb-2">${fullAddress}</p>
                                    ${evaluationsList}
                                    ${footer}
                                </div>
                            `);

                    markerLayerGroup.addLayer(marker);
                    allMarkers.push({
                        marker: marker,
                        search: searchTerms.join(' ').toLowerCase()
                    });
                }
            });

            // 2. Businesses (Green)
            businesses.forEach(biz => {
                if (biz.business_latitude && biz.business_longitude) {
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color: #22c55e; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 2px rgba(0,0,0,0.3);'></div>",
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    const name = biz.customer_entreprenuership_name || (biz.customer ? biz.customer.name : 'Unknown');
                    const customerName = biz.customer ? biz.customer.name : '-'; // Get Customer Name

                    // Construct Address
                    const addressParts = [
                        biz.business_village ? `Desa ${biz.business_village}` : '',
                        biz.business_district ? `Kec. ${biz.business_district}` : '',
                        biz.business_regency ? `Kab. ${biz.business_regency}` : '',
                        biz.business_province || ''
                    ];
                    const address = addressParts.filter(part => part).join(', ') || 'Alamat tidak tersedia';

                    const aoNameBiz = biz.user ? biz.user.name : '-';
                    const bizImage = biz.business_detail_1_path
                        ? `<div class="relative w-full h-32 mb-3 rounded-lg overflow-hidden shadow-sm">
                                    <img src="/media/evaluations/photos/${biz.business_detail_1_path}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500" alt="Foto Usaha">
                               </div>`
                        : '';

                    let distance = '-';
                    if (biz.business_latitude && biz.business_longitude) {
                        distance = (map.distance([-7.487391, 112.440067], [biz.business_latitude, biz.business_longitude]) / 1000).toFixed(2) + ' km';
                    }

                    const footer = getPopupFooter(biz.created_at, biz.updated_at);

                    const marker = L.marker([biz.business_latitude, biz.business_longitude], { icon: icon })
                        .bindPopup(`
                                <div class="font-sans text-sm min-w-[240px]">
                                    <div class="flex items-center gap-2 mb-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query=${biz.business_latitude},${biz.business_longitude}" target="_blank" class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition-colors text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 hover:underline cursor-pointer" title="Buka di Google Maps">
                                        Tempat Usaha
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                    <span class="ml-auto text-[10px] text-gray-500 flex items-center gap-1">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            ${distance}
                                        </span>
                                    </div>
                                    ${bizImage}
                                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-0.5">${name}</h3>
                                    <p class="text-xs text-green-600 font-medium mb-1">${biz.customer_entreprenuership_type || 'Jenis Usaha tidak tersedia'}</p>
                                    <p class="text-xs text-gray-500 leading-snug mb-3">${address}</p>

                                    <div class="bg-gray-50 p-2.5 rounded-md border border-gray-100 flex flex-col gap-2">
                                        <div class="flex items-center gap-2" title="Nasabah">
                                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <p class="font-bold text-gray-700 text-xs truncate" title="${customerName}">${customerName}</p>
                                        </div>
                                        <div class="w-full h-px bg-gray-200"></div>
                                        <div class="flex items-center gap-2" title="Account Officer">
                                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <p class="font-bold text-gray-700 text-xs truncate" title="${aoNameBiz}">${aoNameBiz}</p>
                                        </div>
                                    </div>
                                    ${footer}
                                </div>
                            `);

                    markerLayerGroup.addLayer(marker);
                    allMarkers.push({
                        marker: marker,
                        search: [name, biz.customer_entreprenuership_type, biz.application_id, aoNameBiz, 'Tempat Usaha'].join(' ').toLowerCase()
                    });
                }
            });

            // 3. Collaterals (Red)
            collaterals.forEach(col => {
                if (col.latitude && col.longitude) {
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color: #ef4444; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 2px rgba(0,0,0,0.3);'></div>",
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    let aoNameCol = '-';
                    let appIdCol = '-';
                    if (col.evaluation) {
                        appIdCol = col.evaluation.application_id || 'Draft';
                        if (col.evaluation.user) {
                            aoNameCol = col.evaluation.user.name;
                        }
                    }

                    const colImageSrc = col.vehicle_image_1 || col.property_image_1 || col.image_proof;
                    const colImage = colImageSrc
                        ? `<div class="relative w-full h-32 mb-3 rounded-lg overflow-hidden shadow-sm">
                                    <img src="/media/evaluations/collaterals/${colImageSrc}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500" alt="Foto Agunan">
                               </div>`
                        : '';

                    let distance = '-';
                    if (col.latitude && col.longitude) {
                        distance = (map.distance([-7.487391, 112.440067], [col.latitude, col.longitude]) / 1000).toFixed(2) + ' km';
                    }

                    const formattedValue = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(col.market_value);
                    const footer = getPopupFooter(col.created_at, col.updated_at);

                    const marker = L.marker([col.latitude, col.longitude], { icon: icon })
                        .bindPopup(`
                                 <div class="font-sans text-sm min-w-[240px]">
                                    <div class="flex items-center gap-2 mb-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query=${col.latitude},${col.longitude}" target="_blank" class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition-colors text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 hover:underline cursor-pointer" title="Buka di Google Maps">
                                        Agunan (${col.type})
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                    <span class="ml-auto text-[10px] text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        ${distance}
                                    </span>
                                    </div>
                                    ${colImage}
                                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">${col.owner_name}</h3>
                                    <p class="text-xs text-gray-500 leading-snug mb-3">${col.location_address || 'Alamat tidak tersedia'}</p>

                                    <div class="mb-3 space-y-2">
                                         <div class="flex justify-between items-center bg-gray-50 p-2 rounded-md border border-gray-100">
                                            <span class="text-[10px] text-gray-500 uppercase tracking-wider">Nilai Pasar</span>
                                            <span class="font-bold text-gray-900 text-xs">${formattedValue}</span>
                                        </div>
                                         <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2 rounded-md border border-gray-100">
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-wider">App ID</p>
                                                <p class="font-medium text-gray-700 truncate">${appIdCol}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-wider">AO</p>
                                                <p class="font-medium text-gray-700 truncate" title="${aoNameCol}">${aoNameCol}</p>
                                            </div>
                                        </div>
                                    </div>
                                    ${footer}
                                </div>
                            `);

                    markerLayerGroup.addLayer(marker);
                    allMarkers.push({
                        marker: marker,
                        search: [col.owner_name, col.location_address, col.type, appIdCol, aoNameCol, 'Agunan'].join(' ').toLowerCase()
                    });
                }
            });

            // 4. Visits (Purple)
            visits.forEach(visit => {
                if (visit.latitude && visit.longitude) {
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color: #a855f7; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 2px rgba(0,0,0,0.3);'></div>",
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    const customerName = visit.customer ? visit.customer.name : '-';
                    const aoName = visit.user ? visit.user.name : '-';

                    const addressParts = [
                        visit.address || '',
                        visit.village ? `Desa ${visit.village}` : '',
                        visit.district ? `Kec. ${visit.district}` : '',
                        visit.regency ? `Kab. ${visit.regency}` : '',
                        visit.province || ''
                    ];
                    const fullAddress = addressParts.filter(part => part).join(', ') || 'Alamat tidak tersedia';

                    let distance = '-';
                    distance = (map.distance([-7.487391, 112.440067], [visit.latitude, visit.longitude]) / 1000).toFixed(2) + ' km';

                    const footer = getPopupFooter(visit.created_at, visit.updated_at);

                    const visitImage = visit.photo_path
                        ? `<div class="relative w-full h-32 mb-3 rounded-lg overflow-hidden shadow-sm">
                                    <img src="/media/${visit.photo_path}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500" alt="Foto Kunjungan">
                               </div>`
                        : '';

                    const marker = L.marker([visit.latitude, visit.longitude], { icon: icon })
                        .bindPopup(`
                                <div class="font-sans text-sm min-w-[240px]">
                                    <div class="flex items-center gap-2 mb-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query=${visit.latitude},${visit.longitude}" target="_blank" class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 hover:underline cursor-pointer" title="Buka di Google Maps">
                                        Kunjungan
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                    <span class="ml-auto text-[10px] text-gray-500 flex items-center gap-1">
                                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            ${distance}
                                        </span>
                                    </div>
                                    ${visitImage}
                                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">${customerName}</h3>
                                    <p class="text-xs text-purple-600 font-bold mb-1">Kol: ${visit.kolektibilitas}</p>
                                    <p class="text-xs text-gray-500 leading-snug mb-3">${fullAddress}</p>

                                    <div class="bg-gray-50 p-2.5 rounded-md border border-gray-100 flex flex-col gap-2">
                                        <div class="flex items-center gap-2" title="Account Officer">
                                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <p class="font-bold text-gray-700 text-xs truncate" title="${aoName}">${aoName}</p>
                                        </div>
                                    </div>
                                    ${footer}
                                </div>
                            `);

                    markerLayerGroup.addLayer(marker);
                    allMarkers.push({
                        marker: marker,
                        search: [customerName, aoName, 'Kunjungan', visit.village, visit.district].join(' ').toLowerCase()
                    });
                }
            });

            // Search Functionality with Dropdown & Redirection
            const searchInput = document.getElementById('map-search');
            const searchResults = document.getElementById('search-results');

            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase();
                searchResults.innerHTML = ''; // Clear previous results

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                const matches = allMarkers.filter(item => item.search.includes(query));

                if (matches.length > 0) {
                    searchResults.classList.remove('hidden');
                    matches.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-0';

                        // Extract a meaningful label from search terms (simplified)
                        // Assuming first term is name/owner
                        let label = item.search.split(' ').slice(0, 3).join(' ').replace(/\b\w/g, l => l.toUpperCase());

                        // Add marker type icon/color
                        let typeColor = 'gray'; // Default
                        if (item.search.includes('rumah debitur')) typeColor = 'blue';
                        else if (item.search.includes('tempat usaha')) typeColor = 'green';
                        else if (item.search.includes('agunan')) typeColor = 'red';
                        else if (item.search.includes('kunjungan')) typeColor = 'purple';

                        li.innerHTML = `<span class="inline-block w-2 h-2 rounded-full bg-${typeColor}-500 mr-2"></span> ${label}`;

                        li.addEventListener('click', function () {
                            // 1. Center Map
                            map.flyTo(item.marker.getLatLng(), 18, {
                                animate: true,
                                duration: 1.5
                            });

                            // 2. Open Popup
                            item.marker.openPopup();

                            // 3. Clear Search
                            searchInput.value = '';
                            searchResults.classList.add('hidden');
                        });

                        searchResults.appendChild(li);
                    });
                } else {
                    searchResults.classList.add('hidden');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        });

        // Live Tracking Modal Functions
        function openLiveTracking(imei, aoName, trackerName) {
            const modal = document.getElementById('tracking-modal');
            const modalContent = document.getElementById('tracking-modal-content');
            const iframe = document.getElementById('tracking-iframe');
            const loading = document.getElementById('tracking-loading');
            const title = document.getElementById('tracking-modal-title');

            title.innerText = `Live Tracking: ${aoName} - ${trackerName}`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Animation delay
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);

            loading.classList.remove('opacity-0');
            loading.classList.remove('pointer-events-none');

            iframe.onload = function () {
                loading.classList.add('opacity-0');
                loading.classList.add('pointer-events-none');
            };

            iframe.src = `https://tracksolidpro.com/resource/dev/index.html?t=246074#/monitorTracking?imei=${imei}&googleMapRegion=`;
        }

        function closeLiveTracking() {
            const modal = document.getElementById('tracking-modal');
            const modalContent = document.getElementById('tracking-modal-content');
            const iframe = document.getElementById('tracking-iframe');

            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                iframe.src = ''; // Clear source to stop background tracking
            }, 300);
        }
    </script>
@endpush