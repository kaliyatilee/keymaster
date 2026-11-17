import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { dashboard, home, login, register } from '@/routes';
import {
    about,
    courses,
    leaderboard,
    privacy,
    terms,
    typingTest,
} from '@/routes/pages';

const links = [
    { title: 'Courses', href: courses() },
    { title: 'Typing Test', href: typingTest() },
    { title: 'Leaderboard', href: leaderboard() },
    { title: 'About', href: about() },
];

export default function PublicLayout({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    const { auth, name } = usePage().props;

    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <a
                href="#main"
                className="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:rounded-md focus:bg-primary focus:px-3 focus:py-2 focus:text-primary-foreground"
            >
                Skip to content
            </a>
            <header className="sticky top-0 z-40 border-b border-border/60 bg-background/85 backdrop-blur">
                <nav
                    aria-label="Main"
                    className="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4 sm:px-6"
                >
                    <Link
                        href={home()}
                        className="flex items-center gap-2 font-semibold"
                    >
                        <span className="flex size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                            <AppLogoIcon className="size-5 fill-current" />
                        </span>
                        <span>{name}</span>
                    </Link>
                    <ul className="hidden items-center gap-1 text-sm md:flex">
                        {links.map((link) => (
                            <li key={link.title}>
                                <Link
                                    href={link.href}
                                    className="rounded-md px-3 py-2 text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    {link.title}
                                </Link>
                            </li>
                        ))}
                    </ul>
                    <div className="ml-auto flex items-center gap-2">
                        {auth.user ? (
                            <Button asChild>
                                <Link href={dashboard()}>Go to dashboard</Link>
                            </Button>
                        ) : (
                            <>
                                <Button asChild variant="ghost">
                                    <Link href={login()}>Log in</Link>
                                </Button>
                                <Button asChild>
                                    <Link href={register()}>Sign up free</Link>
                                </Button>
                            </>
                        )}
                    </div>
                </nav>
            </header>

            <main id="main" className={cn('flex-1', className)}>
                {children}
            </main>

            <footer className="border-t border-border/60 bg-muted/40">
                <div className="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4">
                    <div className="md:col-span-2">
                        <div className="flex items-center gap-2 font-semibold">
                            <span className="flex size-7 items-center justify-center rounded-md bg-primary text-primary-foreground">
                                <AppLogoIcon className="size-4 fill-current" />
                            </span>
                            {name}
                        </div>
                        <p className="mt-3 max-w-sm text-sm text-muted-foreground">
                            Learn the keyboard step by step, build a daily
                            habit, and track real progress in speed and
                            accuracy, at home or in class.
                        </p>
                    </div>
                    <div>
                        <h2 className="text-sm font-semibold">Learn</h2>
                        <ul className="mt-3 space-y-2 text-sm text-muted-foreground">
                            {links.map((link) => (
                                <li key={link.title}>
                                    <Link
                                        href={link.href}
                                        className="hover:text-foreground"
                                    >
                                        {link.title}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <h2 className="text-sm font-semibold">Legal</h2>
                        <ul className="mt-3 space-y-2 text-sm text-muted-foreground">
                            <li>
                                <Link
                                    href={privacy()}
                                    className="hover:text-foreground"
                                >
                                    Privacy Policy
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={terms()}
                                    className="hover:text-foreground"
                                >
                                    Terms and Conditions
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="border-t border-border/60 py-6 text-center text-xs text-muted-foreground">
                    © {new Date().getFullYear()} {name}. All rights reserved.
                </div>
            </footer>
        </div>
    );
}
