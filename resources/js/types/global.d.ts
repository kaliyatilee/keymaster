import type { Auth } from '@/types/auth';
import type { ServerNavItem } from '@/types/navigation';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            navigation: ServerNavItem[];
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
