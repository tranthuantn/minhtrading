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
        // Add featured columns to submissions
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
            $table->text('featured_note')->nullable()->after('is_featured');
            $table->timestamp('featured_at')->nullable()->after('featured_note');
        });

        // Create lesson questions table
        Schema::create('lesson_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->json('images')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->text('featured_note')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->timestamps();
        });

        // Create question answers table
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('lesson_questions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('lesson_questions');

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_note', 'featured_at']);
        });
    }
};
