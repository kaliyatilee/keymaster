import { Head, Link } from '@inertiajs/react';
import { Clock } from 'lucide-react';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { register } from '@/routes';

type Props = {
    title: string;
    phase: number;
    description: string;
};

export default function Upcoming({ title, phase, description }: Props) {
    return (
        <PublicLayout>
            <Head title={title} />
            <section className="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6">
                <span className="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                    <Clock className="size-6" aria-hidden="true" />
                </span>
                <h1 className="mt-6 text-4xl font-semibold tracking-tight">
                    {title}
                </h1>
                <p className="mt-4 text-lg text-muted-foreground">
                    {description}
                </p>
                <p className="mt-6 rounded-full bg-muted px-4 py-1.5 text-sm text-muted-foreground">
                    Arriving in development phase {phase}
                </p>
                <Button asChild className="mt-8">
                    <Link href={register()}>Create your free account</Link>
                </Button>
            </section>
        </PublicLayout>
    );
}
