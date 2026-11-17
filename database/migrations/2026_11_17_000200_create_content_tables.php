<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Courses, lessons, versioned lesson text, reusable passages, progress.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->enum('level', ['fundamentals', 'beginner', 'intermediate', 'advanced']);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'sort_order']);
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('slug', 100);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('practice_mode', [
                'home_row', 'top_row', 'bottom_row', 'keys', 'finger_placement',
                'words', 'sentences', 'paragraphs', 'numbers', 'punctuation', 'custom',
            ]);
            $table->unsignedTinyInteger('difficulty')->default(1);
            $table->unsignedSmallInteger('estimated_minutes')->default(5);
            $table->decimal('passing_accuracy', 5, 2)->default(90);
            $table->unsignedSmallInteger('target_wpm')->default(0);
            $table->unsignedSmallInteger('xp_reward')->default(10);
            $table->enum('backspace_mode', ['allowed', 'blocked', 'errors_only'])->default('allowed');
            $table->unsignedSmallInteger('time_limit_seconds')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'status', 'sort_order']);
        });

        Schema::create('lesson_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->text('body');
            $table->unsignedInteger('char_count');
            $table->char('checksum', 64);
            $table->boolean('is_current')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['lesson_id', 'version']);
            $table->index(['lesson_id', 'is_current']);
        });

        Schema::create('text_passages', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['system', 'teacher', 'user']);
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 150)->nullable();
            $table->string('language', 5)->default('en');
            $table->text('body');
            $table->unsignedInteger('char_count');
            $table->char('checksum', 64);
            $table->enum('status', ['draft', 'active', 'archived'])->default('active');
            $table->timestamps();

            $table->index(['source', 'status']);
            $table->index('owner_id');
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'passed'])->default('not_started');
            $table->decimal('best_net_wpm', 6, 2)->nullable();
            $table->decimal('best_accuracy', 5, 2)->nullable();
            $table->unsignedInteger('attempts_count')->default(0);
            $table->timestamp('first_passed_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('text_passages');
        Schema::dropIfExists('lesson_content');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
    }
};
