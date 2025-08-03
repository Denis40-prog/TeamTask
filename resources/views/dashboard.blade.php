<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col h-full w-full gap-4">

        {{-- Zone notifications --}}
        <livewire:dashboard-notifications />

        {{-- Zone équipes --}}
        <livewire:dashboard-teams />

    </div>
</x-layouts.app>
