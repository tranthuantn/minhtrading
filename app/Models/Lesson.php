<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'attachments',
        'sort_order',
        'is_published',
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
     * Scope a query to only include published lessons.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * The course that owns this lesson.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Assignments associated with this lesson.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Questions asked for this lesson.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(LessonQuestion::class)->latest();
    }

    /**
     * Convert standard video URL (YouTube, Google Drive, Vimeo, Loom) to embeddable URL.
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        $url = trim($this->video_url ?? '');
        if (empty($url)) {
            return null;
        }

        // If user pasted an <iframe> tag, extract src attribute
        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $url, $matches)) {
            $url = $matches[1];
        }

        // 1. Google Drive: convert /view, /edit, or query id to /preview
        if (str_contains($url, 'drive.google.com')) {
            // Pattern: /file/d/{id}
            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
            }
            // Pattern: ?id={id} or &id={id}
            if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
            }
        }

        // 2. YouTube: watch?v=, youtu.be/, shorts/, embed/
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
            if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
            if (preg_match('/\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
            if (str_contains($url, '/embed/')) {
                return $url;
            }
        }

        // 3. Vimeo: vimeo.com/{id}
        if (str_contains($url, 'vimeo.com')) {
            if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/', $url, $matches)) {
                $vimeoId = end($matches);
                return 'https://player.vimeo.com/video/' . $vimeoId;
            }
        }

        // 4. Loom: loom.com/share/{id}
        if (str_contains($url, 'loom.com')) {
            if (preg_match('/loom\.com\/share\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.loom.com/embed/' . $matches[1];
            }
        }

        return $url;
    }

    /**
     * Detect video provider (youtube, drive, vimeo, loom, or direct).
     */
    public function getVideoPlatformAttribute(): string
    {
        $url = strtolower($this->video_url ?? '');
        if (str_contains($url, 'drive.google.com')) return 'drive';
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) return 'youtube';
        if (str_contains($url, 'vimeo.com')) return 'vimeo';
        if (str_contains($url, 'loom.com')) return 'loom';
        if (preg_match('/\.(mp4|webm|ogg)$/i', $url)) return 'direct';
        return 'other';
    }
}
