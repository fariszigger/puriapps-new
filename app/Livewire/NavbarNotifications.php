<?php

namespace App\Livewire;

use App\Models\CustomerVisit;
use App\Models\Evaluation;
use App\Models\WarningLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NavbarNotifications extends Component
{
    public $totalCount = 0;
    
    // Kabag specific
    public $pendingEvaluationsCount = 0;
    public $kabagJanjiBayarCount = 0;
    public $kabagFollowUpSpCount = 0;
    
    // AO specific
    public $aoJanjiBayarCount = 0;
    public $aoFollowUpSpCount = 0;
    public $aoEvaluationStatusCount = 0;
    
    public function render()
    {
        $this->calculateNotifications();
        
        return view('livewire.navbar-notifications');
    }

    public function calculateNotifications()
    {
        $user = Auth::user();
        if (!$user) return;
        
        $today = now()->format('Y-m-d');
        $this->totalCount = 0;

        if ($user->hasRole('kabag')) {
            // 1. Pending Evaluations
            $this->pendingEvaluationsCount = Evaluation::where('status', 'pending')
                                                    ->whereNull('deleted_at')
                                                    ->count();
            
            // 2. Janji Bayar (today or overdue, not fulfilled)
            $this->kabagJanjiBayarCount = CustomerVisit::where('hasil_penagihan', 'janji_bayar')
                                                    ->where('tanggal_janji_bayar', '<=', $today)
                                                    ->where('janji_bayar_fulfilled', false)
                                                    ->whereNull('deleted_at')
                                                    ->count();
            
            // 3. Follow Up SP (deadline is today or overdue)
            // Assuming we check 'deadline_date' and maybe a status if we track fulfillment for WarningLetter
            $this->kabagFollowUpSpCount = WarningLetter::where('deadline_date', '<=', $today)
                                                    ->whereNull('deleted_at')
                                                    ->count();

            $this->totalCount = $this->pendingEvaluationsCount + 
                                $this->kabagJanjiBayarCount + 
                                $this->kabagFollowUpSpCount;
        }

        if ($user->hasRole('ao')) {
            $userId = $user->id;
            
            // 1. Janji Bayar for this AO
            $this->aoJanjiBayarCount = CustomerVisit::where('user_id', $userId)
                                                    ->where('hasil_penagihan', 'janji_bayar')
                                                    ->where('tanggal_janji_bayar', '<=', $today)
                                                    ->where('janji_bayar_fulfilled', false)
                                                    ->whereNull('deleted_at')
                                                    ->count();
            
            // 2. Follow Up SP for this AO
            $this->aoFollowUpSpCount = WarningLetter::where('user_id', $userId)
                                                    ->where('deadline_date', '<=', $today)
                                                    ->whereNull('deleted_at')
                                                    ->count();
            
            // 3. Evaluations recently approved or rejected (for simplicity, handled via 'status != pending')
            // You might want to filter this by date if you only want RECENT ones, but we'll show all non-pending for now, 
            // or perhaps you only want to show it if it's "recently" updated? Let's just track approved/rejected 
            // from the last 7 days to keep it relevant.
            $this->aoEvaluationStatusCount = Evaluation::where('user_id', $userId)
                                                    ->whereIn('status', ['approved', 'rejected'])
                                                    ->where('updated_at', '>=', now()->subDays(7))
                                                    ->whereNull('deleted_at')
                                                    ->count();

            $this->totalCount = $this->aoJanjiBayarCount + 
                                $this->aoFollowUpSpCount + 
                                $this->aoEvaluationStatusCount;
        }
    }
}
