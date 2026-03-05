<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'document_checklist' => 'json'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function approvalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(EvaluationScore::class);
    }


    public function customAssets(): HasMany
    {
        return $this->hasMany(EvaluationCustomAsset::class);
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(EvaluationGuarantor::class);
    }

    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class);
    }

    public function externalLoans(): HasMany
    {
        return $this->hasMany(CustomerExternalLoan::class);
    }

    public function getLoanNumberAttribute()
    {
        return $this->application_id;
    }
}
