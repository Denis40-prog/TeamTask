<div class="max-w-3xl mx-auto p-4 sm:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Météo des émotions</h1>
        <p class="text-sm text-gray-500 mt-1">
            Remplis ce mini-sondage pour suivre ton état du jour. Notes de 1 (faible) à 10 (élevé).
        </p>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Date --}}
        <div>
            <label class="block text-sm font-medium mb-1" for="date">Date</label>
            <div class="flex items-center gap-3">
                <input id="date" type="date" wire:model.live="date"
                    class="w-full rounded-lg border-slate-300 dark:border-gray-600 text-slate-800 dark:text-white focus:border-emerald-500 dark:focus:border-blue-500 focus:ring-emerald-500 dark:focus:ring-blue-500" />
                <button type="button"
                        class="shrink-0 rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                        wire:click="$set('date','{{ now()->toDateString() }}')">
                    Aujourd’hui
                </button>
            </div>
            @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @php
            // Palettes couleur selon la "bonne/mauvaise" note
            $badge = function($v) {
                return match(true) {
                    $v <= 3 => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                    $v <= 6 => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
                    default => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                };
            };

            // Émojis par score 1..10
            $sleepEmojis   = ['😵','😫','😴','🥱','😐','🙂','😊','😌','😁','😇'];
            $stressEmojis  = ['😌','🙂','😐','😕','😣','😖','😫','🥵','😱','😵‍💫'];
            $soreEmojis    = ['🧘','🙂','😐','😕','😣','😖','😫','🤕','🧟','💥'];
            $energyEmojis  = ['😴','🥱','😐','🙂','😊','😀','😄','😃','😁','⚡️'];

            $labels = [1=>'1','2','3','4','5','6','7','8','9','10'];
        @endphp

        {{-- Sommeil --}}
        <div class="p-4 rounded-2xl border bg-slate-100 dark:bg-zinc-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">😴</span>
                    <span class="text-sm font-medium">Sommeil</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $badge($sleep) }}">
                    Note : {{ $sleep }}
                </span>
            </div>
            <div class="grid grid-cols-10 gap-2">
                @foreach($labels as $i => $label)
                    <button type="button"
                            wire:click="$set('sleep', {{ $i }})"
                            class="group flex flex-col items-center justify-center rounded-xl border py-3 transition
                                   hover:scale-105 hover:shadow-md
                                   {{ $sleep == $i ? 'border-emerald-400 ring-2 ring-emerald-200 bg-emerald-50 dark:border-emerald-500 dark:ring-emerald-800 dark:bg-emerald-900' : 'border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700' }}">
                        <span class="text-2xl transition group-hover:scale-110 {{ $sleep == $i ? 'animate-pulse' : '' }}">
                            {{ $sleepEmojis[$i-1] }}
                        </span>
                        <span class="mt-1 text-[10px] text-slate-500 dark:text-gray-400">{{ $i }}</span>
                    </button>
                @endforeach
            </div>
            @error('sleep') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Stress --}}
        <div class="p-4 rounded-2xl border bg-slate-100 dark:bg-zinc-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🧠</span>
                    <span class="text-sm font-medium">Stress</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $badge(11 - $stress) }}">
                    Note : {{ $stress }}
                </span>
            </div>
            <div class="grid grid-cols-10 gap-2">
                @foreach($labels as $i => $label)
                    <button type="button"
                            wire:click="$set('stress', {{ $i }})"
                            class="group flex flex-col items-center justify-center rounded-xl border py-3 transition
                                   hover:scale-105 hover:shadow-md
                                   {{ $stress == $i ? 'border-emerald-400 ring-2 ring-emerald-200 bg-emerald-50 dark:border-emerald-500 dark:ring-emerald-800 dark:bg-emerald-900' : 'border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700' }}">
                        <span class="text-2xl transition group-hover:scale-110 {{ $stress == $i ? 'animate-pulse' : '' }}">
                            {{ $stressEmojis[$i-1] }}
                        </span>
                        <span class="mt-1 text-[10px] text-slate-500 dark:text-gray-400">{{ $i }}</span>
                    </button>
                @endforeach
            </div>
            @error('stress') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Courbatures --}}
        <div class="p-4 rounded-2xl border bg-slate-100 dark:bg-zinc-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">💪</span>
                    <span class="text-sm font-medium">Courbatures</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $badge(11 - $soreness) }}">
                    Note : {{ $soreness }}
                </span>
            </div>
            <div class="grid grid-cols-10 gap-2">
                @foreach($labels as $i => $label)
                    <button type="button"
                            wire:click="$set('soreness', {{ $i }})"
                            class="group flex flex-col items-center justify-center rounded-xl border py-3 transition
                                   hover:scale-105 hover:shadow-md
                                   {{ $soreness == $i ? 'border-emerald-400 ring-2 ring-emerald-200 bg-emerald-50 dark:border-emerald-500 dark:ring-emerald-800 dark:bg-emerald-900' : 'border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700' }}">
                        <span class="text-2xl transition group-hover:scale-110 {{ $soreness == $i ? 'animate-pulse' : '' }}">
                            {{ $soreEmojis[$i-1] }}
                        </span>
                        <span class="mt-1 text-[10px] text-slate-500 dark:text-gray-400">{{ $i }}</span>
                    </button>
                @endforeach
            </div>
            @error('soreness') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Énergie --}}
        <div class="p-4 rounded-2xl border bg-slate-100 dark:bg-zinc-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚡️</span>
                    <span class="text-sm font-medium">Énergie</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $badge($energy) }}">
                    Note : {{ $energy }}
                </span>
            </div>
            <div class="grid grid-cols-10 gap-2">
                @foreach($labels as $i => $label)
                    <button type="button"
                            wire:click="$set('energy', {{ $i }})"
                            class="group flex flex-col items-center justify-center rounded-xl border py-3 transition
                                   hover:scale-105 hover:shadow-md
                                   {{ $energy == $i ? 'border-emerald-400 ring-2 ring-emerald-200 bg-emerald-50 dark:border-emerald-500 dark:ring-emerald-800 dark:bg-emerald-900' : 'border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700' }}">
                        <span class="text-2xl transition group-hover:scale-110 {{ $energy == $i ? 'animate-pulse' : '' }}">
                            {{ $energyEmojis[$i-1] }}
                        </span>
                        <span class="mt-1 text-[10px] text-slate-500 dark:text-gray-400">{{ $i }}</span>
                    </button>
                @endforeach
            </div>
            @error('energy') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Presets --}}
        <div class="flex flex-wrap gap-2">
            <button type="button" class="rounded-full px-3 py-1 text-xs border bg-slate-100 hover:bg-gray-50 dark:hover:bg-gray-700"
                    wire:click="setGreatDay">
                💚 Journée au top
            </button>
            <button type="button" class="rounded-full px-3 py-1 text-xs border bg-slate-100 hover:bg-gray-50 dark:hover:bg-gray-700"
                    wire:click="setAverageDay">
                🧩 Moyen + motivation
            </button>
            <button type="button" class="rounded-full px-3 py-1 text-xs border bg-slate-100 hover:bg-gray-50 dark:hover:bg-gray-700"
                    wire:click="setDifficultDay">
                🫶 Journée difficile
            </button>
        </div>

        <livewire:ui.flash-message />

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white
                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
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
