@php
    $props = $getProps();
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    function initMap(mapElement, state, wire, loadingElement) {
        // Show the loading indicator
        loadingElement.style.display = 'flex';

        // Use Geolocation API to get the user's current position
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {

                // Set the map to the user's current location
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Update the state with the user's current location
                state.lat = lat;
                state.lng = lng;

                // Initialize the map with the user's current location
                const map = L.map(mapElement).setView([lat, lng], 16);

                // Set up the map's tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 28,
                }).addTo(map);

                // Add a draggable marker to the map
                const marker = L.marker([lat, lng], {
                        draggable: @js($props['draggable'])
                    }).addTo(map)
                    .bindPopup("Lokasi Anda!")
                    .setIcon(L.icon({
                        iconUrl: `https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png`,
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [0, -33],
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        shadowSize: [41, 41],
                        shadowAnchor: [12, 41]
                    }));

                addMarkers(map, marker, state, wire);

                // Update state when the marker is dragged
                marker.on('dragend', function() {
                    const latLng = marker.getLatLng();

                    state.lat = latLng.lat;
                    state.lng = latLng.lng;

                    state.in_marker_radius = false;

                    wire.$refresh();
                });

                wire.$refresh();

                closeLoading(loadingElement);

            }, function(error) {
                // Hide the loading indicator and initialize the map with the default location

                alert("Terjadi Kesalahan : " + error.message);

                console.error("Geolocation error:", error);

                initDefaultMap(mapElement, state, wire);

                closeLoading(loadingElement);
            });
        } else {
            // Hide the loading indicator and fallback to the default location
            initDefaultMap(mapElement, state, wire);

            closeLoading(loadingElement);
        }
    }

    function initDefaultMap(mapElement, state, wire) {
        // Initialize the map with the default location from the state
        const map = L.map(mapElement).setView([state.lat, state.lng], 13);

        // Set up the map's tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add a draggable marker to the map
        const marker = L.marker([state.lat, state.lng], {
                draggable: @js($props['draggable'])
            }).addTo(map)
            .bindPopup("Lokasi Anda!")
            .setIcon(L.icon({
                iconUrl: `https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png`,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [0, -33],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                shadowSize: [41, 41],
                shadowAnchor: [12, 41]
            }));


        // Update state when the marker is dragged
        marker.on('dragend', function() {
            const latLng = marker.getLatLng();

            state.lat = latLng.lat;
            state.lng = latLng.lng;

            state.in_marker_radius = false;

            wire.$refresh();
        });

    }

    function closeLoading(loadingElement) {

        // Hide the loading indicator once location is obtained
        setTimeout(() => {
            loadingElement.style.display = 'none';
        }, 500);
    }

    function addMarkers(map, user_mark, state, wire) {
        // Get predefined markers from the props
        const markers = @js($props['markers']);

        // Iterate over all predefined markers
        for (let i = 0; i < markers.length; i++) {
            const marker = markers[i];

            // Add a circle and marker for each predefined marker
            L.circle([marker.lat, marker.lng], {
                radius: marker.radius,
                color: marker.color,
            }).addTo(map);
            L.marker([marker.lat, marker.lng]).addTo(map).bindPopup(marker.label).setIcon(L.icon({
                iconUrl: `https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${marker.color}.png`,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [0, -33],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                shadowSize: [41, 41],
                shadowAnchor: [12, 41]
            }));

            const latLng = user_mark.getLatLng();

            // Calculate the distance between the user's marker and the current predefined marker
            const distance = map.distance(latLng, [marker.lat, marker.lng]);

            // Check if the distance is within the marker's radius
            if (distance <= marker.radius) {

                state.in_marker_radius = true;
                state.location_id = marker.id;

                wire.$refresh();
            }
        }
    }


    // function addMarkers(map, user_mark) {

    //     const markers = @js($props['markers']);
    //     for (let i = 0; i < markers.length; i++) {
    //         const marker = markers[i];
    //         L.circle([marker.lat, marker.lng], {radius: marker.radius, color: 'green'}).addTo(map);
    //         L.marker([marker.lat, marker.lng]).addTo(map).bindPopup(marker.label).setIcon(L.icon({
    //             iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    //             iconSize: [25, 41],
    //             iconAnchor: [12, 41],
    //             popupAnchor: [0, -33],
    //             shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    //             shadowSize: [41, 41],
    //             shadowAnchor: [12, 41]
    //         }));
    //     }
    // }
</script>

<style>
    /* Container for the map */
    .map-container {
        position: relative;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background: linear-gradient(to right, #3498db, #2ecc71);
        /* Blue to Green Gradient */
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #272727bf;
        z-index: 100000;
    }

    /* Spinner */
    .spinner {
        width: 24px;
        height: 24px;
        border: 4px solid #3498db;
        /* Blue color */
        border-top: 4px solid transparent;
        border-radius: 50%;
        margin-right: 10px;
        animation: spin 1s linear infinite;
    }

    /* Spinner Animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Map Content Placeholder */
    .map-content {
        width: 100%;
        height: 100%;
    }
</style>

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }">
        <!-- Loading Indicator -->
        <div class="loading-overlay" x-ref="loading" wire:ignore>
            <div class="spinner"></div>
            <span>Memuat Lokasi Saat Ini...</span>
        </div>

        <!-- Map Container -->
        <div class="relative w-full h-64 bg-gradient-to-r from-blue-500 to-green-500 rounded-lg shadow-md"
            x-init="initMap($refs.map, state, $wire, $refs.loading)">
            <div wire:ignore x-ref="map" class="w-full h-full rounded-lg" style="height: {{ $props['height'] }};">
            </div>
        </div>
    </div>
</x-dynamic-component>
