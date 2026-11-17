import LegalDraftNotice from '@/components/public/legal-draft-notice';
import ProsePage from '@/components/public/prose-page';

export default function Privacy({ consentAge }: { consentAge: number }) {
    return (
        <ProsePage
            title="Privacy Policy"
            description="How KeyMaster collects, uses and protects your information."
            lead={<LegalDraftNotice />}
        >
            <section>
                <h2>What we collect</h2>
                <ul>
                    <li>
                        Account details: your name, username, email address (not
                        needed for school-managed accounts), and date of birth,
                        which we store encrypted.
                    </li>
                    <li>
                        Practice data: your typing results, such as speed,
                        accuracy, time spent and the keys you find hardest.
                    </li>
                    <li>
                        Technical data: the IP address and browser used to sign
                        in, kept for security.
                    </li>
                </ul>
            </section>
            <section>
                <h2>Why we use it</h2>
                <ul>
                    <li>To run your account and save your progress.</li>
                    <li>
                        To show your results to you and, if you are in a class,
                        to your teacher.
                    </li>
                    <li>To keep leaderboards fair and the service secure.</li>
                </ul>
                <p>We do not sell personal information.</p>
            </section>
            <section>
                <h2>Children</h2>
                <p>
                    Users under {consentAge} need a parent or guardian to
                    approve their account by email, or an account created by
                    their school. Profiles of users under 18 are private and
                    hidden from public leaderboards by default. We do not show
                    advertising to users under 18.
                </p>
            </section>
            <section>
                <h2>Who can see your information</h2>
                <ul>
                    <li>You.</li>
                    <li>
                        Teachers of classes you join, who see your results for
                        that class.
                    </li>
                    <li>
                        KeyMaster administrators, when needed to run the
                        service. Their access to student records is logged.
                    </li>
                    <li>
                        Other users, only if you choose a public profile or
                        appear on a public leaderboard.
                    </li>
                </ul>
            </section>
            <section>
                <h2>How long we keep it</h2>
                <p>
                    Detailed keystroke records are deleted after 90 days.
                    Results and progress are kept while your account is open.
                    When you delete your account, we remove your personal
                    details straight away; anonymous results may be kept for
                    statistics.
                </p>
            </section>
            <section>
                <h2>Your choices</h2>
                <p>
                    You can update your details, make your profile private, hide
                    yourself from leaderboards or delete your account from your
                    settings at any time.
                </p>
            </section>
        </ProsePage>
    );
}
