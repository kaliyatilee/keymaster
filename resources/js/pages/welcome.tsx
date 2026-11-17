import { Head, Link } from '@inertiajs/react';
import {
    Award,
    BarChart3,
    CalendarCheck,
    ChevronDown,
    Flame,
    GraduationCap,
    Hand,
    Keyboard,
    School,
    ShieldCheck,
    Target,
    Trophy,
    Users,
} from 'lucide-react';
import TypingPreview from '@/components/public/typing-preview';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { register } from '@/routes';
import { courses, typingTest } from '@/routes/pages';

const steps = [
    {
        icon: Hand,
        title: 'Learn the home row',
        text: 'Short guided lessons show which finger presses which key, starting with the eight home keys.',
    },
    {
        icon: Keyboard,
        title: 'Practise a little every day',
        text: 'Set a daily goal of a few minutes. Each lesson adds new keys, then words, sentences and numbers.',
    },
    {
        icon: BarChart3,
        title: 'Watch your progress',
        text: 'Every session records your speed and accuracy, so you can see yourself improving week by week.',
    },
];

const courseList = [
    {
        level: 'Course 1',
        title: 'Keyboard Fundamentals',
        text: 'Posture, finger placement and the home row.',
    },
    {
        level: 'Course 2',
        title: 'Beginner Typing',
        text: 'Top and bottom rows, then your first full words.',
    },
    {
        level: 'Course 3',
        title: 'Intermediate Typing',
        text: 'Sentences, capitals, punctuation and numbers.',
    },
    {
        level: 'Course 4',
        title: 'Advanced Typing',
        text: 'Long passages, symbols and speed with accuracy.',
    },
];

const features = [
    {
        icon: Target,
        title: 'Accurate scoring',
        text: 'Speed and accuracy are checked on our server, so every result on a leaderboard is genuine.',
    },
    {
        icon: Award,
        title: 'XP, levels and badges',
        text: 'Earn experience for real practice and unlock achievements as you reach new milestones.',
    },
    {
        icon: Flame,
        title: 'Daily streaks',
        text: 'Keep your streak alive by meeting your daily goal. Your day follows your own time zone.',
    },
    {
        icon: CalendarCheck,
        title: 'Daily challenges',
        text: 'A new challenge every day, with the same text and rules for everyone taking part.',
    },
    {
        icon: Trophy,
        title: 'Leaderboards',
        text: 'Daily, weekly, monthly and all-time rankings, plus private boards for each class.',
    },
    {
        icon: ShieldCheck,
        title: 'Safe for young learners',
        text: 'Parental approval for children, private profiles by default, and no ads for under-18s.',
    },
];

const measures = [
    {
        name: 'Words per minute (WPM)',
        text: 'Correct characters typed, divided by five, divided by the minutes you spent typing.',
    },
    {
        name: 'Accuracy',
        text: 'The share of keys you pressed correctly the first time. Fixing a mistake still counts it.',
    },
    {
        name: 'Practice time and streak',
        text: 'Minutes practised each day, and how many days in a row you met your goal.',
    },
];

const rankingRules = [
    'Only results our server has checked are ranked.',
    'Higher words per minute ranks first.',
    'If tied, higher accuracy wins.',
    'If still tied, whoever reached it first wins.',
];

const classroom = [
    {
        icon: School,
        title: 'Classes with join codes',
        text: 'Create a class and share a short code. Younger students can get accounts that need no email.',
    },
    {
        icon: GraduationCap,
        title: 'Assignments with targets',
        text: 'Set lessons or your own texts with a target speed, minimum accuracy and a deadline.',
    },
    {
        icon: Users,
        title: 'See who needs help',
        text: 'Class averages, progress over time and a list of students who need extra practice.',
    },
];

const faqs = [
    {
        q: 'Is KeyMaster free?',
        a: 'Yes. Lessons, practice and the typing test are free. We plan optional paid features for schools later, and the core learning will stay free.',
    },
    {
        q: 'How is typing speed calculated?',
        a: 'We count correct characters, divide by five (the standard length of a "word"), and divide by the minutes you actually spent typing. Accuracy is correct keystrokes divided by all keystrokes.',
    },
    {
        q: 'Can children use KeyMaster?',
        a: 'Yes. Children under the consent age need a parent or guardian to approve their account by email, or they can use an account created by their school. Profiles of under-18s stay private and they see no ads.',
    },
    {
        q: 'Do I need to install anything?',
        a: 'No. KeyMaster runs in your web browser on a computer, laptop or tablet with a keyboard.',
    },
    {
        q: 'How do teachers get access?',
        a: 'Sign up and tick "I am a teacher". After you verify your email, our team approves teacher access, usually within one working day.',
    },
];

export default function Welcome() {
    return (
        <PublicLayout>
            <Head title="Learn to type faster and more accurately">
                <meta
                    name="description"
                    content="KeyMaster teaches keyboard skills with step-by-step typing courses, daily practice, challenges and leaderboards, for individuals and classrooms."
                />
            </Head>

            {/* Hero */}
            <section className="relative overflow-hidden">
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-x-0 -top-40 -z-10 h-96 bg-gradient-to-b from-primary/10 to-transparent"
                />
                <div className="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-2">
                    <div>
                        <p className="inline-flex items-center gap-2 rounded-full bg-accent px-3 py-1 text-xs font-medium text-accent-foreground">
                            <Keyboard className="size-3.5" aria-hidden="true" />
                            Keyboard skills for everyone
                        </p>
                        <h1 className="mt-5 text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                            Learn to type faster, with fewer mistakes.
                        </h1>
                        <p className="mt-5 max-w-xl text-lg text-muted-foreground">
                            Step-by-step lessons take you from the home row to
                            full paragraphs. Daily goals, streaks and challenges
                            keep you coming back, and your speed and accuracy
                            show how far you have come.
                        </p>
                        <div className="mt-8 flex flex-wrap gap-3">
                            <Button asChild size="lg">
                                <Link href={register()}>Start practising</Link>
                            </Button>
                            <Button asChild size="lg" variant="outline">
                                <Link href={typingTest()}>
                                    Take a typing test
                                </Link>
                            </Button>
                        </div>
                        <p className="mt-4 text-sm text-muted-foreground">
                            Free to use. Works in your browser.
                        </p>
                    </div>
                    <TypingPreview />
                </div>
            </section>

            {/* How it works */}
            <section
                aria-labelledby="how-heading"
                className="border-y border-border/60 bg-muted/30"
            >
                <div className="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <h2
                        id="how-heading"
                        className="text-3xl font-semibold tracking-tight"
                    >
                        How it works
                    </h2>
                    <ol className="mt-10 grid gap-6 md:grid-cols-3">
                        {steps.map((step, index) => (
                            <li
                                key={step.title}
                                className="rounded-2xl border border-border bg-card p-6"
                            >
                                <div className="flex items-center gap-3">
                                    <span className="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                        <step.icon
                                            className="size-5"
                                            aria-hidden="true"
                                        />
                                    </span>
                                    <span className="text-sm font-medium text-muted-foreground">
                                        Step {index + 1}
                                    </span>
                                </div>
                                <h3 className="mt-4 text-lg font-semibold">
                                    {step.title}
                                </h3>
                                <p className="mt-2 text-sm text-muted-foreground">
                                    {step.text}
                                </p>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            {/* Courses */}
            <section
                aria-labelledby="courses-heading"
                className="mx-auto max-w-6xl px-4 py-16 sm:px-6"
            >
                <div className="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2
                            id="courses-heading"
                            className="text-3xl font-semibold tracking-tight"
                        >
                            Typing courses
                        </h2>
                        <p className="mt-2 text-muted-foreground">
                            Four courses, from your first keys to confident
                            speed.
                        </p>
                    </div>
                    <Button asChild variant="outline">
                        <Link href={courses()}>See all courses</Link>
                    </Button>
                </div>
                <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {courseList.map((course) => (
                        <article
                            key={course.title}
                            className="rounded-2xl border border-border bg-card p-6 transition-shadow hover:shadow-md"
                        >
                            <p className="text-xs font-semibold tracking-wide text-primary uppercase">
                                {course.level}
                            </p>
                            <h3 className="mt-2 font-semibold">
                                {course.title}
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                {course.text}
                            </p>
                        </article>
                    ))}
                </div>
            </section>

            {/* Features */}
            <section
                aria-labelledby="features-heading"
                className="border-y border-border/60 bg-muted/30"
            >
                <div className="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <h2
                        id="features-heading"
                        className="text-3xl font-semibold tracking-tight"
                    >
                        Built to keep you practising
                    </h2>
                    <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {features.map((feature) => (
                            <div key={feature.title} className="flex gap-4">
                                <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-card text-primary shadow-sm ring-1 ring-border">
                                    <feature.icon
                                        className="size-5"
                                        aria-hidden="true"
                                    />
                                </span>
                                <div>
                                    <h3 className="font-semibold">
                                        {feature.title}
                                    </h3>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        {feature.text}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Statistics + leaderboard preview */}
            <section className="mx-auto grid max-w-6xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-2">
                <div
                    aria-labelledby="stats-heading"
                    className="rounded-2xl border border-border bg-card p-8"
                >
                    <h2
                        id="stats-heading"
                        className="text-2xl font-semibold tracking-tight"
                    >
                        What we measure
                    </h2>
                    <p className="mt-2 text-sm text-muted-foreground">
                        Clear numbers you can compare from one week to the next.
                    </p>
                    <dl className="mt-6 space-y-5">
                        {measures.map((measure) => (
                            <div key={measure.name}>
                                <dt className="font-medium">{measure.name}</dt>
                                <dd className="mt-1 text-sm text-muted-foreground">
                                    {measure.text}
                                </dd>
                            </div>
                        ))}
                    </dl>
                </div>
                <div
                    aria-labelledby="leaderboard-heading"
                    className="rounded-2xl border border-border bg-card p-8"
                >
                    <h2
                        id="leaderboard-heading"
                        className="flex items-center gap-2 text-2xl font-semibold tracking-tight"
                    >
                        <Trophy className="size-6 text-xp" aria-hidden="true" />
                        Fair leaderboards
                    </h2>
                    <p className="mt-2 text-sm text-muted-foreground">
                        Daily, weekly, monthly, all-time and class rankings
                        follow the same rules:
                    </p>
                    <ol className="mt-6 space-y-3">
                        {rankingRules.map((rule, index) => (
                            <li key={rule} className="flex gap-3 text-sm">
                                <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                    {index + 1}
                                </span>
                                {rule}
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            {/* Classroom */}
            <section
                aria-labelledby="classroom-heading"
                className="bg-primary text-primary-foreground"
            >
                <div className="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <h2
                        id="classroom-heading"
                        className="text-3xl font-semibold tracking-tight"
                    >
                        Made for classrooms too
                    </h2>
                    <p className="mt-2 max-w-2xl text-primary-foreground/80">
                        Teachers can run typing lessons for a whole class and
                        see each student's progress in one place.
                    </p>
                    <div className="mt-10 grid gap-6 md:grid-cols-3">
                        {classroom.map((item) => (
                            <div
                                key={item.title}
                                className="rounded-2xl bg-primary-foreground/10 p-6"
                            >
                                <item.icon
                                    className="size-6"
                                    aria-hidden="true"
                                />
                                <h3 className="mt-4 font-semibold">
                                    {item.title}
                                </h3>
                                <p className="mt-2 text-sm text-primary-foreground/80">
                                    {item.text}
                                </p>
                            </div>
                        ))}
                    </div>
                    <Button asChild variant="secondary" className="mt-10">
                        <Link href={register()}>Sign up as a teacher</Link>
                    </Button>
                </div>
            </section>

            {/* FAQ */}
            <section
                aria-labelledby="faq-heading"
                className="mx-auto max-w-3xl px-4 py-16 sm:px-6"
            >
                <h2
                    id="faq-heading"
                    className="text-3xl font-semibold tracking-tight"
                >
                    Questions and answers
                </h2>
                <div className="mt-8 divide-y divide-border rounded-2xl border border-border bg-card">
                    {faqs.map((faq) => (
                        <details key={faq.q} className="group p-5">
                            <summary className="flex cursor-pointer list-none items-center justify-between gap-4 font-medium">
                                {faq.q}
                                <ChevronDown
                                    className="size-4 shrink-0 text-muted-foreground transition-transform group-open:rotate-180"
                                    aria-hidden="true"
                                />
                            </summary>
                            <p className="mt-3 text-sm text-muted-foreground">
                                {faq.a}
                            </p>
                        </details>
                    ))}
                </div>
                <div className="mt-12 text-center">
                    <Button asChild size="lg">
                        <Link href={register()}>Start practising for free</Link>
                    </Button>
                </div>
            </section>
        </PublicLayout>
    );
}
