<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['exam_id', 'user_id', 'started_at', 'finished_at', 'score', 'correct_count', 'wrong_count', 'used_free_slot'];

    /**
     * Get the exam that owns the attempt.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user that owns the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answers for the attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ExamAttemptAnswer::class);
    }
}