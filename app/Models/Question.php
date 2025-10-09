<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    // Constants cho loại câu hỏi
    const LOAI_NHAN_BIET = 'nhan_biet';
    const LOAI_THONG_HIEU = 'thong_hieu';
    const LOAI_VAN_DUNG = 'van_dung';
    const LOAI_PHAN_TICH = 'phan_tich';
    const LOAI_TONG_HOP = 'tong_hop';
    const LOAI_DANH_GIA = 'danh_gia';

    protected $fillable = [
        'loai',
        'question',
        'is_active',
    ];

    /**
     * Danh sách các loại câu hỏi
     */
    public static function getDanhSachLoai(): array
    {
        return [
            self::LOAI_NHAN_BIET => 'Nhận biết',
            self::LOAI_THONG_HIEU => 'Thông hiểu',
            self::LOAI_VAN_DUNG => 'Vận dụng',
            self::LOAI_PHAN_TICH => 'Phân tích',
            self::LOAI_TONG_HOP => 'Tổng hợp',
            self::LOAI_DANH_GIA => 'Đánh giá',
        ];
    }

    /**
     * Lấy tên hiển thị của loại câu hỏi
     */
    public function getTenLoaiAttribute(): string
    {
        return self::getDanhSachLoai()[$this->loai] ?? 'Không xác định';
    }
    
    /**
     * Get all of the questionChoices for the Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionChoices(): HasMany
    {
        return $this->hasMany(QuestionChoice::class);
    }

    /**
     * Get all exams that this question belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
                    ->withTimestamps();
    }

    /**
     * Alias for questionChoices()
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices(): HasMany
    {
        return $this->questionChoices();
    }

    /**
     * Get the subject that owns the Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
