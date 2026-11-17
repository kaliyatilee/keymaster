import { Form, Head } from '@inertiajs/react';
import { MailCheck } from 'lucide-react';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { logout } from '@/routes';
import { resend } from '@/routes/consent';

type Props = {
    guardianEmail: string | null;
    expired: boolean;
    status?: string;
};

export default function ConsentPending({
    guardianEmail,
    expired,
    status,
}: Props) {
    return (
        <PublicLayout>
            <Head title="Waiting for approval" />
            <section className="mx-auto max-w-xl px-4 py-16 sm:px-6">
                <div className="rounded-2xl border border-border bg-card p-8 text-center">
                    <MailCheck
                        className="mx-auto size-10 text-primary"
                        aria-hidden="true"
                    />
                    <h1 className="mt-4 text-2xl font-semibold tracking-tight">
                        Ask a parent or guardian to check their email
                    </h1>
                    <p className="mt-3 text-muted-foreground">
                        We sent a link to{' '}
                        {guardianEmail ?? 'your parent or guardian'}. Once they
                        approve, you can start saving your progress.
                    </p>
                    {expired && (
                        <p className="mt-3 text-sm text-typing-incorrect">
                            The last link has expired. Send a new one below.
                        </p>
                    )}
                    {status === 'consent-link-sent' && (
                        <p className="mt-3 text-sm font-medium text-success">
                            A new link has been sent.
                        </p>
                    )}
                    <div className="mt-8 flex flex-wrap justify-center gap-3">
                        <Form {...resend.form()}>
                            {({ processing }) => (
                                <Button
                                    type="submit"
                                    variant="outline"
                                    disabled={processing}
                                >
                                    Send the email again
                                </Button>
                            )}
                        </Form>
                        <Form {...logout.form()}>
                            {({ processing }) => (
                                <Button
                                    type="submit"
                                    variant="ghost"
                                    disabled={processing}
                                >
                                    Log out
                                </Button>
                            )}
                        </Form>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
