import type { LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

export function StatCard({
    icon: Icon,
    label,
    value,
    hint,
    tone = 'default',
}: {
    icon: LucideIcon;
    label: string;
    value: string;
    hint?: string;
    tone?: 'default' | 'xp' | 'success';
}) {
    return (
        <div className="rounded-2xl border border-border bg-card p-5">
            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <Icon
                    className={cn(
                        'size-4',
                        tone === 'xp' && 'text-xp',
                        tone === 'success' && 'text-success',
                    )}
                    aria-hidden="true"
                />
                {label}
            </div>
            <p className="mt-2 text-3xl font-semibold tracking-tight tabular-nums">
                {value}
            </p>
            {hint && (
                <p className="mt-1 text-xs text-muted-foreground">{hint}</p>
            )}
        </div>
    );
}
