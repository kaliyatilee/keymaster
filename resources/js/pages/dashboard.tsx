import { Head, Link, usePage } from '@inertiajs/react';
import {
    Award,
    Flame,
    Gauge,
    Keyboard,
    Sparkles,
    Target,
    Timer,
} from 'lucide-react';
import { StatCard } from '@/components/dashboard/stat-card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { dashboard, practice } from '@/routes';

type Props = {
    stats: {
        level: { number: number; name: string } | null;
        xp: number;
        xp_for_next_level: number | null;
        next_level_name: string | null;
        best_wpm: number;
        average_wpm: number | null;
        average_accuracy: number | null;
        attempts: number;
        lessons_completed: number;
        practice_minutes: number;
    };
    dailyGoalMinutes: number;
    streak: number;
};

function greeting(): string {
    const hour = new Date().getHours();

    if (hour < 12) {
        return 'Good morning';
    }

    return hour < 18 ? 'Good afternoon' : 'Good evening';
}

export default function Dashboard({ stats, dailyGoalMinutes, streak }: Props) {
    const { auth } = usePage().props;
    const hasPractised = stats.attempts > 0;

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section className="flex flex-col gap-6 rounded-2xl border border-border bg-gradient-to-br from-primary/10 via-card to-card p-6 md:flex-row md:items-center">
                    <div className="flex-1">
                        <p className="text-sm text-muted-foreground">
                            {greeting()},
                        </p>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            {auth.user.display_name}
                        </h1>
                        <p className="mt-2 text-sm text-muted-foreground">
                            {hasPractised
                                ? 'Keep your streak going with a short session today.'
                                : 'Your first lesson takes about five minutes. Ready when you are.'}
                        </p>
                    </div>
                    <div className="w-full max-w-sm rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                        <div className="flex items-center justify-between text-sm">
                            <span className="font-medium">
                                {stats.level
                                    ? `Level ${stats.level.number} · ${stats.level.name}`
                                    : 'Level 1'}
                            </span>
                            <span className="flex items-center gap-1 text-xp-foreground dark:text-xp">
                                <Sparkles
                                    className="size-4"
                                    aria-hidden="true"
                                />
                                {stats.xp.toLocaleString()} XP
                            </span>
                        </div>
                        {stats.xp_for_next_level !== null && (
                            <>
                                <Progress
                                    className="mt-3"
                                    barClassName="bg-xp"
                                    value={stats.xp}
                                    max={stats.xp_for_next_level}
                                    label="Progress to next level"
                                />
                                <p className="mt-2 text-xs text-muted-foreground">
                                    {(
                                        stats.xp_for_next_level - stats.xp
                                    ).toLocaleString()}{' '}
                                    XP to {stats.next_level_name}
                                </p>
                            </>
                        )}
                    </div>
                </section>

                <section
                    aria-label="Your statistics"
                    className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                >
                    <StatCard
                        icon={Flame}
                        tone="xp"
                        label="Current streak"
                        value={`${streak} ${streak === 1 ? 'day' : 'days'}`}
                    />
                    <StatCard
                        icon={Gauge}
                        label="Best speed"
                        value={hasPractised ? `${stats.best_wpm} WPM` : '—'}
                    />
                    <StatCard
                        icon={Target}
                        tone="success"
                        label="Average accuracy"
                        value={
                            stats.average_accuracy !== null
                                ? `${stats.average_accuracy}%`
                                : '—'
                        }
                    />
                    <StatCard
                        icon={Timer}
                        label="Time practised"
                        value={`${stats.practice_minutes} min`}
                        hint={`${stats.lessons_completed} lessons completed`}
                    />
                </section>

                <section className="grid gap-4 lg:grid-cols-3">
                    <div className="rounded-2xl border border-border bg-card p-6 lg:col-span-2">
                        <h2 className="flex items-center gap-2 font-semibold">
                            <Keyboard
                                className="size-5 text-primary"
                                aria-hidden="true"
                            />
                            Recommended next
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Lessons, typing tests and your practice history
                            arrive in the next release. Your daily goal is{' '}
                            {dailyGoalMinutes} minutes.
                        </p>
                        <Button asChild className="mt-4">
                            <Link href={practice()}>Go to practice</Link>
                        </Button>
                    </div>
                    <div className="rounded-2xl border border-border bg-card p-6">
                        <h2 className="flex items-center gap-2 font-semibold">
                            <Award
                                className="size-5 text-xp"
                                aria-hidden="true"
                            />
                            Achievements
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Badges such as First Lesson, First 20 WPM and
                            Seven-Day Streak will appear here once you start
                            practising.
                        </p>
                    </div>
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
