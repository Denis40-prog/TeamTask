<div>
    <button wire:click="$toggle('showForm')" class="btn btn-primary">+ Créer une équipe</button>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center">
            <div class="dark:bg-neutral-900 text-black dark:text-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-4">Nouvelle équipe</h2>

                <form wire:submit.prevent="create">
                    <input
                        type="text"
                        wire:model="name"
                        class="rounded-xl w-full border border-neutral-300 dark:border-neutral-600 rounded p-2 mb-4 bg-grey dark:bg-neutral-800 text-black dark:text-white placeholder:text-neutral-500 dark:placeholder:text-neutral-400"
                        placeholder="Nom de l'équipe"
                    >

                    @error('name')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showForm', false)" class="btn">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
