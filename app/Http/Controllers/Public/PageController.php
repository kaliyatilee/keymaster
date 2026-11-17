<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public, server-rendered pages. These are the SEO entry points.
 */
class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('welcome');
    }

    public function about(): Response
    {
        return Inertia::render('public/about');
    }

    public function privacy(): Response
    {
        return Inertia::render('public/privacy', ['consentAge' => (int) config('keymaster.consent_age')]);
    }

    public function terms(): Response
    {
        return Inertia::render('public/terms');
    }

    public function courses(): Response
    {
        return $this->upcoming('Typing courses', 3, 'Keyboard Fundamentals, Beginner, Intermediate and Advanced courses, each with step-by-step lessons.');
    }

    public function typingTest(): Response
    {
        return $this->upcoming('Typing test', 3, 'A free typing speed test with live words per minute and accuracy. Sign up to save your results.');
    }

    public function leaderboard(): Response
    {
        return $this->upcoming('Leaderboard', 4, 'Daily, weekly, monthly and all-time rankings built only from verified typing results.');
    }

    private function upcoming(string $title, int $phase, string $description): Response
    {
        return Inertia::render('public/upcoming', compact('title', 'phase', 'description'));
    }
}
