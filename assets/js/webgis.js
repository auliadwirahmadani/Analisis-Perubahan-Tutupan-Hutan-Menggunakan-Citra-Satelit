/**
 * ForestWatch Interactive WebGIS with Leaflet.js
 */

let webGisMap = null;
let currentAlertsLayer = null;
let currentForestPolygonLayer = null;
let drawnItems = null;

function initForestWebGis(regionConfig, allRegions) {
    const mapElement = document.getElementById('mapContainer');
    if (!mapElement) return;

    const initialLat = regionConfig.lat || -0.9654;
    const initialLng = regionConfig.lng || 116.7321;
    const initialZoom = regionConfig.zoom || 11;

    // 1. Initialize Map
    webGisMap = L.map('mapContainer', {
        center: [initialLat, initialLng],
        zoom: initialZoom,
        zoomControl: true,
        attributionControl: true
    });

    // 2. Basemaps
    const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    }).addTo(webGisMap);

    const openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    });

    const cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        maxZoom: 19
    });

    const baseMaps = {
        "🛰️ Citra Satelit Esri (High-Res)": esriSatellite,
        "🗺️ OpenStreetMap": openStreetMap,
        "🌑 CartoDB Dark Matter": cartoDark
    };

    // 3. Layer Groups
    currentAlertsLayer = L.layerGroup().addTo(webGisMap);
    currentForestPolygonLayer = L.layerGroup().addTo(webGisMap);

    // 4. Draw Region Forest Coverage Simulation Polygon
    renderRegionForestOverlay(regionConfig);

    // 5. Load Deforestation Hotspot Alerts
    loadAlertGeoJson(regionConfig.id);

    // 6. Layer Controls
    const overlayMaps = {
        "🌲 Zona Klasifikasi Hutan": currentForestPolygonLayer,
        "🚨 Titik Peringatan Deforestasi (Hotspots)": currentAlertsLayer
    };

    L.control.layers(baseMaps, overlayMaps, { position: 'topright' }).addTo(webGisMap);

    // 7. Setup Drawing / AOI Tool
    setupAoiDrawing(webGisMap);

    // 8. Setup Side-by-Side / Swipe Map Feature
    setupSwipeComparison(webGisMap, regionConfig);
}

/**
 * Render Poligon Tutupan Hutan Berdasarkan Bounding Box Wilayah
 */
function renderRegionForestOverlay(regionConfig) {
    if (!currentForestPolygonLayer) return;
    currentForestPolygonLayer.clearLayers();

    const bounds = regionConfig.bounds;
    if (!bounds || bounds.length < 2) return;

    const latMin = bounds[0][0];
    const lngMin = bounds[0][1];
    const latMax = bounds[1][0];
    const lngMax = bounds[1][1];

    // Buat poligon simulasi zona konservasi & tutupan hutan
    const primaryForestPoly = [
        [latMin + 0.05, lngMin + 0.05],
        [latMin + 0.05, lngMax - 0.15],
        [latMax - 0.10, lngMax - 0.10],
        [latMax - 0.08, lngMin + 0.10]
    ];

    const poly = L.polygon(primaryForestPoly, {
        color: '#059669',
        weight: 2,
        fillColor: '#10b981',
        fillOpacity: 0.25,
        dashArray: '4, 4'
    }).bindPopup(`
        <div class="p-2">
            <h4 class="font-bold text-emerald-400 text-sm mb-1">Zona Inti Tutupan Hutan</h4>
            <p class="text-xs text-slate-300">${regionConfig.name}</p>
            <div class="mt-2 text-xs grid grid-cols-2 gap-1 border-t border-slate-700/60 pt-2">
                <div><span class="text-slate-400">Status:</span> Kawasan Lindung</div>
                <div><span class="text-slate-400">NDVI:</span> 0.74 - 0.82</div>
            </div>
        </div>
    `);

    currentForestPolygonLayer.addLayer(poly);
}

/**
 * Muat Titik Alert Deforestasi dari Endpoint REST GeoJSON
 */
function loadAlertGeoJson(regionId) {
    const url = `${window.APP_BASE_URL}/api/geojson/alerts?region=${encodeURIComponent(regionId || 'all')}`;

    fetch(url)
        .then(res => res.json())
        .then(geojson => {
            if (!geojson || !geojson.features) return;
            currentAlertsLayer.clearLayers();

            geojson.features.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;

                // Create custom pulsating HTML marker
                const customIcon = L.divIcon({
                    className: 'custom-alert-icon',
                    html: `<div class="pulse-marker" title="${props.code}: ${props.threat}"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker([coords[1], coords[0]], { icon: customIcon });

                // Status badge color
                let statusBadge = `<span class="px-2 py-0.5 rounded text-xs bg-amber-500/20 text-amber-400 border border-amber-500/30">${props.status}</span>`;
                if (props.status === 'Terverifikasi Valid') {
                    statusBadge = `<span class="px-2 py-0.5 rounded text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">${props.status}</span>`;
                } else if (props.status === 'Ditindaklanjuti') {
                    statusBadge = `<span class="px-2 py-0.5 rounded text-xs bg-blue-500/20 text-blue-400 border border-blue-500/30">${props.status}</span>`;
                }

                const popupHtml = `
                    <div class="w-64 p-3 font-sans">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-red-400 text-xs">${props.code}</span>
                            ${statusBadge}
                        </div>
                        <h4 class="font-semibold text-slate-100 text-sm mb-1">${props.threat}</h4>
                        <p class="text-xs text-slate-300 mb-2 leading-relaxed">${props.notes || 'Tidak ada catatan tambahan.'}</p>
                        <div class="bg-slate-800/80 rounded-lg p-2 text-xs space-y-1 mb-2 border border-slate-700/50">
                            <div class="flex justify-between"><span class="text-slate-400">Estimasi Kehilangan:</span> <span class="font-bold text-red-400">${props.loss_ha} Ha</span></div>
                            <div class="flex justify-between"><span class="text-slate-400">Deteksi Satelit:</span> <span class="text-slate-200">${props.date}</span></div>
                            <div class="flex justify-between"><span class="text-slate-400">Keyakinan:</span> <span class="text-slate-200">${props.confidence}</span></div>
                            <div class="flex justify-between"><span class="text-slate-400">Pelapor:</span> <span class="text-slate-200 truncate ml-2">${props.reporter}</span></div>
                        </div>
                        <div class="text-right">
                            <a href="${window.APP_BASE_URL}/alerts?search=${props.code}" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium inline-flex items-center gap-1">
                                Kelola Laporan &rarr;
                            </a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupHtml);
                currentAlertsLayer.addLayer(marker);
            });
        })
        .catch(err => console.error("Error loading GeoJSON alerts:", err));
}

/**
 * Setup AOI (Area of Interest) Polygon Analysis
 */
function setupAoiDrawing(map) {
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    let isDrawing = false;
    let currentPoints = [];
    let tempPolygon = null;

    const btnDrawAoi = document.getElementById('btnDrawAoi');
    const aoiResultModal = document.getElementById('aoiResultModal');
    const aoiCloseBtn = document.getElementById('aoiCloseBtn');

    if (aoiCloseBtn && aoiResultModal) {
        aoiCloseBtn.addEventListener('click', () => {
            aoiResultModal.classList.add('hidden');
        });
    }

    if (btnDrawAoi) {
        btnDrawAoi.addEventListener('click', () => {
            isDrawing = !isDrawing;
            currentPoints = [];
            if (tempPolygon) {
                map.removeLayer(tempPolygon);
                tempPolygon = null;
            }

            if (isDrawing) {
                btnDrawAoi.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
                btnDrawAoi.classList.add('bg-red-600', 'hover:bg-red-500');
                btnDrawAoi.innerHTML = `
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Batal Gambar Poligon
                `;
                map.getContainer().style.cursor = 'crosshair';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Klik pada peta untuk membuat sudut poligon. Dobel klik untuk menyelesaikan.',
                        showConfirmButton: false,
                        timer: 4500
                    });
                }
            } else {
                resetDrawingState();
            }
        });
    }

    function resetDrawingState() {
        isDrawing = false;
        if (btnDrawAoi) {
            btnDrawAoi.classList.remove('bg-red-600', 'hover:bg-red-500');
            btnDrawAoi.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
            btnDrawAoi.innerHTML = `
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Gambar Area Minat (AOI)
            `;
        }
        map.getContainer().style.cursor = '';
    }

    map.on('click', (e) => {
        if (!isDrawing) return;

        currentPoints.push([e.latlng.lat, e.latlng.lng]);

        if (tempPolygon) map.removeLayer(tempPolygon);

        if (currentPoints.length >= 2) {
            tempPolygon = L.polygon(currentPoints, {
                color: '#f59e0b',
                fillColor: '#fbbf24',
                fillOpacity: 0.35,
                weight: 2
            }).addTo(map);
        }
    });

    map.on('dblclick', (e) => {
        if (!isDrawing || currentPoints.length < 3) return;
        L.DomEvent.stopPropagation(e);

        const finalCoords = [...currentPoints];
        resetDrawingState();

        // Send to backend for spatial analysis
        analyzeCustomAoi(finalCoords);
    });
}

function analyzeCustomAoi(coordinates) {
    const aoiResultModal = document.getElementById('aoiResultModal');
    if (!aoiResultModal) return;

    fetch(`${window.APP_BASE_URL}/api/aoi-analyze`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ coordinates: coordinates })
    })
    .then(res => res.json())
    .then(response => {
        if (!response.success) {
            alert(response.message || 'Gagal menganalisis area poligon.');
            return;
        }

        const data = response.data;
        document.getElementById('aoiRegionName').innerText = data.region_name;
        document.getElementById('aoiAreaHa').innerText = Number(data.total_area_ha).toLocaleString('id-ID') + ' Ha';
        document.getElementById('aoiAreaKm2').innerText = Number(data.total_area_km2).toLocaleString('id-ID') + ' km²';
        document.getElementById('aoiForestPct').innerText = data.forest_percentage + '%';
        document.getElementById('aoiForestHa').innerText = Number(data.forest_area_ha).toLocaleString('id-ID') + ' Ha';
        document.getElementById('aoiNdviVal').innerText = data.avg_ndvi;
        document.getElementById('aoiNdviClass').innerText = data.ndvi_classification;
        document.getElementById('aoiCarbonTonnes').innerText = Number(data.estimated_carbon_tonnes).toLocaleString('id-ID') + ' Ton';

        const riskBadge = document.getElementById('aoiRiskBadge');
        if (riskBadge) {
            riskBadge.innerText = 'Risiko: ' + data.risk_level;
            riskBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold border ' + 
                (data.risk_level === 'Tinggi' ? 'bg-red-500/20 text-red-400 border-red-500/30' : 
                (data.risk_level === 'Sedang' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'));
        }

        aoiResultModal.classList.remove('hidden');
    })
    .catch(err => {
        console.error("AOI analysis error:", err);
    });
}

/**
 * Setup Swipe / Split Curtain Map Comparison
 */
function setupSwipeComparison(map, regionConfig) {
    const swipeSlider = document.getElementById('swipeSlider');
    const swipeToggle = document.getElementById('toggleSwipeMode');
    const swipeControlPanel = document.getElementById('swipeControlPanel');

    if (!swipeToggle || !swipeSlider) return;

    let isSwipeActive = false;
    let overlayTileLayer = null;

    swipeToggle.addEventListener('change', (e) => {
        isSwipeActive = e.target.checked;
        if (isSwipeActive) {
            swipeControlPanel.classList.remove('hidden');
            // Tambahkan layer CartoDB / Sentinel simulation sebagai pembanding
            if (!overlayTileLayer) {
                overlayTileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    opacity: 0.65
                }).addTo(map);
            }
        } else {
            swipeControlPanel.classList.add('hidden');
            if (overlayTileLayer) {
                map.removeLayer(overlayTileLayer);
                overlayTileLayer = null;
            }
        }
    });

    swipeSlider.addEventListener('input', (e) => {
        const val = e.target.value;
        if (overlayTileLayer) {
            overlayTileLayer.setOpacity(val / 100);
        }
    });
}
