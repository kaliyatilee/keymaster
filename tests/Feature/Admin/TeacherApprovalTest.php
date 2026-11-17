<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_pending_requests()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        $applicant = User::factory()->teacherRequested()->create();
        User::factory()->create(); // not requesting

        $this->actingAs($admin)->get(route('admin.teacher-requests.index'))
            ->assertInertia(fn ($page) => $page
                ->component('admin/teacher-requests')
                ->has('requests', 1)
                ->where('requests.0.username', $applicant->username));
    }

    public function test_admin_can_approve_a_teacher_and_it_is_audited()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        $applicant = User::factory()->teacherRequested()->create();

        $this->actingAs($admin)->post(route('admin.teacher-requests.approve', $applicant))->assertRedirect();

        $applicant->flushPermissionCache();
        $this->assertTrue($applicant->hasRole(RoleName::Teacher));
        $this->assertTrue($applicant->hasPermission('teacher.access'));
        $this->assertDatabaseHas('role_user', ['user_id' => $applicant->id, 'granted_by' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'teacher.approved',
            'actor_id' => $admin->id,
            'auditable_id' => $applicant->id,
        ]);

        $this->actingAs($applicant)->get(route('teacher.dashboard'))->assertOk();
    }

    public function test_admin_can_reject_a_teacher_request()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        $applicant = User::factory()->teacherRequested()->create();

        $this->actingAs($admin)->post(route('admin.teacher-requests.reject', $applicant))->assertRedirect();

        $this->assertNull($applicant->refresh()->teacher_requested_at);
        $this->assertFalse($applicant->hasRole(RoleName::Teacher));
        $this->assertDatabaseHas('audit_logs', ['action' => 'teacher.rejected', 'auditable_id' => $applicant->id]);
    }

    public function test_unverified_applicants_cannot_be_approved()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        $applicant = User::factory()->teacherRequested()->unverified()->create();

        $this->actingAs($admin)->post(route('admin.teacher-requests.approve', $applicant))->assertStatus(422);
        $this->assertFalse($applicant->hasRole(RoleName::Teacher));
    }

    public function test_users_without_a_request_cannot_be_approved()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.teacher-requests.approve', $user))->assertNotFound();
    }

    public function test_requests_from_approved_school_domains_are_granted_on_verification()
    {
        config(['keymaster.teacher_auto_approve_domains' => ['school.ac.zw']]);

        $applicant = User::factory()->teacherRequested()->create(['email' => 'teacher@school.ac.zw']);
        $other = User::factory()->teacherRequested()->create(['email' => 'teacher@gmail.com']);

        event(new Verified($applicant));
        event(new Verified($other));

        $this->assertTrue($applicant->fresh()->hasRole(RoleName::Teacher));
        $this->assertFalse($other->fresh()->hasRole(RoleName::Teacher));
    }

    public function test_admin_user_list_can_be_filtered()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();
        User::factory()->create(['username' => 'needle_user']);
        User::factory()->count(3)->create();

        $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'needle']))
            ->assertInertia(fn ($page) => $page
                ->component('admin/users/index')
                ->has('users.data', 1)
                ->where('users.data.0.username', 'needle_user'));
    }
}
