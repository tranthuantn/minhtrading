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
        Schema::create('model_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('trade_entry_id')->nullable()->constrained('trade_entries')->cascadeOnDelete();
            $table->foreignId('source_trade_entry_id')->nullable()->constrained('trade_entries')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('currency_pair')->nullable();
            $table->string('pair')->nullable();
            $table->string('direction')->default('buy');
            $table->decimal('entry_price', 15, 5);
            $table->decimal('stop_loss', 15, 5)->nullable();
            $table->decimal('take_profit', 15, 5)->nullable();
            $table->decimal('exit_price', 15, 5)->nullable();
            $table->decimal('pnl', 15, 2)->nullable();
            $table->decimal('rr_ratio', 8, 2)->nullable();
            $table->string('setup_name')->nullable();
            $table->longText('analysis')->nullable();
            $table->text('explanation')->nullable();
            $table->text('notes')->nullable();
            $table->json('chart_screenshots')->nullable();
            $table->string('chart_image')->nullable();
            $table->string('timeframe')->nullable();
            $table->json('setup_criteria')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_trades');
    }
};
