import { Form, Head } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { approve, index, reject } from '@/routes/admin/teacher-requests';

type Request = {
    id: number;
    username: string;
    name: string;
    email: string | null;
    email_verified: boolean;
    requested_at: string | null;
};

export default function TeacherRequests({ requests }: { requests: Request[] }) {
    return (
        <>
            <Head title="Teacher requests" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Teacher requests
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Approve only people you can confirm are teachers.
                        Teachers can invite children into classes.
                    </p>
                </div>

                {requests.length === 0 ? (
                    <p className="rounded-2xl border border-dashed border-border p-10 text-center text-sm text-muted-foreground">
                        No teacher requests are waiting.
                    </p>
                ) : (
                    <ul className="divide-y divide-border rounded-2xl border border-border bg-card">
                        {requests.map((request) => (
                            <li
                                key={request.id}
                                className="flex flex-wrap items-center gap-4 p-5"
                            >
                                <div className="min-w-0 flex-1">
                                    <p className="font-medium">
                                        {request.name}{' '}
                                        <span className="text-muted-foreground">
                                            @{request.username}
                                        </span>
                                    </p>
                                    <p className="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                                        {request.email}
                                        {request.email_verified ? (
                                            <Badge variant="secondary">
                                                Email verified
                                            </Badge>
                                        ) : (
                                            <Badge variant="outline">
                                                Email not verified
                                            </Badge>
                                        )}
                                        {request.requested_at && (
                                            <span>
                                                · requested{' '}
                                                {new Date(
                                                    request.requested_at,
                                                ).toLocaleDateString()}
                                            </span>
                                        )}
                                    </p>
                                </div>
                                <div className="flex gap-2">
                                    <Form
                                        {...reject.form(request.id)}
                                        options={{ preserveScroll: true }}
                                    >
                                        {({ processing }) => (
                                            <Button
                                                type="submit"
                                                variant="outline"
                                                disabled={processing}
                                            >
                                                Decline
                                            </Button>
                                        )}
                                    </Form>
                                    <Form
                                        {...approve.form(request.id)}
                                        options={{ preserveScroll: true }}
                                    >
                                        {({ processing }) => (
                                            <Button
                                                type="submit"
                                                disabled={
                                                    processing ||
                                                    !request.email_verified
                                                }
                                            >
                                                Approve
                                            </Button>
                                        )}
                                    </Form>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </>
    );
}

TeacherRequests.layout = {
    breadcrumbs: [
        { title: 'Admin', href: dashboard() },
        { title: 'Teacher requests', href: index() },
    ],
};
