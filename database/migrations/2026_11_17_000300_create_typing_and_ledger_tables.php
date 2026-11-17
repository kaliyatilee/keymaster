<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Typing sessions and attempts, the XP ledger, levels, cached user
     * statistics and the audit log.
     *
     * `typing_attempts`, `typing_attempt_logs`, `attempt_moderations`,
     * `xp_transactions` and `audit_logs` are append-only: they have no
     * `updated_at`, and the runtime database account is granted SELECT and
     * INSERT only (see `keymaster:db-grants`).
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('level_number')->unique();
            $table->string('name', 50);
            $table->unsignedInteger('xp_required')->unique();
            $table->string('badge_path')->nullable();
            $table->timestamps();
        });

        Schema::create('typing_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('context_type', ['test', 'lesson', 'challenge', 'assignment', 'competition']);
            $table->unsignedBigInteger('context_id')->nullable();
            $table->string('content_type', 30);
            $table->unsignedBigInteger('content_id');
            $table->char('content_checksum', 64);
            $table->json('rules');
            $table->timestamp('issued_at', 3);
            $table->timestamp('expires_at', 3);
            $table->timestamp('consumed_at', 3)->nullable();

            $table->index(['user_id', 'issued_at']);
        });

        Schema::create('typing_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->ulid('session_id')->unique();
            $table->foreign('session_id')->references('id')->on('typing_sessions')->restrictOnDelete();
            $table->enum('context_type', ['test', 'lesson', 'challenge', 'assignment', 'competition']);
            $table->unsignedBigInteger('context_id')->nullable();
            $table->string('content_type', 30);
            $table->unsignedBigInteger('content_id');
            $table->timestamp('started_at', 3);
            $table->timestamp('ended_at', 3);
            $table->unsignedInteger('elapsed_ms');
            $table->unsignedInteger('chars_typed');
            $table->unsignedInteger('chars_correct');
            $table->unsignedInteger('chars_incorrect');
            $table->unsignedInteger('corrections')->default(0);
            $table->decimal('gross_wpm', 6, 2);
            $table->decimal('net_wpm', 6, 2);
            $table->decimal('accuracy', 5, 2)->nullable();
            $table->boolean('completed');
            $table->enum('validation_status', ['valid', 'flagged', 'rejected']);
            $table->json('validation_flags')->nullable();
            $table->timestamp('created_at', 3)->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['context_type', 'context_id', 'net_wpm']);
            $table->index(['validation_status', 'created_at']);
        });

        Schema::create('typing_attempt_logs', function (Blueprint $table) {
            $table->foreignId('attempt_id')->primary()->constrained('typing_attempts')->restrictOnDelete();
            $table->mediumBinary('log_gz');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('attempt_moderations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('typing_attempts')->restrictOnDelete();
            $table->enum('decision', ['upheld', 'voided']);
            $table->string('reason', 500);
            $table->foreignId('moderator_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('attempt_id');
        });

        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->integer('amount');
            $table->string('rule_key', 64);
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('idempotency_key', 191)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('user_statistics', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->integer('xp_total')->default(0);
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->decimal('best_net_wpm', 6, 2)->default(0);
            $table->unsignedInteger('attempts_count')->default(0);
            $table->decimal('sum_net_wpm', 12, 2)->default(0);
            $table->decimal('sum_accuracy', 12, 2)->default(0);
            $table->unsignedBigInteger('total_practice_ms')->default(0);
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('action', 64);
            $table->string('auditable_type', 100)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['actor_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->json('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_statistics');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('attempt_moderations');
        Schema::dropIfExists('typing_attempt_logs');
        Schema::dropIfExists('typing_attempts');
        Schema::dropIfExists('typing_sessions');
        Schema::dropIfExists('levels');
    }
};
