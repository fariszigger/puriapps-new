<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditDisbursement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'nomor_spk',
        'customer_name',
        'address',
        'amount',
        'jangka_waktu',
        'suku_bunga',
        'jenis_pinjaman',
        'angsuran',
        'disbursement_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'disbursement_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
