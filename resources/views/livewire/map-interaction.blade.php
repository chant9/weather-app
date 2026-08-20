<div class="absolute inset-0 z-0 p-2">
    <div
        id="map"
        wire:ignore
        class="h-full w-full [&_.leaflet-tile-pane]:transition-[filter] [&_.leaflet-tile-pane]:duration-300"
    ></div>

    <div class="pointer-events-none absolute right-4 top-4 z-[1000] flex gap-2">
        <button
            id="use-my-location"
            type="button"
            class="pointer-events-auto cursor-pointer rounded-full bg-slate-100/90 p-3 text-gray-700 shadow-lg backdrop-blur hover:bg-slate-100 dark:bg-gray-800/90 dark:text-gray-200 dark:hover:bg-gray-800"
            title="Use my location"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                <circle cx="12" cy="12" r="3" />
                <path d="M12 2v3m0 14v3m10-10h-3M5 12H2" />
            </svg>
        </button>

        <button
            id="theme-toggle"
            type="button"
            class="pointer-events-auto cursor-pointer rounded-full bg-slate-100/90 p-3 text-gray-700 shadow-lg backdrop-blur hover:bg-slate-100 dark:bg-gray-800/90 dark:text-gray-200 dark:hover:bg-gray-800"
            title="Toggle theme"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 dark:hidden">
                <path d="M12 3a6 6 0 1 0 9 9 9 9 0 1 1-9-9Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden h-5 w-5 dark:block">
                <circle cx="12" cy="12" r="4" />
                <path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.07-7.07-1.41 1.41M6.34 17.66l-1.41 1.41m12.73 0-1.41-1.41M6.34 6.34 4.93 4.93" />
            </svg>
        </button>
    </div>

    @script
    <script>
        const seedLocations = @json($locations);
        const markers = {};

        const map = L.map('map', { zoomControl: false }).setView([54.5, 15.2], 4);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        map.attributionControl.setPrefix(false);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        function addMarker(location) {
            markers[location.id] = L.marker([location.lat, location.lng])
                .addTo(map)
                .on('click', () => {
                    Livewire.dispatch('locationChosen', {
                        id: location.id,
                        name: location.name,
                        lat: location.lat,
                        lng: location.lng,
                    });
                });
        }

        function removeMarker(id) {
            if (markers[id]) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }
        }

        seedLocations.forEach(addMarker);

        function applyMapTheme(isDark) {
            const pane = document.querySelector('#map .leaflet-tile-pane');
            pane?.classList.toggle('invert', isDark);
            pane?.classList.toggle('hue-rotate-180', isDark);
            pane?.classList.toggle('brightness-95', isDark);
            pane?.classList.toggle('contrast-90', isDark);
        }

        applyMapTheme(document.documentElement.classList.contains('dark'));

        window.addEventListener('theme-changed', (event) => applyMapTheme(event.detail.dark));

        map.on('click', (event) => {
            $wire.selectLocation(event.latlng.lat, event.latlng.lng);
        });

        document.getElementById('use-my-location').addEventListener('click', () => {
            if (!navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                const { latitude, longitude } = position.coords;
                map.flyTo([latitude, longitude], 11);
                $wire.selectLocation(latitude, longitude);
            });
        });

        document.getElementById('theme-toggle').addEventListener('click', () => {
            const next = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark', next);
            localStorage.setItem('theme', next ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: next } }));
        });

        Livewire.on('locationSaved', (location) => addMarker(location));
        Livewire.on('locationDeleted', ({ id }) => removeMarker(id));
        Livewire.on('centerMapOnLocation', ({ lat, lng }) => map.flyTo([lat, lng], 11));
    </script>
    @endscript
</div>
