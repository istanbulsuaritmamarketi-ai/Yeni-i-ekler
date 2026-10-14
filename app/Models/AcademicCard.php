<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicCard extends Model
{
    protected $fillable = [
        'topic_id',
        'step_order',
        'front_image',
        'back_text',
        'extra_images_json',
        'tags_json',
    ];

    protected $casts = [
        'extra_images_json' => 'array',
        'tags_json' => 'array',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class, 'topic_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(AcademicReview::class, 'card_id');
    }
}
