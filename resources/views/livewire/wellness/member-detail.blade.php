<div>
    <div class="container mx-auto px-4 py-8 text-slate-800 dark:text-white dark:bg-transparent">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $member->name }}</h1>
                <p class="text-slate-600 dark:text-gray-400 mt-2">{{ $team->name }}</p>
            </div>
            <a href="{{ route('wellness.followup.team', $team->id) }}"
               class="bg-slate-200 dark:bg-zinc-900 hover:bg-slate-300 dark:hover:bg-gray-700 text-slate-800 dark:text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 shadow-lg flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Retour équipe</span>
            </a>
        </div>

        <div class="bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-gray-600 rounded-lg p-6 mb-6">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="date-from" class="text-sm font-medium text-slate-700 dark:text-gray-300 mb-2 block">Du</label>
                    <input type="date" id="date-from" class="px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500" wire:model.live="from">
                </div>
                <div>
                    <label for="date-to" class="text-sm font-medium text-slate-700 dark:text-gray-300 mb-2 block">Au</label>
                    <input type="date" id="date-to" class="px-3 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500 dark:focus:border-blue-500" wire:model.live="to">
                </div>
                <div class="ml-auto flex gap-2">
                    <button type="button" class="px-3 py-1 text-xs rounded-full bg-slate-200 dark:bg-gray-700 border border-slate-300 dark:border-gray-600 text-slate-800 dark:text-white hover:bg-slate-300 dark:hover:bg-gray-600 transition-colors duration-200" wire:click="setPreset(7)">7j</button>
                    <button type="button" class="px-3 py-1 text-xs rounded-full bg-slate-200 dark:bg-gray-700 border border-slate-300 dark:border-gray-600 text-slate-800 dark:text-white hover:bg-slate-300 dark:hover:bg-gray-600 transition-colors duration-200" wire:click="setPreset(30)">30j</button>
                    <button type="button" class="px-3 py-1 text-xs rounded-full bg-slate-200 dark:bg-gray-700 border border-slate-300 dark:border-gray-600 text-slate-800 dark:text-white hover:bg-slate-300 dark:hover:bg-gray-600 transition-colors duration-200" wire:click="setPreset(90)">90j</button>
                </div>
            </div>
            @error('from') <p class="text-xs text-red-500 dark:text-red-400 mt-2">{{ $message }}</p> @enderror
            @error('to') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

    <div class="grid md:grid-cols-2 gap-6">
        @foreach (['sleep'=>'Sommeil 😴','stress'=>'Stress 🧠','soreness'=>'Courbatures 💪','energy'=>'Énergie ⚡️'] as $metric => $title)
            <div class="bg-slate-100 dark:bg-zinc-900 border border-slate-200 dark:border-gray-600 rounded-lg p-6 overflow-x-auto">
                <div class="text-lg font-semibold text-slate-800 dark:text-white mb-4">{{ $title }}</div>
                <div class="w-full min-w-[400px]">
                    <canvas id="detail-{{ $metric }}" height="260" class="!w-full"></canvas>
                </div>
            </div>
        @endforeach
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
<script>
    let charts = {};
    const palette = { sleep:'#6366f1', stress:'#ef4444', soreness:'#f59e0b', energy:'#10b981' };

    function renderWith(data) {
        Object.entries(data).forEach(([metric, points]) => {
            const id = 'detail-' + metric;
            const el = document.getElementById(id);
            if (!el) return;

            const ctx = el.getContext('2d');
            const hasOnePoint = (points?.length || 0) === 1;
            const pointRadius = hasOnePoint ? 3 : 0;

            if (charts[metric]) {
                charts[metric].data.datasets[0].data = points || [];
                charts[metric].data.datasets[0].pointRadius = pointRadius;
                charts[metric].update();
            } else {
                charts[metric] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [{
                            data: points || [],
                            tension: 0.35,
                            borderWidth: 2,
                            borderColor: palette[metric],
                            pointRadius: pointRadius,
                            pointHoverRadius: Math.max(pointRadius, 3),
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            x: { type: 'time', time: { unit: 'day' }, grid: { display: false } },
                            y: { min: 0, max: 10, ticks: { stepSize: 1 } }
                        },
                        plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    }
                });
            }

            // Empty state
            const card = el.closest('.rounded-2xl');
            if (card) {
                let empty = card.querySelector('[data-empty]');
                if (!points || !points.length) {
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
    }

    // 1) Rendu initial avec les données du 1er render
    document.addEventListener('DOMContentLoaded', () => {
        renderWith(@json($data));
    });

    // 2) À chaque recalcul côté PHP, on reçoit l’événement Livewire
    window.addEventListener('series-updated', (e) => {
        const fresh = (e.detail && (e.detail.data ?? e.detail)) || {};
        renderWith(fresh);
    });

</script>

</div>
