<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationAsset extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(AssetComponent::class, 'asset_component_id');
    }
}
