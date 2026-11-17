import { Form, Head } from '@inertiajs/react';
import { CheckCircle2, ShieldCheck, XCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { respond } from '@/routes/consent';

type Props = {
    token: string;
    open: boolean;
    status: 'pending' | 'granted' | 'declined' | 'revoked';
    child: { name: string; username: string };
    expiresAt: string;
};

export default function ConsentShow({
    token,
    open,
    status,
    child,
    expiresAt,
}: Props) {
    return (
        <PublicLayout>
            <Head title="Parental consent" />
            <section className="mx-auto max-w-xl px-4 py-16 sm:px-6">
                <div className="rounded-2xl border border-border bg-card p-8">
                    <ShieldCheck
                        className="size-8 text-primary"
                        aria-hidden="true"
                    />
                    <h1 className="mt-4 text-2xl font-semibold tracking-tight">
                        Approve {child.name}'s account
                    </h1>

                    {open ? (
                        <>
                            <p className="mt-3 text-muted-foreground">
                                {child.name} (username{' '}
                                <strong>{child.username}</strong>) has signed up
                                for KeyMaster, a website for learning to type,
                                and gave your email as their parent or guardian.
                            </p>
                            <ul className="mt-5 space-y-2 text-sm text-muted-foreground">
                                <li>
                                    · We store their name, username, date of
                                    birth and typing results.
                                </li>
                                <li>
                                    · Their profile stays private and they see
                                    no advertising.
                                </li>
                                <li>
                                    · You can ask us to delete the account at
                                    any time.
                                </li>
                            </ul>
                            <div className="mt-8 flex flex-wrap gap-3">
                                <Form {...respond.form(token)}>
                                    {({ processing }) => (
                                        <>
                                            <input
                                                type="hidden"
                                                name="decision"
                                                value="approve"
                                            />
                                            <Button
                                                type="submit"
                                                disabled={processing}
                                            >
                                                Approve account
                                            </Button>
                                        </>
                                    )}
                                </Form>
                                <Form {...respond.form(token)}>
                                    {({ processing }) => (
                                        <>
                                            <input
                                                type="hidden"
                                                name="decision"
                                                value="decline"
                                            />
                                            <Button
                                                type="submit"
                                                variant="outline"
                                                disabled={processing}
                                            >
                                                Decline
                                            </Button>
                                        </>
                                    )}
                                </Form>
                            </div>
                            <p className="mt-6 text-xs text-muted-foreground">
                                This link expires on{' '}
                                {new Date(expiresAt).toLocaleDateString()}.
                            </p>
                        </>
                    ) : status === 'granted' ? (
                        <p className="mt-4 flex items-start gap-2 text-success">
                            <CheckCircle2
                                className="mt-0.5 size-5 shrink-0"
                                aria-hidden="true"
                            />
                            Thank you. The account is approved and {child.name}{' '}
                            can start practising.
                        </p>
                    ) : status === 'declined' ? (
                        <p className="mt-4 flex items-start gap-2 text-muted-foreground">
                            <XCircle
                                className="mt-0.5 size-5 shrink-0"
                                aria-hidden="true"
                            />
                            You declined this request. The account stays
                            inactive.
                        </p>
                    ) : (
                        <p className="mt-4 text-muted-foreground">
                            This link has expired or been replaced by a newer
                            one. Please use the most recent email we sent you.
                        </p>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}
