<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
