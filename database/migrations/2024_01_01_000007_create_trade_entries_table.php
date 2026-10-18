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
        Schema::create('trade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Type & Pair (support both type/trade_type and currency_pair/pair)
            $table->string('type')->default('live');
            $table->string('trade_type')->nullable();
            $table->string('currency_pair')->nullable();
            $table->string('pair')->nullable();
            
            // Direction & Prices
            $table->string('direction')->default('buy');
            $table->decimal('entry_price', 15, 5);
            $table->decimal('stop_loss', 15, 5)->nullable();
            $table->decimal('take_profit', 15, 5)->nullable();
            $table->decimal('exit_price', 15, 5)->nullable();
            $table->decimal('lot_size', 10, 2)->nullable()->default(0.1);
            
            // Results & PnL
            $table->string('result')->default('pending');
            $table->string('status')->nullable();
            $table->decimal('pnl_pips', 10, 2)->nullable();
            $table->decimal('pnl_amount', 15, 2)->nullable();
            $table->decimal('pnl', 15, 2)->nullable();
            $table->decimal('rr_ratio', 8, 2)->nullable();
            
            // Time & Notes
            $table->dateTime('entry_time')->nullable();
            $table->dateTime('exit_time')->nullable();
            $table->string('timeframe')->nullable();
            $table->text('analysis_notes')->nullable();
            $table->text('notes')->nullable();
            $table->text('setup_description')->nullable();
            $table->text('lessons_learned')->nullable();
            
            // Charts & Attachments
            $table->json('chart_screenshots')->nullable();
            $table->string('chart_before_url')->nullable();
            $table->string('chart_after_url')->nullable();
            $table->json('setup_criteria')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_entries');
    }
};
