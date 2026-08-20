@extends('layouts.app')

@section('content')
    <div class="relative h-screen w-screen">
        <livewire:map-interaction />
        <livewire:location-list />
        <livewire:add-location-modal />
        <livewire:weather-panel />

        <div
            id="onboarding-overlay"
            class="fixed inset-0 z-40 {{ $hasLocations ? 'hidden' : 'flex' }} items-center justify-center bg-black/50 p-4"
        >
            <div class="max-w-sm rounded-lg bg-slate-200 p-6 text-center shadow-xl dark:bg-gray-800">
                <h1 class="font-dangrek text-4xl text-gray-800 dark:text-gray-100">Weather-App</h1>
                <p class="mt-3 text-gray-700 dark:text-gray-200">
                    Click the map to save favourite locations and track weather.
                </p>
                <button
                    id="onboarding-dismiss"
                    type="button"
                    class="mt-4 cursor-pointer rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Get started
                </button>
            </div>
        </div>

        <div
            id="delete-toast"
            x-data="{ show: false, message: '' }"
            x-show="show"
            x-transition.opacity
            x-cloak
            class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-gray-900/90 px-4 py-2 text-sm text-white shadow-lg dark:bg-gray-100/90 dark:text-gray-900"
        >
            <span x-text="message"></span>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('locationDeleted', ({ name }) => {
                const toast = Alpine.$data(document.getElementById('delete-toast'));
                toast.message = `${name} deleted`;
                toast.show = true;
                setTimeout(() => (toast.show = false), 2000);
            });
        });
    </script>
@endsection
