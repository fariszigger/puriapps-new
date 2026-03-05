<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserStatusOverlay extends Component
{
    public function render()
    {
        return view('livewire.user-status-overlay', [
            'users' => User::where('is_online', true)->orderBy('name', 'asc')->get(),
        ]);
    }
}
