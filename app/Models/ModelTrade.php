<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelTrade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'trade_entry_id',
        'source_trade_entry_id',
        'title',
        'description',
        'currency_pair',
        'pair',
        'direction',
        'entry_price',
        'stop_loss',
        'take_profit',
        'exit_price',
        'pnl',
        'rr_ratio',
        'setup_name',
        'analysis',
        'explanation',
        'notes',
        'chart_screenshots',
        'chart_image',
        'timeframe',
        'setup_criteria',
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
            'chart_screenshots' => 'array',
            'setup_criteria' => 'array',
            'is_published' => 'boolean',
            'entry_price' => 'decimal:5',
            'stop_loss' => 'decimal:5',
            'take_profit' => 'decimal:5',
            'exit_price' => 'decimal:5',
            'pnl' => 'decimal:2',
            'rr_ratio' => 'decimal:2',
        ];
    }

    /**
     * Booted method to sync aliased fields.
     */
    protected static function booted(): void
    {
        static::saving(function (ModelTrade $trade) {
            // Admin & User ID
            if (!empty($trade->admin_id) && empty($trade->user_id)) {
                $trade->user_id = $trade->admin_id;
            } elseif (!empty($trade->user_id) && empty($trade->admin_id)) {
                $trade->admin_id = $trade->user_id;
            }

            // Pair & Currency pair
            if (!empty($trade->currency_pair) && empty($trade->pair)) {
                $trade->pair = strtoupper($trade->currency_pair);
            } elseif (!empty($trade->pair) && empty($trade->currency_pair)) {
                $trade->currency_pair = strtoupper($trade->pair);
            }

            // Trade Entry ID & Source Trade Entry ID
            if (!empty($trade->trade_entry_id) && empty($trade->source_trade_entry_id)) {
                $trade->source_trade_entry_id = $trade->trade_entry_id;
            } elseif (!empty($trade->source_trade_entry_id) && empty($trade->trade_entry_id)) {
                $trade->trade_entry_id = $trade->source_trade_entry_id;
            }

            // Analysis & Explanation
            if (!empty($trade->analysis) && empty($trade->explanation)) {
                $trade->explanation = $trade->analysis;
            } elseif (!empty($trade->explanation) && empty($trade->analysis)) {
                $trade->analysis = $trade->explanation;
            }
        });
    }

    /**
     * The admin user who authored this model trade.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Alias for creator user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Creator relation alias.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Source trade entry.
     */
    public function tradeEntry(): BelongsTo
    {
        return $this->belongsTo(TradeEntry::class, 'trade_entry_id');
    }

    /**
     * Source trade relation alias.
     */
    public function sourceTrade(): BelongsTo
    {
        return $this->belongsTo(TradeEntry::class, 'source_trade_entry_id');
    }

    /**
     * Scope a query to only include published model trades.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
