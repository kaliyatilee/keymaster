<?php

namespace App\Enums;

/**
 * Permission names used in Gates and policies. The `permissions` table is
 * seeded from this list by RolesAndPermissionsSeeder.
 */
enum Permission: string
{
    // Student area
    case Practice = 'practice.use';
    case ClassesJoin = 'classes.join';

    // Teacher area
    case TeacherAccess = 'teacher.access';
    case ClassesManage = 'classes.manage';
    case AssignmentsManage = 'assignments.manage';
    case CompetitionsManage = 'competitions.manage';
    case ReportsDownload = 'reports.download';

    // Admin area
    case AdminAccess = 'admin.access';
    case UsersManage = 'users.manage';
    case TeachersApprove = 'teachers.approve';
    case ContentManage = 'content.manage';
    case GamificationManage = 'gamification.manage';
    case AttemptsModerate = 'attempts.moderate';
    case MonetisationManage = 'monetisation.manage';
    case SettingsManage = 'settings.manage';
    case AuditView = 'audit.view';

    public function label(): string
    {
        return ucfirst(str_replace(['.', '_'], ' ', $this->value));
    }

    /**
     * @return list<self>
     */
    public static function forRole(RoleName $role): array
    {
        return match ($role) {
            RoleName::Student => [self::Practice, self::ClassesJoin],
            RoleName::Teacher => [
                self::Practice, self::TeacherAccess, self::ClassesManage,
                self::AssignmentsManage, self::CompetitionsManage, self::ReportsDownload,
            ],
            RoleName::Admin => self::cases(),
        };
    }
}
