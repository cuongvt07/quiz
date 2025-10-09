<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'title', 'duration_minutes', 'total_questions'];

    /**
     * Get the subject that owns the exam.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the exam attempts for the exam.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    /**
     * Get the questions for the exam.
     */
    public function questions() {
        return $this->belongsToMany(Question::class, 'exam_questions');
    }

    /**
     * Lấy loại thi (Năng lực/Tư duy) từ subject
     */
    public function getTypeAttribute(): ?string
    {
        return $this->subject?->type;
    }

    /**
     * Lấy tên loại thi
     */
    public function getTypeNameAttribute(): string
    {
        return $this->subject?->type_name ?? 'Chưa xác định';
    }

    /**
     * Kiểm tra đề thi có phải dạng Năng lực không
     */
    public function isCompetency(): bool
    {
        return $this->subject?->isCompetency() ?? false;
    }

    /**
     * Kiểm tra đề thi có phải dạng Tư duy không
     */
    public function isCognitive(): bool
    {
        return $this->subject?->isCognitive() ?? false;
    }

    /**
     * Lấy số lượt thi đã sử dụng của user
     */
    public function getAttemptsCountForUser(int $userId): int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('used_free_slot', true)
            ->count();
    }

    /**
     * Kiểm tra user còn lượt thi miễn phí không
     * 
     * Logic: 
     * - Nếu còn free_slots > 0: cho phép thi (không quan tâm số lần đã thi)
     * - Nếu free_slots = 0: kiểm tra đã thi >= 2 lần chưa
     */
    public function canUserAttempt(User $user): bool
    {
        // Nếu còn free_slots thì luôn cho thi
        if ($user->free_slots > 0) {
            return true;
        }

        // Nếu hết free_slots thì kiểm tra số lần đã thi
        $usedFreeAttempts = $this->attempts()
            ->where('user_id', $user->id)
            ->where('used_free_slot', true)
            ->count();

        // Chỉ block khi hết free_slots VÀ đã thi >= 2 lần
        return $usedFreeAttempts < 2;
    }
}