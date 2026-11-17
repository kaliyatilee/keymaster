<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Roles and permissions, profiles, parental consent and schools.
     *
     * RBAC is implemented in-house (four small tables) instead of adding a
     * package: the role set is small, and owning the code keeps permission
     * checks easy to audit. Ownership rules live in Policies, not here.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('label', 100);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('label', 150);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['role_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('display_name', 50);
            $table->string('avatar_path')->nullable();
            $table->string('bio', 280)->nullable();
            $table->char('country', 2)->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('show_on_leaderboards')->default(true);
            $table->string('keyboard_layout', 16)->default('qwerty-us');
            $table->unsignedSmallInteger('daily_goal_minutes')->default(10);
            $table->timestamps();
        });

        Schema::create('parental_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('guardian_email');
            $table->char('token_hash', 64)->unique();
            $table->enum('status', ['pending', 'granted', 'declined', 'revoked'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->string('responder_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->char('country', 2)->nullable();
            $table->string('contact_email')->nullable();
            // Set when the school's agreement covers consent for managed accounts.
            $table->timestamp('consent_agreement_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
        Schema::dropIfExists('parental_consents');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
