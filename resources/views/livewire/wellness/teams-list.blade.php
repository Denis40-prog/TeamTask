<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white bg-slate-100 dark:bg-transparent">
        <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-6">Suivi météo — Mes équipes</h1>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($teams as $team)
                <a href="{{ route('wellness.followup.team', $team['id']) }}"
                   class="bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-600 rounded-lg p-6 hover:bg-slate-100 dark:hover:bg-gray-700 hover:border-slate-300 dark:hover:border-gray-500 transition-all duration-200 shadow-lg">
                    <div class="text-xl font-semibold text-slate-800 dark:text-white">{{ $team['name'] }}</div>
                    <div class="text-sm text-slate-600 dark:text-gray-400 mt-2">ID: {{ $team['id'] }}</div>
                    <div class="mt-4 text-slate-700 dark:text-white text-sm flex items-center space-x-2">
                        <span>Voir le tableau de bord</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-8 w-8 text-slate-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-700 dark:text-gray-300 mb-2">Aucune équipe</h3>
                    <p class="text-slate-600 dark:text-gray-500">Vous n'avez accès à aucune équipe pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
