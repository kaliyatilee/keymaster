<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Suspended users are signed out. Users waiting for parental consent can
 * only see the "waiting for approval" page and sign out.
 */
class EnsureAccountIsActive
{
    /** @var list<string> */
    private const PENDING_ALLOWED_ROUTES = [
        'consent.pending',
        'consent.resend',
        'consent.show',
        'consent.respond',
        'verification.*',
        'logout',
        'home',
        'pages.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if ($user->status === AccountStatus::Suspended) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'This account has been suspended. Please contact support.',
            ]);
        }

        if ($user->status === AccountStatus::PendingConsent
            && ! $request->routeIs(...self::PENDING_ALLOWED_ROUTES)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Parental consent is required.'], 403)
                : redirect()->route('consent.pending');
        }

        return $next($request);
    }
}
