<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Identity\Actions\ReviewTeacherRequest;
use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherRequestController extends Controller
{
    public function index(): Response
    {
        $requests = User::query()
            ->whereNotNull('teacher_requested_at')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', RoleName::Teacher->value))
            ->orderBy('teacher_requested_at')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified' => $user->email_verified_at !== null,
                'requested_at' => $user->teacher_requested_at?->toISOString(),
            ]);

        return Inertia::render('admin/teacher-requests', ['requests' => $requests]);
    }

    public function approve(Request $request, User $user, ReviewTeacherRequest $review): RedirectResponse
    {
        abort_unless($user->isAwaitingTeacherApproval(), 404);
        abort_if($user->email_verified_at === null, 422, 'The applicant must verify their email first.');

        /** @var User $admin */
        $admin = $request->user();
        $review->approve($user, $admin);

        Inertia::flash('toast', ['type' => 'success', 'message' => "{$user->username} is now a teacher."]);

        return back();
    }

    public function reject(Request $request, User $user, ReviewTeacherRequest $review): RedirectResponse
    {
        abort_unless($user->isAwaitingTeacherApproval(), 404);

        /** @var User $admin */
        $admin = $request->user();
        $review->reject($user, $admin);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Teacher request from {$user->username} declined."]);

        return back();
    }
}
