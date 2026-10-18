<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TradeEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'trade_type',
        'currency_pair',
        'pair',
        'direction',
        'entry_price',
        'stop_loss',
        'take_profit',
        'exit_price',
        'lot_size',
        'analysis_notes',
        'notes',
        'setup_description',
        'lessons_learned',
        'chart_screenshots',
        'chart_before_url',
        'chart_after_url',
        'result',
        'status',
        'pnl_pips',
        'pnl_amount',
        'pnl',
        'rr_ratio',
        'entry_time',
        'exit_time',
        'timeframe',
        'setup_criteria',
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
            'entry_time' => 'datetime',
            'exit_time' => 'datetime',
            'entry_price' => 'decimal:5',
            'stop_loss' => 'decimal:5',
            'take_profit' => 'decimal:5',
            'exit_price' => 'decimal:5',
            'lot_size' => 'decimal:2',
            'pnl_pips' => 'decimal:2',
            'pnl_amount' => 'decimal:2',
            'pnl' => 'decimal:2',
            'rr_ratio' => 'decimal:2',
        ];
    }

    /**
     * Booted method to sync aliased fields.
     */
    protected static function booted(): void
    {
        static::saving(function (TradeEntry $trade) {
            // Pair & Currency pair
            if (!empty($trade->currency_pair) && empty($trade->pair)) {
                $trade->pair = strtoupper($trade->currency_pair);
            } elseif (!empty($trade->pair) && empty($trade->currency_pair)) {
                $trade->currency_pair = strtoupper($trade->pair);
            }

            // Type & Trade type
            if (!empty($trade->type) && empty($trade->trade_type)) {
                $trade->trade_type = strtolower($trade->type);
            } elseif (!empty($trade->trade_type) && empty($trade->type)) {
                $trade->type = strtolower($trade->trade_type);
            }

            // Direction (normalize to lowercase or uppercase as needed)
            if (!empty($trade->direction)) {
                $trade->direction = strtolower($trade->direction);
            }

            // Result & Status
            if (!empty($trade->result) && empty($trade->status)) {
                $trade->status = strtolower($trade->result);
            } elseif (!empty($trade->status) && empty($trade->result)) {
                $trade->result = strtolower($trade->status);
            }

            // PnL & PnL Amount
            if ($trade->pnl_amount !== null && $trade->pnl === null) {
                $trade->pnl = $trade->pnl_amount;
            } elseif ($trade->pnl !== null && $trade->pnl_amount === null) {
                $trade->pnl_amount = $trade->pnl;
            }

            // Notes & Analysis notes
            if (!empty($trade->analysis_notes) && empty($trade->notes)) {
                $trade->notes = $trade->analysis_notes;
            } elseif (!empty($trade->notes) && empty($trade->analysis_notes)) {
                $trade->analysis_notes = $trade->notes;
            }
        });
    }

    /**
     * The user who logged this trade entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Model trade generated or associated with this trade entry.
     */
    public function modelTrade(): HasOne
    {
        return $this->hasOne(ModelTrade::class);
    }

    /**
     * Scope for backtest entries.
     */
    public function scopeBacktests(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('type', 'backtest')->orWhere('trade_type', 'backtest');
        });
    }

    /**
     * Scope for live trade entries.
     */
    public function scopeLiveTrades(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('type', 'live')->orWhere('trade_type', 'live');
        });
    }

    /**
     * Scope for demo trade entries.
     */
    public function scopeDemoTrades(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('type', 'demo')->orWhere('trade_type', 'demo');
        });
    }

    /**
     * Scope for winning trade entries.
     */
    public function scopeWins(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('result', 'win')->orWhere('status', 'win');
        });
    }

    /**
     * Scope for losing trade entries.
     */
    public function scopeLosses(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('result', 'loss')->orWhere('status', 'loss');
        });
    }
}
