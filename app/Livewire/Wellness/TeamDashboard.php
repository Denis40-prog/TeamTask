<?php

namespace App\Livewire\Wellness;

use App\Models\Team;
use App\Models\User;
use App\Models\WellnessSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeamDashboard extends Component
{
    public Team $team;
    public array $members = []; // users de l'équipe
    public array $series = [];  // données par user_id -> metrics -> [ {x,y} ... ]

    public function mount(Team $team): void
    {
        $this->team = $team;

        // membres visibles = membres de l'équipe
        $this->members = $team->users()
        ->select(
            'users.id as id',
            'users.name',
            'users.email'
        )
        ->orderBy('users.name')
        ->get()
        ->toArray();

        // plage par défaut : 30 derniers jours
        $from = Carbon::now()->subDays(30)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $userIds = array_column($this->members, 'id');

        $surveys = WellnessSurvey::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('date')
            ->get(['user_id','date','sleep','stress','soreness','energy']);

        // Structure les séries : user_id => metric => [[x,y],...]
        $byUser = [];
        foreach ($surveys as $s) {
            $date = Carbon::parse($s->date)->format('Y-m-d');
            $byUser[$s->user_id]['sleep'][]    = ['x' => $date, 'y' => (int)$s->sleep];
            $byUser[$s->user_id]['stress'][]   = ['x' => $date, 'y' => (int)$s->stress];
            $byUser[$s->user_id]['soreness'][] = ['x' => $date, 'y' => (int)$s->soreness];
            $byUser[$s->user_id]['energy'][]   = ['x' => $date, 'y' => (int)$s->energy];
        }
        $this->series = $byUser;
    }

    public function render()
    {
        return view('livewire.wellness.team-dashboard')->title("Suivi météo — {$this->team->name}");
    }
}
