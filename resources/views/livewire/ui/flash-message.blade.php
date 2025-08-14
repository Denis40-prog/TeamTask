<div
    x-data="{
        show: @entangle('show').live,    // <-- entanglement Livewire <-> Alpine
        timeout: @js($timeout)
    }"
    x-init="$watch('show', (val) => { if (val && timeout) setTimeout(() => show = false, timeout) })"
    x-show="show"
    x-transition.opacity
    role="alert"
    class="relative mb-4 overflow-hidden"
>
    @php
        $styles = [
            'success' => 'border-green-200 bg-green-50 text-green-700',
            'error'   => 'border-red-200 bg-red-50 text-red-700',
            'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
            'info'    => 'border-blue-200 bg-blue-50 text-blue-700',
        ];
        $icons = [
            'success' => '🎉',
            'error'   => '❌',
            'warning' => '⚠️',
            'info'    => 'ℹ️',
        ];
    @endphp

    <div class="rounded-lg border px-4 py-3 pr-10 {{ $styles[$type] ?? $styles['success'] }}">
        <span class="mr-2">{{ $icons[$type] ?? 'ℹ️' }}</span>
        {{ $text }}
        <button
            type="button"
            @click="show = false; $wire.dismiss()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-current text-lg leading-none opacity-70 hover:opacity-100"
            aria-label="Fermer"
        >&times;</button>
    </div>

    {{-- Confettis optionnels --}}
    @if($this->shouldConfetti() && !empty($text))
        <div class="pointer-events-none absolute inset-0">
            @for($i=0; $i<30; $i++)
                <span class="absolute text-xl animate-confetti"
                      style="left: {{ rand(0,100) }}%; top: -10px; animation-delay: {{ rand(0,10)/10 }}s;">
                    🎉
                </span>
            @endfor
        </div>
    @endif

    {{-- CSS confetti une seule fois dans tout le cycle --}}
    @once
        <style>
            @keyframes confetti-fall {
                0%   { transform: translateY(-10px) rotate(0deg);   opacity: 1; }
                100% { transform: translateY(60px) rotate(360deg); opacity: 0; }
            }
            .animate-confetti { animation: confetti-fall 1.2s ease-out forwards; }
        </style>
    @endonce
</div>
