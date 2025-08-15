<?php

namespace App\Livewire;

use App\Models\WellnessSurvey;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WellnessSurveyComponent extends Component
{
    #[Validate('required|date')]
    public $date;

    #[Validate('required|integer|min:1|max:10')]
    public $sleep = 5;

    #[Validate('required|integer|min:1|max:10')]
    public $stress = 5;

    #[Validate('required|integer|min:1|max:10')]
    public $soreness = 5;

    #[Validate('required|integer|min:1|max:10')]
    public $energy = 5;

    public $existingId = null;

    public function mount(?string $date = null): void
    {
        // Par défaut : aujourd’hui
        $this->date = $date ?? now()->toDateString();

        // Si une saisie existe déjà pour ce jour, on pré-remplit (update UX)
        $existing = WellnessSurvey::query()
            ->where('user_id', Auth::id())
            ->whereDate('date', $this->date)
            ->first();

        if ($existing) {
            $this->existingId = $existing->id;
            $this->sleep     = (int) $existing->sleep;
            $this->stress    = (int) $existing->stress;
            $this->soreness  = (int) $existing->soreness;
            $this->energy    = (int) $existing->energy;
        }
    }

    public function updatedDate(): void
    {
        // Quand la date change, recharger la saisie correspondante si elle existe
        $existing = WellnessSurvey::query()
            ->where('user_id', Auth::id())
            ->whereDate('date', $this->date)
            ->first();

        if ($existing) {
            $this->existingId = $existing->id;
            $this->sleep     = (int) $existing->sleep;
            $this->stress    = (int) $existing->stress;
            $this->soreness  = (int) $existing->soreness;
            $this->energy    = (int) $existing->energy;
        } else {
            $this->existingId = null;
            $this->sleep = $this->stress = $this->soreness = $this->energy = 5;
        }
    }

    public function setGreatDay()
    {
        $this->sleep = 10;
        $this->stress = 1;
        $this->soreness = 1;
        $this->energy = 10;
    }

    public function setAverageDay()
    {
        $this->sleep = 4;
        $this->stress = 4;
        $this->soreness = 5;
        $this->energy = 7;
    }

    public function setDifficultDay()
    {
        $this->sleep = 2;
        $this->stress = 8;
        $this->soreness = 8;
        $this->energy = 2;
    }

    public function save()
    {
        $validated = $this->validate();

        // Upsert par (user_id, date)
        $survey = WellnessSurvey::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $validated['date']],
            [
                'sleep'    => $validated['sleep'],
                'stress'   => $validated['stress'],
                'soreness' => $validated['soreness'],
                'energy'   => $validated['energy'],
            ]
        );

        $this->existingId = $survey->id;

        // Event pour éventuellement rafraîchir un widget dashboard, si tu en as un
        $this->dispatch('wellnessSurveySaved');

        $this->dispatch('flash', type: 'success', text: 'Votre météo des émotions a été enregistrée !');
    }

    public function render()
    {
        return view('livewire.wellness-survey-component')
            ->title('Météo des émotions');
    }
}
