<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Identity\Actions\DeleteAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $profile = $user->profile;

        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => ! $user->isSchoolManaged(),
            'status' => $request->session()->get('status'),
            'isMinor' => $user->isMinor(),
            'isSchoolManaged' => $user->isSchoolManaged(),
            'profile' => [
                'username' => $user->username,
                'display_name' => $profile !== null ? $profile->display_name : $user->name,
                'timezone' => $user->timezone,
                'daily_goal_minutes' => $profile !== null ? $profile->daily_goal_minutes : 10,
                'is_public' => $profile !== null && $profile->is_public,
                'show_on_leaderboards' => $profile !== null && $profile->show_on_leaderboards,
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $data = $request->validated();

        DB::transaction(function () use ($user, $data) {
            $user->fill([
                'name' => $data['name'],
                'username' => $data['username'],
            ]);

            if (array_key_exists('email', $data)) {
                $user->email = $data['email'];
            }

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($data['timezone'] !== $user->timezone) {
                $user->timezone = $data['timezone'];
                $user->timezone_changed_at = now();
            }

            $user->save();

            $user->profile()->updateOrCreate([], [
                'display_name' => $data['display_name'],
                'daily_goal_minutes' => $data['daily_goal_minutes'],
                'is_public' => $data['is_public'],
                'show_on_leaderboards' => $data['show_on_leaderboards'],
            ]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile updated.')]);

        return to_route('profile.edit');
    }

    public function destroy(ProfileDeleteRequest $request, DeleteAccount $deleteAccount): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        Auth::logout();

        $deleteAccount->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
