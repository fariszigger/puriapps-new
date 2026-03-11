<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarningLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'letter_date' => 'date',
        'credit_agreement_date' => 'date',
        'tunggakan_date' => 'date',
        'deadline_date' => 'date',
        'tunggakan_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get label text for the letter type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'sp1' => 'Surat Peringatan I (Pertama)',
            'sp2' => 'Surat Peringatan II (Kedua)',
            'sp3' => 'Surat Peringatan III (Ketiga)',
            'panggilan' => 'Surat Panggilan',
            default => $this->type,
        };
    }

    /**
     * Get short label
     */
    public function getTypeShortLabelAttribute(): string
    {
        return match($this->type) {
            'sp1' => 'SP-1',
            'sp2' => 'SP-2',
            'sp3' => 'SP-3',
            'panggilan' => 'Panggilan',
            default => $this->type,
        };
    }
    /**
     * Generate the next letter number automatically
     * Format: NNN/BPR.PURI.KRD/M/YYYY (e.g., 117/BPR.PURI.KRD/X/2025)
     */
    public static function generateLetterNumber(\Carbon\Carbon $date = null): string
    {
        $date = $date ?? now();
        $year = $date->year;
        
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[$date->month];

        // Find the last letter created in this year to get the next sequence number
        $lastLetter = self::whereYear('letter_date', $year)
            ->whereNotNull('letter_number')
            ->where('letter_number', 'LIKE', '%/BPR.PURI.KRD/%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastLetter) {
            // Extract the number part from the beginning of the string
            // Format is "117/BPR.PURI.KRD/X/2025"
            $parts = explode('/', $lastLetter->letter_number);
            if (is_numeric($parts[0])) {
                $sequence = (int) $parts[0] + 1;
            }
        }

        // Format sequence to always be at least 3 digits, e.g., 001, 012, 117
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return "{$sequenceFormatted}/BPR.PURI.KRD/{$romanMonth}/{$year}";
    }
}
