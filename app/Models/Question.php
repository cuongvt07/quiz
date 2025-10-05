<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question',
        'is_active',
    ];
    
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
     * Get the category that owns the Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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
}
