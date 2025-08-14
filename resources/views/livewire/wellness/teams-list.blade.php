<div class="max-w-5xl mx-auto p-4 sm:p-6">
    <h1 class="text-2xl font-semibold mb-4">Suivi météo — Mes équipes</h1>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($teams as $team)
            <a href="{{ route('wellness.followup.team', $team['id']) }}"
               class="rounded-xl border p-4 hover:shadow-md transition bg-white dark:bg-gray-900">
                <div class="text-lg font-medium">{{ $team['name'] }}</div>
                <div class="text-xs text-gray-500 mt-1">ID: {{ $team['id'] }}</div>
                <div class="mt-3 text-indigo-600 text-sm">Voir le tableau de bord →</div>
            </a>
        @empty
            <div class="text-gray-500">Aucune équipe trouvée.</div>
        @endforelse
    </div>
</div>
