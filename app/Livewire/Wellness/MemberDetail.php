<?php

namespace App\Livewire\Wellness;

use App\Models\Team;
use App\Models\User;
use App\Models\WellnessSurvey;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MemberDetail extends Component
{
    public Team $team;
    public User $member;

    #[Validate('required|date')]
    public $from;

    #[Validate('required|date|after_or_equal:from')]
    public $to;

    public array $data = [];

    public function mount(Team $team, User $user): void
    {
        $this->team = $team;
        $this->member = $user;

        $this->from = Carbon::now()->subDays(30)->toDateString();
        $this->to   = Carbon::now()->toDateString();

        $this->loadData();
    }

    public function updated($prop): void
    {
        if (in_array($prop, ['from','to'])) {
            $this->validate();
            $this->loadData();
        }
    }

    public function setPreset($days): void
    {
        $this->to = Carbon::now()->toDateString();
        $this->from = Carbon::now()->subDays($days)->toDateString();
        $this->loadData();
    }

    private function loadData(): void
    {
        $rows = WellnessSurvey::query()
            ->where('user_id', $this->member->id)
            ->whereBetween('date', [$this->from, $this->to])
            ->orderBy('date')
            ->get(['date','sleep','stress','soreness','energy']);

        $out = ['sleep'=>[],'stress'=>[],'soreness'=>[],'energy'=>[]];
        foreach ($rows as $r) {
            $d = Carbon::parse($r->date)->format('Y-m-d');
            $out['sleep'][]    = ['x'=>$d,'y'=>(int)$r->sleep];
            $out['stress'][]   = ['x'=>$d,'y'=>(int)$r->stress];
            $out['soreness'][] = ['x'=>$d,'y'=>(int)$r->soreness];
            $out['energy'][]   = ['x'=>$d,'y'=>(int)$r->energy];
        }
        $this->data = $out;

        $this->dispatch('series-updated', data: $this->data);
    }

    public function render()
    {
        return view('livewire.wellness.member-detail')
            ->title("Suivi — {$this->team->name} / {$this->member->name}");
    }
}
