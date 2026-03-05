<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationNotification extends Model
{
    protected $fillable = ['message'];

    /**
     * Scope to get recent notifications (last 1 hour)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subHour());
    }
}
