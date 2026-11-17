import { Head } from '@inertiajs/react';
import { Construction } from 'lucide-react';

type Props = {
    title: string;
    phase: number;
    description: string;
};

/**
 * Shown for areas scheduled in a later phase, so navigation works end to
 * end without pretending a feature exists.
 */
export default function ComingSoon({ title, phase, description }: Props) {
    return (
        <>
            <Head title={title} />
            <div className="flex flex-1 items-center justify-center p-6">
                <div className="max-w-md text-center">
                    <span className="mx-auto flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                        <Construction className="size-6" aria-hidden="true" />
                    </span>
                    <h1 className="mt-5 text-2xl font-semibold tracking-tight">
                        {title}
                    </h1>
                    <p className="mt-3 text-muted-foreground">{description}</p>
                    <p className="mt-5 inline-block rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">
                        Coming in phase {phase}
                    </p>
                </div>
            </div>
        </>
    );
}

ComingSoon.layout = {
    breadcrumbs: [],
};
