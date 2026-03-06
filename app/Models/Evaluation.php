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
        'document_checklist' => 'json',
        'evaluation_date' => 'date',
        'loan_amount' => 'float',
        'loan_term_months' => 'integer',
        'loan_interest_rate' => 'float',
        'old_loan_amount' => 'float',
        'old_loan_term_months' => 'integer',
        'old_loan_interest_rate' => 'float',
        'customer_entreprenuership_year' => 'integer',
        'path_distance' => 'float',
        'rpc_ratio' => 'float',
        'approved_amount' => 'float',
        'approved_tenor' => 'integer',
        'approved_interest_rate' => 'float',
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
