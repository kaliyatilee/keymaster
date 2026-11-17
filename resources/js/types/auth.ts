export type RoleName = 'student' | 'teacher' | 'admin';

/**
 * The user fields the server shares with every page
 * (see HandleInertiaRequests::sharedUser). Sensitive fields such as date of
 * birth are never sent.
 */
export type User = {
    id: number;
    name: string;
    username: string;
    email: string | null;
    display_name: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    status: 'active' | 'pending_consent' | 'suspended';
    roles: RoleName[];
    is_school_managed: boolean;
    awaiting_teacher_approval: boolean;
    created_at: string;
    updated_at: string;
};

export type Auth = {
    user: User;
};

export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
