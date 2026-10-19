<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'course_id',
        'amount',
        'months_count',
        'billing_type',
        'start_date',
        'end_date',
        'payment_method',
        'transaction_code',
        'payment_proof',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'months_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * The student who paid.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for student relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The admin who recorded this transaction.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Optional related course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Format payment method text.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'cash' => 'Tiền mặt',
            'crypto' => 'USDT / Crypto',
            default => 'Khác',
        };
    }

    /**
     * Format billing type text.
     */
    public function getBillingTypeLabelAttribute(): string
    {
        return match ($this->billing_type) {
            'monthly' => 'Theo từng tháng',
            'multi_month' => 'Đóng trước ' . $this->months_count . ' tháng',
            'full_course' => 'Trọn gói cả khóa',
            'custom' => 'Tùy chỉnh kỳ hạn',
            default => 'Học phí',
        };
    }
}

