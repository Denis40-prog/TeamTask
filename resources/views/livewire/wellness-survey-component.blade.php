<div class="max-w-3xl mx-auto p-4 sm:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Météo des émotions</h1>
        <p class="text-sm text-gray-500 mt-1">
            Remplis ce mini-sondage pour suivre ton état du jour. Notes de 1 (faible) à 10 (élevé).
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Date --}}
        <div>
            <label class="block text-sm font-medium mb-1" for="date">Date</label>
            <input id="date" type="date" wire:model.live="date"
                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Sommeil --}}
            <div class="p-4 rounded-xl border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium">Sommeil</label>
                    <span class="text-sm text-gray-500">Note: {{ $sleep }}</span>
                </div>
                <input type="range" min="1" max="10" step="1" wire:model.live="sleep" class="w-full" />
                @error('sleep') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Stress --}}
            <div class="p-4 rounded-xl border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium">Stress</label>
                    <span class="text-sm text-gray-500">Note: {{ $stress }}</span>
                </div>
                <input type="range" min="1" max="10" step="1" wire:model.live="stress" class="w-full" />
                @error('stress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Courbatures --}}
            <div class="p-4 rounded-xl border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium">Courbatures</label>
                    <span class="text-sm text-gray-500">Note: {{ $soreness }}</span>
                </div>
                <input type="range" min="1" max="10" step="1" wire:model.live="soreness" class="w-full" />
                @error('soreness') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Énergie --}}
            <div class="p-4 rounded-xl border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium">Énergie</label>
                    <span class="text-sm text-gray-500">Note: {{ $energy }}</span>
                </div>
                <input type="range" min="1" max="10" step="1" wire:model.live="energy" class="w-full" />
                @error('energy') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg wire:loading wire:target="save" class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                Enregistrer
            </button>

            @if($existingId)
                <span class="text-sm text-gray-500">Une saisie existe déjà pour cette date (mise à jour possible).</span>
            @endif
        </div>
    </form>
</div>
