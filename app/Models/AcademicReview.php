<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicReview extends Model
{
    protected $fillable = [
        'card_id',
        'repetition_count',
        'ease_factor',
        'interval_days',
        'next_review_at',
        'last_reviewed_at',
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'ease_factor' => 'float',
        'repetition_count' => 'integer',
        'interval_days' => 'integer',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(AcademicCard::class, 'card_id');
    }
}
