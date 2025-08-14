<?php

namespace App\Livewire\Ui;

use Livewire\Component;

class FlashMessage extends Component
{
    /** @var string|null visible text */
    public ?string $text = null;

    /** @var string type: success|error|warning|info */
    public string $type = 'success';

    /** @var bool show/hide */
    public bool $show = false;

    /** @var array<string> session keys à écouter dans l'ordre */
    public array $keys = ['success', 'message', 'error', 'warning', 'info'];

    /** @var bool activer les confettis ? */
    public bool $confetti = true;

    /** @var array<string> types qui déclenchent les confettis */
    public array $confettiFor = ['success'];

    /** @var int auto-close en ms (0 = jamais) */
    public int $timeout = 4000;

    /**
     * Permet d'override via balise: <livewire:ui.flash-message :keys="['message','success']" type="warning" :confetti="false" :timeout="0" />
     */
    public function mount(
        ?array $keys = null,
        ?string $type = null,
        ?bool $confetti = null,
        ?array $confettiFor = null,
        ?int $timeout = null
    ): void {
        if ($keys !== null) $this->keys = $keys;
        if ($type !== null) $this->type = $type;
        if ($confetti !== null) $this->confetti = $confetti;
        if ($confettiFor !== null) $this->confettiFor = $confettiFor;
        if ($timeout !== null) $this->timeout = $timeout;

        $this->pullFromSession();
    }

    public function render()
    {
        return view('livewire.ui.flash-message');
    }

    /** Récupère le 1er message trouvé dans la session selon $keys */
    public function pullFromSession(): void
    {
        foreach ($this->keys as $key) {
            if (session()->has($key)) {
                $this->text = (string) session($key);
                // Mapping simple: 'message' => success par défaut
                if ($key === 'error')   $this->type = 'error';
                elseif ($key === 'warning') $this->type = 'warning';
                elseif ($key === 'info')    $this->type = 'info';
                else /* success|message */  $this->type = 'success';

                $this->show = true;
                return;
            }
        }
        $this->show = false;
    }

    /** Permet d’afficher depuis n’importe quel composant Livewire: $this->dispatch('flash', type:'success', text:'...') */
    #[\Livewire\Attributes\On('flash')]
    public function showFlash(string $type = 'success', string $text = '', ?int $timeout = null, ?bool $confetti = null): void
    {
        $this->type = $type;
        $this->text = $text;
        if ($timeout !== null)  $this->timeout = $timeout;
        if ($confetti !== null) $this->confetti = $confetti;
        $this->show = true;
    }

    public function dismiss(): void
    {
        $this->show = false;
    }

    public function shouldConfetti(): bool
    {
        return $this->confetti && in_array($this->type, $this->confettiFor, true);
    }
}
