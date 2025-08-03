        {{-- Zone équipes --}}
        <div class="h-1/2 relative p-4 border rounded-xl border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Mes équipes</h2>
                <livewire:create-team-form />
            </div>

            {{-- Hexagones en quinconce --}}
            <div class="relative w-full h-full">
                <x-hexagon-grid :teams="$teams" />
            </div>
        </div>
