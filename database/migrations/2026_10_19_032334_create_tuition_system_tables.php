<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm các trường học phí vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->string('billing_type')->default('monthly')->after('is_active'); // monthly, multi_month, full_course, free
            $table->decimal('monthly_tuition_fee', 15, 2)->default(2000000)->after('billing_type');
            $table->date('paid_until')->nullable()->after('monthly_tuition_fee');
            $table->text('tuition_notes')->nullable()->after('paid_until');
        });

        // 2. Tạo bảng lưu lịch sử các lần thu tiền học phí
        Schema::create('tuition_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->integer('months_count')->default(1);
            $table->string('billing_type')->default('monthly'); // monthly, multi_month, full_course, custom
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, cash, crypto, other
            $table->string('transaction_code')->nullable();
            $table->string('payment_proof')->nullable(); // Ảnh bill chuyển khoản
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_payments');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['billing_type', 'monthly_tuition_fee', 'paid_until', 'tuition_notes']);
        });
    }
};
