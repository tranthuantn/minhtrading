<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'title',
        'content',
        'images',
        'is_featured',
        'featured_note',
        'featured_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_featured' => 'boolean',
            'featured_at' => 'datetime',
        ];
    }

    /**
     * The student who asked the question.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The course this question is associated with (if any).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The lesson this question belongs to (null if outside lesson).
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if question is outside of any lesson.
     */
    public function isOutsideLesson(): bool
    {
        return is_null($this->lesson_id);
    }

    /**
     * Answers to this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->latest();
    }

    /**
     * Scope a query to only include featured questions.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

