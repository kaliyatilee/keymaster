import { cn } from '@/lib/utils';

/**
 * Accessible progress bar. `value` and `max` use the same units.
 */
export function Progress({
    value,
    max = 100,
    label,
    className,
    barClassName,
}: {
    value: number;
    max?: number;
    label: string;
    className?: string;
    barClassName?: string;
}) {
    const percent = max > 0 ? Math.min(100, Math.max(0, (value / max) * 100)) : 0;

    return (
        <div
            role="progressbar"
            aria-label={label}
            aria-valuemin={0}
            aria-valuemax={max}
            aria-valuenow={value}
            className={cn('h-2 overflow-hidden rounded-full bg-muted', className)}
        >
            <div
                className={cn('h-full rounded-full bg-primary transition-[width]', barClassName)}
                style={{ width: `${percent}%` }}
            />
        </div>
    );
}
