import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';
import PublicLayout from '@/layouts/public-layout';

export default function ProsePage({
    title,
    description,
    lead,
    children,
}: {
    title: string;
    description: string;
    lead?: ReactNode;
    children: ReactNode;
}) {
    return (
        <PublicLayout>
            <Head title={title}>
                <meta name="description" content={description} />
            </Head>
            <article className="mx-auto max-w-3xl px-4 py-16 sm:px-6">
                <h1 className="text-4xl font-semibold tracking-tight">
                    {title}
                </h1>
                {lead && (
                    <div className="mt-4 text-lg text-muted-foreground">
                        {lead}
                    </div>
                )}
                <div className="mt-10 space-y-8 text-[15px] leading-7 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_li]:ml-5 [&_li]:list-disc [&_p+p]:mt-3 [&_ul]:mt-3 [&_ul]:space-y-1">
                    {children}
                </div>
            </article>
        </PublicLayout>
    );
}
