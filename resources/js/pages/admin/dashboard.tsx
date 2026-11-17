import { Head, Link } from '@inertiajs/react';
import {
    GraduationCap,
    Hourglass,
    Shield,
    UserCheck,
    Users,
} from 'lucide-react';
import { StatCard } from '@/components/dashboard/stat-card';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { index as teacherRequests } from '@/routes/admin/teacher-requests';

type Props = {
    counts: {
        users: number;
        students: number;
        teachers: number;
        admins: number;
        pending_consent: number;
        teacher_requests: number;
    };
    recentActivity: {
        id: number;
        action: string;
        actor: string | null;
        subject: string | null;
        at: string;
    }[];
};

export default function AdminDashboard({ counts, recentActivity }: Props) {
    return (
        <>
            <Head title="Admin overview" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <h1 className="text-2xl font-semibold tracking-tight">
                    Platform overview
                </h1>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <StatCard
                        icon={Users}
                        label="Users"
                        value={counts.users.toLocaleString()}
                        hint={`${counts.students} students`}
                    />
                    <StatCard
                        icon={GraduationCap}
                        label="Teachers"
                        value={counts.teachers.toLocaleString()}
                    />
                    <StatCard
                        icon={Shield}
                        label="Administrators"
                        value={counts.admins.toLocaleString()}
                    />
                    <StatCard
                        icon={Hourglass}
                        label="Awaiting parental consent"
                        value={counts.pending_consent.toLocaleString()}
                    />
                </section>

                <section className="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-border bg-card p-6">
                    <div className="flex items-center gap-3">
                        <UserCheck
                            className="size-6 text-primary"
                            aria-hidden="true"
                        />
                        <div>
                            <h2 className="font-semibold">Teacher requests</h2>
                            <p className="text-sm text-muted-foreground">
                                {counts.teacher_requests === 0
                                    ? 'No requests waiting.'
                                    : `${counts.teacher_requests} waiting for review.`}
                            </p>
                        </div>
                    </div>
                    <Button
                        asChild
                        variant={
                            counts.teacher_requests > 0 ? 'default' : 'outline'
                        }
                    >
                        <Link href={teacherRequests()}>Review requests</Link>
                    </Button>
                </section>

                <section className="rounded-2xl border border-border bg-card">
                    <h2 className="border-b border-border px-6 py-4 font-semibold">
                        Recent activity
                    </h2>
                    {recentActivity.length === 0 ? (
                        <p className="px-6 py-8 text-sm text-muted-foreground">
                            No audited activity yet.
                        </p>
                    ) : (
                        <ul className="divide-y divide-border">
                            {recentActivity.map((entry) => (
                                <li
                                    key={entry.id}
                                    className="flex flex-wrap items-center justify-between gap-2 px-6 py-3 text-sm"
                                >
                                    <span>
                                        <span className="font-mono text-xs">
                                            {entry.action}
                                        </span>
                                        {entry.subject && (
                                            <span className="text-muted-foreground">
                                                {' '}
                                                · {entry.subject}
                                            </span>
                                        )}
                                    </span>
                                    <span className="text-muted-foreground">
                                        {entry.actor ?? 'system'} ·{' '}
                                        {new Date(entry.at).toLocaleString()}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </div>
        </>
    );
}

AdminDashboard.layout = {
    breadcrumbs: [{ title: 'Admin', href: dashboard() }],
};
