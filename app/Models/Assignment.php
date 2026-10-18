<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'requirements',
        'due_date',
        'type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    /**
     * The lesson that owns this assignment.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Submissions for this assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Scope a query to only include active/published assignments.
     */
    public function scopeActive($query)
    {
        return $query;
    }

    public function scopePublished($query)
    {
        return $query;
    }

    /**
     * Get the course through lesson.
     */
    public function course(): ?Course
    {
        return $this->lesson?->course;
    }

    /**
     * Accessor for course property.
     */
    public function getCourseAttribute(): ?Course
    {
        return $this->course();
    }
}
