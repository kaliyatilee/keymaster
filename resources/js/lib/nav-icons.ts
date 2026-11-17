import type { LucideIcon } from 'lucide-react';
import {
    Award,
    BookOpen,
    ChartLine,
    ClipboardList,
    FileBarChart,
    Flame,
    Keyboard,
    LayoutGrid,
    ListChecks,
    School,
    Settings,
    Trophy,
    User,
    Users,
} from 'lucide-react';

/**
 * Icon names the server may send in navigation items. Unknown names fall
 * back to a neutral icon rather than failing.
 */
const icons: Record<string, LucideIcon> = {
    award: Award,
    'book-open': BookOpen,
    'chart-line': ChartLine,
    'clipboard-list': ClipboardList,
    'file-bar-chart': FileBarChart,
    flame: Flame,
    keyboard: Keyboard,
    'layout-grid': LayoutGrid,
    'list-checks': ListChecks,
    school: School,
    settings: Settings,
    trophy: Trophy,
    user: User,
    users: Users,
};

export function navIcon(name: string): LucideIcon {
    return icons[name] ?? LayoutGrid;
}
