<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTopic extends Model
{
    protected $fillable = ['course_id', 'title'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(AcademicCourse::class, 'course_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(AcademicCard::class, 'topic_id');
    }
}
