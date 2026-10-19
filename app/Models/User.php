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
        'billing_type',
        'monthly_tuition_fee',
        'paid_until',
        'tuition_notes',
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
            'paid_until' => 'date',
            'monthly_tuition_fee' => 'decimal:2',
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
     * Tuition payments made by this student.
     */
    public function tuitionPayments(): HasMany
    {
        return $this->hasMany(TuitionPayment::class, 'user_id')->latest();
    }

    /**
     * Get computed tuition status info.
     */
    public function getTuitionStatusAttribute(): array
    {
        $today = now()->startOfDay();

        if ($this->billing_type === 'free') {
            return [
                'code' => 'free',
                'label' => 'Miễn phí',
                'badge_class' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                'description' => 'Học bổng / Miễn học phí',
                'is_overdue' => false,
                'days' => 0,
            ];
        }

        if ($this->billing_type === 'full_course') {
            return [
                'code' => 'full_course',
                'label' => 'Trọn gói cả khóa',
                'badge_class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                'description' => 'Đã đóng trọn gói, không cần gia hạn',
                'is_overdue' => false,
                'days' => 0,
            ];
        }

        if (! $this->paid_until) {
            return [
                'code' => 'no_record',
                'label' => 'Chưa có thông tin nộp',
                'badge_class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                'description' => 'Chưa từng ghi nhận đóng học phí',
                'is_overdue' => true,
                'days' => 0,
            ];
        }

        $paidUntil = $this->paid_until->copy()->startOfDay();

        // Đang còn hạn
        if ($paidUntil->gte($today)) {
            $daysLeft = (int) $today->diffInDays($paidUntil);
            if ($daysLeft <= 5) {
                return [
                    'code' => 'due_soon',
                    'label' => 'Sắp hết hạn (' . ($daysLeft == 0 ? 'hôm nay' : 'còn ' . $daysLeft . ' ngày') . ')',
                    'badge_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                    'description' => 'Hạn đến ngày ' . $paidUntil->format('d/m/Y'),
                    'is_overdue' => false,
                    'days' => $daysLeft,
                ];
            }

            return [
                'code' => 'active',
                'label' => 'Đã nộp đủ (còn ' . $daysLeft . ' ngày)',
                'badge_class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                'description' => 'Đã đóng đến hết ' . $paidUntil->format('d/m/Y'),
                'is_overdue' => false,
                'days' => $daysLeft,
            ];
        }

        // Đã quá hạn
        $overdueDays = (int) $paidUntil->diffInDays($today);
        if ($overdueDays <= 30) {
            return [
                'code' => 'overdue',
                'label' => 'Chưa nộp tháng này (' . $overdueDays . ' ngày)',
                'badge_class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                'description' => 'Hết hạn từ ' . $paidUntil->format('d/m/Y') . ' (quá hạn ' . $overdueDays . ' ngày)',
                'is_overdue' => true,
                'days' => $overdueDays,
            ];
        }

        $overdueMonths = max(1, (int) round($overdueDays / 30));

        return [
            'code' => 'severely_overdue',
            'label' => 'Nợ ' . $overdueMonths . ' tháng (' . $overdueDays . ' ngày)',
            'badge_class' => 'bg-red-950/40 text-rose-300 border border-rose-500/40 ring-1 ring-rose-500/30',
            'description' => 'Nợ từ ' . $paidUntil->format('d/m/Y') . ' (quá hạn ' . $overdueDays . ' ngày)',
            'is_overdue' => true,
            'days' => $overdueDays,
        ];
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
