<div
    x-data="{ collapsed: {{ $locations->isEmpty() ? 'true' : 'false' }}, confirmingId: null, confirmingName: '' }"
    x-on:onboarding-dismissed.window="collapsed = false"
    class="absolute left-0 top-0 z-10 flex h-full"
>
    <div
        x-show="!collapsed"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="flex w-72 max-w-[80vw] flex-col overflow-hidden bg-slate-200/70 shadow-lg backdrop-blur-md dark:bg-gray-800/70"
    >
        <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Saved locations</h2>
            <button
                type="button"
                @click="collapsed = true"
                class="cursor-pointer rounded p-1 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                title="Collapse sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>
        </div>

        <ul class="flex-1 overflow-y-auto [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-400/60 dark:[&::-webkit-scrollbar-thumb]:bg-gray-500/60">
            @forelse ($locations as $location)
                <li
                    wire:key="location-{{ $location->id }}"
                    class="group flex items-center border-b border-gray-100 dark:border-gray-700"
                >
                    <button
                        type="button"
                        wire:click="choose({{ $location->id }})"
                        title="{{ $location->name }}"
                        class="flex-1 cursor-pointer truncate px-4 py-3 text-left text-sm text-gray-700 transition-colors hover:bg-slate-300/60 hover:text-blue-600 dark:text-gray-200 dark:hover:bg-gray-700/60 dark:hover:text-blue-400"
                    >
                        {{ $location->name }}
                    </button>

                    <button
                        type="button"
                        @click="confirmingId = {{ $location->id }}; confirmingName = @js($location->name)"
                        class="mr-2 cursor-pointer rounded p-1 text-gray-400 opacity-0 hover:bg-red-50 hover:text-red-600 group-hover:opacity-100 dark:hover:bg-red-950"
                        title="Delete location"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6h16Z" />
                        </svg>
                    </button>
                </li>
            @empty
                <li class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                    Click the map to save your first location.
                </li>
            @endforelse
        </ul>
    </div>

    <button
        type="button"
        x-show="collapsed"
        @click="collapsed = false"
        class="mt-4 h-10 w-8 cursor-pointer self-start rounded-r-lg bg-slate-200/95 text-gray-600 shadow-lg backdrop-blur hover:bg-slate-200 dark:bg-gray-800/95 dark:text-gray-300"
        title="Expand sidebar"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto h-5 w-5">
            <path d="m9 18 6-6-6-6" />
        </svg>
    </button>

    <div
        x-show="confirmingId !== null"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-30 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="confirmingId = null"
    >
        <div
            x-show="confirmingId !== null"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            @click.outside="confirmingId = null"
            class="w-full max-w-sm rounded-lg bg-slate-200 p-6 shadow-xl dark:bg-gray-800"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Delete location</h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to delete <span x-text="confirmingName" class="font-medium text-gray-700 dark:text-gray-300"></span>? This cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-2">
                <button
                    type="button"
                    @click="confirmingId = null"
                    class="cursor-pointer rounded px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="$wire.delete(confirmingId); confirmingId = null"
                    class="cursor-pointer rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
