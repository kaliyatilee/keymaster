import { Form, Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { ageOn } from '@/lib/age';
import { login } from '@/routes';
import { privacy, terms } from '@/routes/pages';
import { store } from '@/routes/register';

type Props = {
    passwordRules: string;
    consentAge: number;
};

export default function Register({ passwordRules, consentAge }: Props) {
    const [dateOfBirth, setDateOfBirth] = useState('');
    const timezone = useMemo(
        () => Intl.DateTimeFormat().resolvedOptions().timeZone,
        [],
    );
    const age = ageOn(dateOfBirth, new Date());
    // The server makes the final decision; this only decides which fields to show.
    const needsGuardian = age !== null && age < consentAge;
    const isAdult = age !== null && age >= 18;
    const today = new Date().toISOString().slice(0, 10);

    return (
        <>
            <Head title="Register" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <input type="hidden" name="timezone" value={timezone} />
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Your name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    autoComplete="name"
                                    name="name"
                                    placeholder="First and last name"
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <Input
                                    id="username"
                                    type="text"
                                    required
                                    autoComplete="username"
                                    name="username"
                                    placeholder="e.g. fast_fingers"
                                    aria-describedby="username-hint"
                                    pattern="[A-Za-z0-9_]{3,30}"
                                />
                                <p
                                    id="username-hint"
                                    className="text-xs text-muted-foreground"
                                >
                                    3–30 letters, numbers or underscores. Shown
                                    on leaderboards, so don't use your full
                                    name.
                                </p>
                                <InputError message={errors.username} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="date_of_birth">
                                    Date of birth
                                </Label>
                                <Input
                                    id="date_of_birth"
                                    type="date"
                                    required
                                    name="date_of_birth"
                                    max={today}
                                    value={dateOfBirth}
                                    onChange={(event) =>
                                        setDateOfBirth(event.target.value)
                                    }
                                    aria-describedby="dob-hint"
                                />
                                <p
                                    id="dob-hint"
                                    className="text-xs text-muted-foreground"
                                >
                                    We use this to keep younger learners safe.
                                    It is stored encrypted and never shown to
                                    others.
                                </p>
                                <InputError message={errors.date_of_birth} />
                            </div>

                            {needsGuardian && (
                                <div className="grid gap-2 rounded-xl border border-primary/30 bg-primary/5 p-4">
                                    <Label htmlFor="guardian_email">
                                        Parent or guardian's email
                                    </Label>
                                    <p className="text-xs text-muted-foreground">
                                        Because you are under {consentAge}, a
                                        parent or guardian needs to approve your
                                        account. We'll email them a link.
                                    </p>
                                    <Input
                                        id="guardian_email"
                                        type="email"
                                        required
                                        name="guardian_email"
                                        placeholder="parent@example.com"
                                    />
                                    <InputError
                                        message={errors.guardian_email}
                                    />
                                </div>
                            )}
                            {!needsGuardian && errors.guardian_email && (
                                <InputError message={errors.guardian_email} />
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <PasswordInput
                                    id="password"
                                    required
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                    passwordrules={passwordRules}
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirm password
                                </Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                    passwordrules={passwordRules}
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            {isAdult && (
                                <div className="flex items-start gap-3">
                                    <Checkbox
                                        id="wants_teacher_access"
                                        name="wants_teacher_access"
                                        value="1"
                                    />
                                    <div className="grid gap-1">
                                        <Label htmlFor="wants_teacher_access">
                                            I am a teacher
                                        </Label>
                                        <p className="text-xs text-muted-foreground">
                                            We'll review your request after you
                                            verify your email.
                                        </p>
                                    </div>
                                </div>
                            )}

                            <div className="grid gap-1">
                                <div className="flex items-start gap-3">
                                    <Checkbox
                                        id="terms"
                                        name="terms"
                                        value="1"
                                        required
                                    />
                                    <Label
                                        htmlFor="terms"
                                        className="leading-snug font-normal"
                                    >
                                        I agree to the{' '}
                                        <TextLink
                                            href={terms()}
                                            target="_blank"
                                        >
                                            Terms
                                        </TextLink>{' '}
                                        and{' '}
                                        <TextLink
                                            href={privacy()}
                                            target="_blank"
                                        >
                                            Privacy Policy
                                        </TextLink>
                                    </Label>
                                </div>
                                <InputError message={errors.terms} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()}>Log in</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </>
    );
}

Register.layout = {
    title: 'Create your account',
    description: 'Start learning to type with step-by-step lessons',
};
