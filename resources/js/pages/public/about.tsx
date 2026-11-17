import ProsePage from '@/components/public/prose-page';

export default function About() {
    return (
        <ProsePage
            title="About KeyMaster"
            description="KeyMaster helps people of all ages learn the keyboard and type faster and more accurately."
            lead="KeyMaster helps people of all ages learn the keyboard properly, at home or at school."
        >
            <section>
                <h2>Why typing still matters</h2>
                <p>
                    Typing is how most of us write, study and work. Touch
                    typing, using all ten fingers without looking at the keys,
                    frees your attention for what you are writing rather than
                    where the letters are.
                </p>
            </section>
            <section>
                <h2>How we teach</h2>
                <p>
                    Lessons build up one row of keys at a time, then move to
                    words, sentences, numbers and punctuation. Short daily
                    practice works better than long, rare sessions, so KeyMaster
                    is built around daily goals, streaks and challenges.
                </p>
            </section>
            <section>
                <h2>For schools</h2>
                <p>
                    Teachers can create classes, set assignments with clear
                    targets, and see which students need extra help. Schools can
                    create accounts for younger students that do not need an
                    email address.
                </p>
            </section>
            <section>
                <h2>Fair and safe</h2>
                <ul>
                    <li>
                        Every result is checked on our server before it counts
                        towards XP or a leaderboard.
                    </li>
                    <li>
                        Children need a parent's or school's approval, and their
                        profiles stay private.
                    </li>
                    <li>
                        We never show ads during typing, and never to users
                        under 18.
                    </li>
                </ul>
            </section>
        </ProsePage>
    );
}
