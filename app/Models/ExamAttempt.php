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

    /**
     * Kiểm tra lượt thi đã hoàn thành chưa
     */
    public function isCompleted(): bool
    {
        return !is_null($this->finished_at);
    }

    /**
     * Kiểm tra lượt thi đang trong quá trình làm bài
     */
    public function isInProgress(): bool
    {
        return !is_null($this->started_at) && is_null($this->finished_at);
    }

    /**
     * Lấy loại thi (Năng lực/Tư duy)
     */
    public function getTypeAttribute(): ?string
    {
        return $this->exam?->type;
    }

    /**
     * Lấy tên loại thi
     */
    public function getTypeNameAttribute(): string
    {
        return $this->exam?->type_name ?? 'Chưa xác định';
    }

    /**
     * Tính thời gian làm bài (phút)
     */
    public function getDurationInMinutesAttribute(): ?float
    {
        if ($this->started_at && $this->finished_at) {
            $start = \Carbon\Carbon::parse($this->started_at);
            $end = \Carbon\Carbon::parse($this->finished_at);
            return round($end->diffInMinutes($start, true), 2);
        }
        return null;
    }

    /**
     * Tính phần trăm điểm
     */
    public function getScorePercentageAttribute(): ?float
    {
        if ($this->score && $this->exam?->total_questions) {
            return round(($this->score / $this->exam->total_questions) * 100, 2);
        }
        return null;
    }
}