<div
    x-data="{ open: @entangle('isOpen') }"
    x-init="$watch('open', (value) => { if (value) { $nextTick(() => $refs.nameInput.focus()) } })"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-30 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="$wire.close()"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            @click.outside="$wire.close()"
            class="w-full max-w-sm rounded-lg bg-slate-200 p-6 shadow-xl dark:bg-gray-800"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Save this location</h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ number_format((float) $lat, 4) }}, {{ number_format((float) $lng, 4) }}
            </p>

            <form wire:submit="save" class="mt-4">
                <label for="location-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Name
                </label>

                <input
                    id="location-name"
                    x-ref="nameInput"
                    type="text"
                    wire:model="name"
                    class="mt-1 w-full rounded border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g. Home, Office, Mum's house"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex justify-end gap-2">
                    <button
                        type="button"
                        wire:click="close"
                        class="cursor-pointer rounded px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="cursor-pointer rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
