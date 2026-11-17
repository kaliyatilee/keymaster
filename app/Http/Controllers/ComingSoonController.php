<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Placeholder for features scheduled in later phases. Each entry says
 * plainly which phase delivers it, so nothing pretends to work.
 */
class ComingSoonController extends Controller
{
    /** @var array<string, array{title: string, phase: int, description: string}> */
    private const FEATURES = [
        'practice' => ['title' => 'Practice', 'phase' => 3, 'description' => 'Home row, top row, bottom row, words, sentences, numbers, punctuation and custom text practice, with live WPM and accuracy.'],
        'challenges' => ['title' => 'Challenges', 'phase' => 4, 'description' => 'Daily, weekly and monthly challenges where everyone types the same text under the same rules.'],
        'student-classes' => ['title' => 'My Classes', 'phase' => 5, 'description' => 'Join a class with a code from your teacher and see your assignments and class leaderboard.'],
        'teacher-classes' => ['title' => 'My Classes', 'phase' => 5, 'description' => 'Create classes, share join codes and manage enrolled students.'],
        'teacher-students' => ['title' => 'Students', 'phase' => 5, 'description' => 'Progress, trends and weakest keys for each student in your classes.'],
        'assignments' => ['title' => 'Assignments', 'phase' => 5, 'description' => 'Assign lessons or custom texts with target WPM, minimum accuracy and deadlines.'],
        'competitions' => ['title' => 'Competitions', 'phase' => 5, 'description' => 'Run best-score competitions and live races for your classes.'],
        'reports' => ['title' => 'Reports', 'phase' => 5, 'description' => 'Download class results as CSV or PDF.'],
        'admin-courses' => ['title' => 'Courses', 'phase' => 6, 'description' => 'Create, order and publish courses.'],
        'admin-lessons' => ['title' => 'Lessons', 'phase' => 6, 'description' => 'Edit lessons and publish new versions of their text.'],
        'admin-classes' => ['title' => 'Classes', 'phase' => 6, 'description' => 'Oversee all classes and schools.'],
        'admin-challenges' => ['title' => 'Challenges', 'phase' => 6, 'description' => 'Schedule daily, weekly and monthly challenges.'],
        'admin-achievements' => ['title' => 'Achievements', 'phase' => 6, 'description' => 'Manage achievements, XP rules and levels.'],
        'admin-analytics' => ['title' => 'Analytics', 'phase' => 6, 'description' => 'Platform usage, growth and learning outcomes.'],
        'admin-settings' => ['title' => 'Settings', 'phase' => 6, 'description' => 'Platform settings, advertising and plans.'],
    ];

    public function __invoke(string $feature): Response
    {
        $details = self::FEATURES[$feature] ?? throw new NotFoundHttpException;

        return Inertia::render('coming-soon', $details);
    }
}
