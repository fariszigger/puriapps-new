<?php

namespace App\Livewire;

use App\Models\EvaluationNotification;
use Livewire\Component;

class NotificationMarquee extends Component
{
    public function render()
    {
        $notifications = EvaluationNotification::recent()
            ->latest()
            ->take(1)
            ->get();

        return view('livewire.notification-marquee', [
            'notifications' => $notifications,
        ]);
    }
}
