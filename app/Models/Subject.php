<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    /**
     * Get the exams for the subject.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}