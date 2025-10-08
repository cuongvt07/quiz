<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    // Constants cho loại thi
    const TYPE_COMPETENCY = 'nang_luc';  // Năng lực
    const TYPE_COGNITIVE = 'tu_duy';     // Tư duy

    protected $fillable = ['name', 'type'];

    /**
     * Get the exams for the subject.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Kiểm tra môn học có phải dạng Năng lực không
     */
    public function isCompetency(): bool
    {
        return $this->type === self::TYPE_COMPETENCY;
    }

    /**
     * Kiểm tra môn học có phải dạng Tư duy không
     */
    public function isCognitive(): bool
    {
        return $this->type === self::TYPE_COGNITIVE;
    }

    /**
     * Lấy tên hiển thị của loại thi
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            self::TYPE_COMPETENCY => 'Năng lực',
            self::TYPE_COGNITIVE => 'Tư duy',
            default => 'Chưa xác định'
        };
    }

    /**
     * Lấy danh sách các loại thi
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_COMPETENCY => 'Năng lực',
            self::TYPE_COGNITIVE => 'Tư duy',
        ];
    }
}