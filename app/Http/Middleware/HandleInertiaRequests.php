<?php

namespace App\Http\Middleware;

use App\Domain\Identity\NavigationBuilder;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(private readonly NavigationBuilder $navigation) {}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Only whitelisted, non-sensitive user fields are shared with the browser.
     * Date of birth, two-factor secrets and password hashes never leave the
     * server.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var User|null $user */
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user === null ? null : $this->sharedUser($user),
            ],
            'navigation' => fn () => $this->navigation->for($user),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sharedUser(User $user): array
    {
        $user->loadMissing('roles:id,name', 'profile');

        $avatar = $user->profile?->avatar_path;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'display_name' => $user->profile !== null ? $user->profile->display_name : $user->name,
            ...($avatar !== null ? ['avatar' => $avatar] : []),
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'two_factor_enabled' => $user->two_factor_confirmed_at !== null,
            'status' => $user->status->value,
            'roles' => $user->roles->pluck('name')->values()->all(),
            'is_school_managed' => $user->isSchoolManaged(),
            'awaiting_teacher_approval' => $user->isAwaitingTeacherApproval(),
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}
