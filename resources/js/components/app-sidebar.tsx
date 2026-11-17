import { Link, usePage } from '@inertiajs/react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { navIcon } from '@/lib/nav-icons';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

/**
 * The menu comes from the server (NavigationBuilder) so it always matches
 * the user's role; the client only renders it.
 */
export function AppSidebar() {
    const { navigation, auth } = usePage().props;

    const items: NavItem[] = navigation.map((item) => ({
        title: item.title,
        href: item.href,
        icon: navIcon(item.icon),
    }));

    const label = auth.user.roles.includes('admin')
        ? 'Administration'
        : auth.user.roles.includes('teacher')
          ? 'Teaching'
          : 'Learning';

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={items} label={label} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
