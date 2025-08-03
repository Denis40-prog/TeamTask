<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DashboardNotifications extends Component
{
    public $notifications = [];

    protected $listeners = ['notificationCreated' => 'refresh'];

    public function mount()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-notifications');
    }
}
