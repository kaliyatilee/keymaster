import LegalDraftNotice from '@/components/public/legal-draft-notice';
import ProsePage from '@/components/public/prose-page';

export default function Terms() {
    return (
        <ProsePage
            title="Terms and Conditions"
            description="The rules for using KeyMaster."
            lead={<LegalDraftNotice />}
        >
            <section>
                <h2>Using KeyMaster</h2>
                <p>
                    KeyMaster is a website for learning and practising typing.
                    By creating an account you agree to use it fairly and to
                    keep your password private.
                </p>
            </section>
            <section>
                <h2>Fair play</h2>
                <ul>
                    <li>
                        Do not use scripts, macros, copy and paste or any tool
                        that types for you.
                    </li>
                    <li>
                        Results that look automated may be removed from
                        leaderboards and reviewed by our team.
                    </li>
                    <li>One person, one account.</li>
                </ul>
            </section>
            <section>
                <h2>Respect others</h2>
                <p>
                    Usernames, display names and any text you create must not be
                    offensive, impersonate others or share personal information.
                </p>
            </section>
            <section>
                <h2>Teachers and schools</h2>
                <p>
                    Teachers may only add students they are responsible for, and
                    must use students' results only for teaching.
                </p>
            </section>
            <section>
                <h2>Changes and closure</h2>
                <p>
                    We may update these terms and will tell you about important
                    changes. We may suspend accounts that break these rules. You
                    can delete your account at any time.
                </p>
            </section>
        </ProsePage>
    );
}
