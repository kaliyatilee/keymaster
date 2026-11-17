import { Head } from '@inertiajs/react';
import { ClipboardList, School, Users } from 'lucide-react';
import { dashboard } from '@/routes/teacher';

const upcoming = [
    {
        icon: School,
        title: 'Create a class',
        text: 'Share a join code, or create accounts for younger students that need no email.',
    },
    {
        icon: ClipboardList,
        title: 'Set assignments',
        text: 'Choose lessons or your own text, with a target speed, minimum accuracy and deadline.',
    },
    {
        icon: Users,
        title: 'Track progress',
        text: 'See class averages, improvement over time and who needs extra practice.',
    },
];

export default function TeacherDashboard({
    displayName,
}: {
    displayName: string;
}) {
    return (
        <>
            <Head title="Teacher dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Welcome, {displayName}
                    </h1>
                    <p className="mt-1 text-muted-foreground">
                        Your teacher account is active. Classroom tools arrive
                        in phase 5; here is what they will cover.
                    </p>
                </div>
                <div className="grid gap-4 md:grid-cols-3">
                    {upcoming.map((item) => (
                        <div
                            key={item.title}
                            className="rounded-2xl border border-border bg-card p-6"
                        >
                            <item.icon
                                className="size-6 text-primary"
                                aria-hidden="true"
                            />
                            <h2 className="mt-4 font-semibold">{item.title}</h2>
                            <p className="mt-2 text-sm text-muted-foreground">
                                {item.text}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

TeacherDashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
