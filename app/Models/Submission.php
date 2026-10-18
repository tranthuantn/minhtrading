<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'assignment_id',
        'student_id',
        'user_id',
        'content',
        'attachments',
        'attachment_path',
        'chart_url',
        'trade_data',
        'score',
        'status',
        'is_featured',
        'featured_note',
        'featured_at',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'trade_data' => 'array',
            'is_featured' => 'boolean',
            'featured_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    /**
     * Booted method to sync student_id and user_id.
     */
    protected static function booted(): void
    {
        static::saving(function (Submission $submission) {
            if (!empty($submission->student_id) && empty($submission->user_id)) {
                $submission->user_id = $submission->student_id;
            } elseif (!empty($submission->user_id) && empty($submission->student_id)) {
                $submission->student_id = $submission->user_id;
            }
        });
    }

    /**
     * The assignment this submission belongs to.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * The student who created this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * User alias for student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Reviews on this submission.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(SubmissionReview::class);
    }

    /**
     * Latest review.
     */
    public function latestReview(): HasOne
    {
        return $this->hasOne(SubmissionReview::class)->latestOfMany();
    }

    /**
     * Scope for pending submissions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for reviewed submissions.
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Scope for approved submissions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for submissions requiring revision.
     */
    public function scopeRevisionNeeded($query)
    {
        return $query->where('status', 'revision_needed');
    }

    /**
     * Scope for featured/exemplary submissions.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
