<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white bg-slate-100 dark:bg-transparent">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Suivi — {{ $team->name }}</h1>
                <p class="text-slate-600 dark:text-gray-400 mt-2">Vue des 30 derniers jours. Clique un graphique pour voir le détail d'un membre.</p>
            </div>
            <a href="{{ route('wellness.followup') }}"
               class="bg-slate-200 dark:bg-gray-800 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 shadow-lg flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Mes équipes</span>
            </a>
        </div>

        <div class="space-y-6">
            @foreach ($members as $m)
                @php $uid = $m['id']; @endphp
                <div class="bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-600 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-xl font-semibold text-slate-800 dark:text-white">{{ $m['name'] }}</div>
                            <div class="text-sm text-slate-600 dark:text-gray-400">{{ $m['email'] }}</div>
                        </div>
                        <a href="{{ route('wellness.followup.team.member', [$team->id, $uid]) }}"
                           class="bg-emerald-600 dark:bg-blue-600 hover:bg-emerald-700 dark:hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <span>Détails</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach (['sleep' => 'Sommeil 😴','stress'=>'Stress 🧠','soreness'=>'Courbatures 💪','energy'=>'Énergie ⚡️'] as $metric => $title)
                            <div class="bg-slate-100 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-lg p-4 hover:bg-slate-200 dark:hover:bg-gray-600 hover:border-slate-300 dark:hover:border-gray-500 transition-all duration-200 cursor-pointer"
                                onclick="window.location='{{ route('wellness.followup.team.member', [$team->id, $uid]) }}'">
                                <div class="text-sm font-semibold text-slate-800 dark:text-white mb-3">{{ $title }}</div>

                                {{-- Conteneur à hauteur fixe --}}
                                <div class="h-40 md:h-36 lg:h-32">  {{-- ~160 / 144 / 128 px selon breakpoints --}}
                                    <canvas id="chart-{{ $uid }}-{{ $metric }}" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    {{-- Chart.js + date adapter --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>

<script>
    const palette = {
        sleep:  '#6366f1',
        stress: '#ef4444',
        soreness:'#f59e0b',
        energy: '#10b981',
    };
    const series = @json($series); // { userId: {metric: [{x:'2025-08-01', y:5}, ...]}}

    // Petit cache pour éviter de recréer 1000 fois les charts
    const charts = {};

    function makeChart(canvasId, points, color) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');

        if (charts[canvasId]) {
            charts[canvasId].data.datasets[0].data = points || [];
            charts[canvasId].update();
            return;
        }

        const radius = (points?.length || 0) <= 1 ? 3 : 0;

        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    data: points || [],
                    tension: 0.3,
                    pointRadius: radius,
                    borderWidth: 2,
                    borderColor: color,
                    fill: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { type: 'time', time: { unit: 'day' }, grid: { display: false } },
                    y: { min: 0, max: 10, ticks: { stepSize: 2 } }
                },
                plugins: { legend: { display: false }, tooltip: { enabled: true } },
            }
        });
    }

    function renderAll() {
        Object.entries(series).forEach(([userId, metrics]) => {
            ['sleep','stress','soreness','energy'].forEach(metric => {
                const points = (metrics && metrics[metric]) ? metrics[metric] : [];
                makeChart(`chart-${userId}-${metric}`, points, palette[metric]);
                // Affiche un mini état vide si pas de points
                const card = document.getElementById(`chart-${userId}-${metric}`)?.closest('.rounded-xl');
                if (card) {
                    let empty = card.querySelector('[data-empty]');
                    if (!points.length) {
                        if (!empty) {
                            empty = document.createElement('div');
                            empty.setAttribute('data-empty', '1');
                            empty.className = 'text-xs text-gray-400 mt-2';
                            empty.textContent = 'Aucune donnée sur la période';
                            card.appendChild(empty);
                        }
                    } else if (empty) empty.remove();
                }
            });
        });
    }

    // 1) Premier rendu au chargement de la page
    document.addEventListener('DOMContentLoaded', renderAll);
    // 2) Si tu utilises Livewire navigate
    document.addEventListener('livewire:navigated', renderAll);
    // 3) Hook Livewire v3 quand le DOM est réconcilié
    document.addEventListener('livewire:update', renderAll);
</script>

</div>
