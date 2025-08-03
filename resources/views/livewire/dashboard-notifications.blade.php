<div class="h-1/2 overflow-auto p-4 border rounded-xl border-neutral-200 dark:border-neutral-700">
    <h2 class="text-xl font-semibold mb-2">Notifications</h2>
    <ul class="space-y-2">
        @forelse ($notifications as $notification)
            <li class="p-3 rounded-lg">
                {{ $notification->title }}
            </li>
        @empty
            <li class="text-gray-500">Aucune notification</li>
        @endforelse
    </ul>
</div>
