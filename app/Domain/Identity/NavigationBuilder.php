<?php

namespace App\Domain\Identity;

use App\Enums\RoleName;
use App\Models\User;

/**
 * Builds the sidebar menu on the server from the user's role, so the client
 * never decides what a user may see. Menus follow the product brief.
 *
 * @phpstan-type NavItem array{title: string, href: string, icon: string}
 */
class NavigationBuilder
{
    /**
     * @return list<NavItem>
     */
    public function for(?User $user): array
    {
        if ($user === null || ! $user->isActive()) {
            return [];
        }

        return match (true) {
            $user->hasRole(RoleName::Admin) => $this->admin(),
            $user->hasRole(RoleName::Teacher) => $this->teacher(),
            default => $this->student(),
        };
    }

    /** @return list<NavItem> */
    private function student(): array
    {
        return [
            $this->item('Dashboard', 'dashboard', 'layout-grid'),
            $this->item('Practice', 'practice', 'keyboard'),
            $this->item('Courses', 'pages.courses', 'book-open'),
            $this->item('Challenges', 'challenges', 'flame'),
            $this->item('Leaderboards', 'pages.leaderboard', 'trophy'),
            $this->item('My Classes', 'classes', 'users'),
            $this->item('Profile', 'profile.edit', 'user'),
        ];
    }

    /** @return list<NavItem> */
    private function teacher(): array
    {
        return [
            $this->item('Dashboard', 'teacher.dashboard', 'layout-grid'),
            $this->item('My Classes', 'teacher.classes', 'school'),
            $this->item('Students', 'teacher.students', 'users'),
            $this->item('Assignments', 'teacher.assignments', 'clipboard-list'),
            $this->item('Competitions', 'teacher.competitions', 'trophy'),
            $this->item('Reports', 'teacher.reports', 'file-bar-chart'),
            $this->item('Profile', 'profile.edit', 'user'),
        ];
    }

    /** @return list<NavItem> */
    private function admin(): array
    {
        return [
            $this->item('Dashboard', 'admin.dashboard', 'layout-grid'),
            $this->item('Users', 'admin.users.index', 'users'),
            $this->item('Courses', 'admin.courses', 'book-open'),
            $this->item('Lessons', 'admin.lessons', 'list-checks'),
            $this->item('Classes', 'admin.classes', 'school'),
            $this->item('Challenges', 'admin.challenges', 'flame'),
            $this->item('Achievements', 'admin.achievements', 'award'),
            $this->item('Analytics', 'admin.analytics', 'chart-line'),
            $this->item('Settings', 'admin.settings', 'settings'),
        ];
    }

    /** @return NavItem */
    private function item(string $title, string $route, string $icon): array
    {
        return [
            'title' => $title,
            'href' => route($route, absolute: false),
            'icon' => $icon,
        ];
    }
}
