<?php

namespace Tests\Feature\Identity;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login()
    {
        foreach (['dashboard', 'practice', 'teacher.dashboard', 'admin.dashboard', 'admin.users.index'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_students_cannot_open_teacher_or_admin_areas()
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('dashboard'))->assertOk();
        $this->actingAs($student)->get(route('practice'))->assertOk();
        $this->actingAs($student)->get(route('teacher.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.classes'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_teachers_can_open_the_teacher_area_but_not_admin()
    {
        $teacher = User::factory()->withRole(RoleName::Teacher)->create();

        $this->actingAs($teacher)->get(route('teacher.dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($teacher)->post(route('admin.teacher-requests.approve', $teacher))->assertForbidden();
    }

    public function test_admins_can_open_the_admin_area()
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.teacher-requests.index'))->assertOk();
    }

    public function test_dashboard_sends_each_role_home()
    {
        $teacher = User::factory()->withRole(RoleName::Teacher)->create();
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $this->actingAs($teacher)->get(route('dashboard'))->assertRedirect(route('teacher.dashboard'));
        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_navigation_matches_the_role()
    {
        $student = User::factory()->create();
        $teacher = User::factory()->withRole(RoleName::Teacher)->create();
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $titles = fn (User $user, string $route) => collect(
            $this->actingAs($user)->get(route($route))->viewData('page')['props']['navigation']
        )->pluck('title')->all();

        $this->assertSame(
            ['Dashboard', 'Practice', 'Courses', 'Challenges', 'Leaderboards', 'My Classes', 'Profile'],
            $titles($student, 'dashboard'),
        );
        $this->assertSame(
            ['Dashboard', 'My Classes', 'Students', 'Assignments', 'Competitions', 'Reports', 'Profile'],
            $titles($teacher, 'teacher.dashboard'),
        );
        $this->assertSame(
            ['Dashboard', 'Users', 'Courses', 'Lessons', 'Classes', 'Challenges', 'Achievements', 'Analytics', 'Settings'],
            $titles($admin, 'admin.dashboard'),
        );
    }

    public function test_every_navigation_link_is_reachable_for_its_role()
    {
        foreach ([null, RoleName::Teacher, RoleName::Admin] as $role) {
            $factory = User::factory();
            $user = ($role === null ? $factory : $factory->withRole($role))->create();
            $home = $user->homeRouteName();

            $items = $this->actingAs($user)->get(route($home))->viewData('page')['props']['navigation'];

            foreach ($items as $item) {
                $this->actingAs($user)->get($item['href'])->assertOk();
            }
        }
    }

    public function test_shared_user_props_never_include_sensitive_fields()
    {
        $user = User::factory()->withTwoFactor()->create();

        $shared = $this->actingAs($user)->get(route('dashboard'))->viewData('page')['props']['auth']['user'];

        foreach (['password', 'date_of_birth', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'] as $field) {
            $this->assertArrayNotHasKey($field, $shared);
        }
        $this->assertSame(['student'], $shared['roles']);
    }
}
