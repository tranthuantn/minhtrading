<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'is_published',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Sync order attribute with sort_order column for backward compatibility.
     */
    protected function order(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['sort_order'] ?? 0,
            set: fn ($value) => ['sort_order' => (int) $value],
        );
    }

    /**
     * Scope a query to only include published courses.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Lessons belonging to this course ordered by sort_order.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Assignments belonging to this course through lessons.
     */
    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(Assignment::class, Lesson::class);
    }

    /**
     * Students enrolled or granted access to this course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * Only actively enrolled students.
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('is_active', true);
    }

    /**
     * Check if a specific student is enrolled.
     */
    public function hasStudent(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->enrolledStudents()->where('users.id', $userId)->exists();
    }
}
