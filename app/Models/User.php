<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'bio',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has student role.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Submissions created by this student.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    /**
     * Trade entries recorded by this user.
     */
    public function tradeEntries(): HasMany
    {
        return $this->hasMany(TradeEntry::class);
    }

    /**
     * Alias for tradeEntries relationship.
     */
    public function trades(): HasMany
    {
        return $this->hasMany(TradeEntry::class);
    }

    /**
     * Model trades authored by this admin.
     */
    public function modelTrades(): HasMany
    {
        return $this->hasMany(ModelTrade::class, 'admin_id');
    }

    /**
     * Notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Alias for notifications relationship.
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Submission reviews provided by this user.
     */
    public function submissionReviews(): HasMany
    {
        return $this->hasMany(SubmissionReview::class, 'reviewer_id');
    }

    /**
     * Questions asked by this user.
     */
    public function lessonQuestions(): HasMany
    {
        return $this->hasMany(LessonQuestion::class);
    }

    /**
     * Courses that this student is enrolled in or granted access to.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * Courses that this student currently has active access to.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('is_active', true);
    }

    /**
     * Check if user has permission / access to a specific course.
     */
    public function hasCourseAccess(Course|int $course): bool
    {
        // Admin has access to all courses
        if ($this->isAdmin()) {
            return true;
        }

        $courseId = $course instanceof Course ? $course->id : $course;

        return $this->enrolledCourses()->where('courses.id', $courseId)->exists();
    }

    /**
     * Scope for student users.
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope for admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
