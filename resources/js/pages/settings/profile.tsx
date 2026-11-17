import { Form, Head, Link, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/delete-user';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
    isMinor: boolean;
    isSchoolManaged: boolean;
    profile: {
        username: string;
        display_name: string;
        timezone: string;
        daily_goal_minutes: number;
        is_public: boolean;
        show_on_leaderboards: boolean;
    };
};

function timezones(): string[] {
    try {
        return Intl.supportedValuesOf('timeZone');
    } catch {
        return [];
    }
}

export default function Profile({
    mustVerifyEmail,
    status,
    isMinor,
    isSchoolManaged,
    profile,
}: Props) {
    const { auth } = usePage().props;
    const zones = useMemo(timezones, []);

    return (
        <>
            <Head title="Profile settings" />

            <h1 className="sr-only">Profile settings</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Profile"
                    description="Your account details and what others can see"
                />

                <Form
                    {...ProfileController.update.form()}
                    options={{ preserveScroll: true }}
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    defaultValue={auth.user.name}
                                    name="name"
                                    required
                                    autoComplete="name"
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="display_name">
                                    Display name
                                </Label>
                                <Input
                                    id="display_name"
                                    defaultValue={profile.display_name}
                                    name="display_name"
                                    required
                                    maxLength={50}
                                />
                                <InputError message={errors.display_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <Input
                                    id="username"
                                    defaultValue={profile.username}
                                    name="username"
                                    required
                                    autoComplete="username"
                                />
                                <InputError message={errors.username} />
                            </div>

                            {!isSchoolManaged && (
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        defaultValue={auth.user.email ?? ''}
                                        name="email"
                                        required
                                        autoComplete="email"
                                    />
                                    <InputError message={errors.email} />
                                </div>
                            )}

                            {mustVerifyEmail &&
                                auth.user.email_verified_at === null && (
                                    <div>
                                        <p className="-mt-4 text-sm text-muted-foreground">
                                            Your email address is unverified.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline underline-offset-4"
                                            >
                                                Re-send the verification email.
                                            </Link>
                                        </p>
                                        {status ===
                                            'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-success">
                                                A new verification link has been
                                                sent to your email address.
                                            </div>
                                        )}
                                    </div>
                                )}

                            <div className="grid gap-2">
                                <Label htmlFor="timezone">Time zone</Label>
                                <Input
                                    id="timezone"
                                    name="timezone"
                                    defaultValue={profile.timezone}
                                    list="timezone-options"
                                    required
                                    aria-describedby="timezone-hint"
                                />
                                <datalist id="timezone-options">
                                    {zones.map((zone) => (
                                        <option key={zone} value={zone} />
                                    ))}
                                </datalist>
                                <p
                                    id="timezone-hint"
                                    className="text-xs text-muted-foreground"
                                >
                                    Decides when your practice day starts for
                                    streaks. You can change it once a week.
                                </p>
                                <InputError message={errors.timezone} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="daily_goal_minutes">
                                    Daily practice goal (minutes)
                                </Label>
                                <Input
                                    id="daily_goal_minutes"
                                    name="daily_goal_minutes"
                                    type="number"
                                    min={1}
                                    max={120}
                                    defaultValue={profile.daily_goal_minutes}
                                    required
                                    className="w-32"
                                />
                                <InputError
                                    message={errors.daily_goal_minutes}
                                />
                            </div>

                            <fieldset className="grid gap-4">
                                <legend className="mb-2 text-sm font-medium">
                                    Privacy
                                </legend>
                                <input
                                    type="hidden"
                                    name="is_public"
                                    value="0"
                                />
                                <div className="flex items-start gap-3">
                                    <Checkbox
                                        id="is_public"
                                        name="is_public"
                                        value="1"
                                        defaultChecked={profile.is_public}
                                        disabled={isMinor}
                                    />
                                    <div className="grid gap-1">
                                        <Label htmlFor="is_public">
                                            Public profile
                                        </Label>
                                        <p className="text-xs text-muted-foreground">
                                            {isMinor
                                                ? 'Profiles of users under 18 stay private.'
                                                : 'Anyone can see your level, best speed and achievements.'}
                                        </p>
                                    </div>
                                </div>
                                <InputError message={errors.is_public} />
                                <input
                                    type="hidden"
                                    name="show_on_leaderboards"
                                    value="0"
                                />
                                <div className="flex items-start gap-3">
                                    <Checkbox
                                        id="show_on_leaderboards"
                                        name="show_on_leaderboards"
                                        value="1"
                                        defaultChecked={
                                            profile.show_on_leaderboards
                                        }
                                    />
                                    <div className="grid gap-1">
                                        <Label htmlFor="show_on_leaderboards">
                                            Show me on public leaderboards
                                        </Label>
                                        <p className="text-xs text-muted-foreground">
                                            Class leaderboards are always
                                            visible to your class.
                                        </p>
                                    </div>
                                </div>
                            </fieldset>

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                >
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>

            <DeleteUser />
        </>
    );
}

Profile.layout = {
    breadcrumbs: [{ title: 'Profile settings', href: edit() }],
};
