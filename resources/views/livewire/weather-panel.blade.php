<div x-data="{ open: @entangle('isOpen') }" x-cloak>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-20 flex w-full max-w-sm flex-col overflow-hidden bg-slate-200/70 shadow-2xl backdrop-blur-md sm:inset-y-4 sm:right-4 sm:rounded-xl dark:bg-gray-800/70"
    >
        <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
            <h2 class="min-w-0 flex-1 break-words font-semibold text-gray-800 dark:text-gray-100">{{ $locationName }}</h2>

            <button
                type="button"
                wire:click="close"
                class="cursor-pointer rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700"
                title="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-400/60 dark:[&::-webkit-scrollbar-thumb]:bg-gray-500/60">
            @if ($loading)
                <div class="animate-pulse space-y-4">
                    <div class="h-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-32 rounded bg-gray-200 dark:bg-gray-700"></div>
                </div>
            @elseif ($error)
                <div class="rounded border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
                    {{ $error }}
                </div>
            @else
                {{-- Current weather --}}
                @if ($weather)
                    <div class="flex items-center gap-4">
                        <img
                            src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png"
                            alt="{{ $weather['description'] }}"
                            class="h-16 w-16"
                        >
                        <div>
                            <p class="text-3xl font-semibold text-gray-800 dark:text-gray-100">
                                {{ round($weather['temperature']) }}&deg;C
                            </p>
                            <p class="text-sm capitalize text-gray-500 dark:text-gray-400">
                                {{ $weather['description'] }} &middot; feels like {{ round($weather['feelsLike']) }}&deg;C
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current conditions unavailable.</p>
                @endif

                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    @if ($weather)
                        <div class="rounded bg-gray-50 p-2 dark:bg-gray-700/50">
                            <dt class="text-gray-500 dark:text-gray-400">Humidity</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $weather['humidity'] }}%</dd>
                        </div>
                        <div class="rounded bg-gray-50 p-2 dark:bg-gray-700/50">
                            <dt class="text-gray-500 dark:text-gray-400">Wind</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ round($weather['windSpeed']) }} m/s</dd>
                        </div>
                        <div class="rounded bg-gray-50 p-2 dark:bg-gray-700/50">
                            <dt class="text-gray-500 dark:text-gray-400">Pressure</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $weather['pressure'] }} hPa</dd>
                        </div>
                    @endif
                    @if ($airQuality)
                        <div class="rounded bg-gray-50 p-2 dark:bg-gray-700/50">
                            <dt class="text-gray-500 dark:text-gray-400">Air quality</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-100">{{ $airQuality['label'] }}</dd>
                        </div>
                    @endif
                </dl>

                {{-- Hourly forecast --}}
                @if ($forecast && count($forecast['hourly']))
                    <h3 class="mt-6 mb-2 text-sm font-semibold text-gray-600 dark:text-gray-300">Hourly forecast</h3>
                    <div class="flex gap-3 overflow-x-auto pb-2 [scrollbar-width:thin] [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-400/60 dark:[&::-webkit-scrollbar-thumb]:bg-gray-500/60">
                        @foreach ($forecast['hourly'] as $hour)
                            <div class="flex shrink-0 flex-col items-center rounded bg-gray-200 px-3 py-2 dark:bg-gray-700/50">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Carbon::parse($hour['dateTime'])->format('H:i') }}
                                </span>
                                <img
                                    src="https://openweathermap.org/img/wn/{{ $hour['icon'] }}.png"
                                    alt="{{ $hour['description'] }}"
                                    class="h-12 w-12"
                                >
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                    {{ round($hour['temperature']) }}&deg;
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Daily forecast --}}
                @if ($forecast && count($forecast['daily']))
                    <h3 class="mt-6 mb-2 text-sm font-semibold text-gray-600 dark:text-gray-300">Daily forecast</h3>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($forecast['daily'] as $day)
                            <li class="flex items-center justify-between py-0 text-sm">
                                <span class="text-gray-600 dark:text-gray-300">
                                    {{ \Illuminate\Support\Carbon::parse($day['date'])->format('D j M') }}
                                </span>
                                <img
                                    src="https://openweathermap.org/img/wn/{{ $day['icon'] }}.png"
                                    alt="{{ $day['description'] }}"
                                    class="h-12 w-12"
                                >
                                <span class="text-gray-800 dark:text-gray-100">
                                    <span class="font-medium">{{ round($day['maxTemperature']) }}&deg;</span>
                                    <span class="text-gray-400 dark:text-gray-500">{{ round($day['minTemperature']) }}&deg;</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Air quality detail --}}
                @if ($airQuality)
                    <div x-data="{ expanded: false }" class="mt-6">
                        <button
                            type="button"
                            @click="expanded = !expanded"
                            class="flex w-full cursor-pointer items-center justify-between text-sm font-semibold text-gray-600 dark:text-gray-300"
                        >
                            <span>Air quality</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4 transition-transform"
                                :class="expanded && 'rotate-180'"
                            >
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div x-show="expanded" x-transition.opacity x-cloak class="mt-2 grid grid-cols-3 gap-2 text-xs">
                            @foreach ($airQuality['components'] as $pollutant => $value)
                                <div class="rounded bg-gray-50 p-2 text-center dark:bg-gray-700/50">
                                    <p class="uppercase text-gray-500 dark:text-gray-400">{{ $pollutant }}</p>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ round($value, 1) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
