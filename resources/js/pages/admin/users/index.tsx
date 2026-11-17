import { Form, Head, Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/users';
import type { Paginated } from '@/types';

type Row = {
    id: number;
    username: string;
    name: string;
    email: string | null;
    roles: string[];
    status: 'active' | 'pending_consent' | 'suspended';
    account_type: 'self' | 'school_managed';
    created_at: string | null;
    last_login_at: string | null;
};

type Props = {
    users: Paginated<Row>;
    filters: { search: string; role: string; status: string };
    roles: { name: string; label: string }[];
};

const statusLabel: Record<Row['status'], string> = {
    active: 'Active',
    pending_consent: 'Awaiting consent',
    suspended: 'Suspended',
};

/** Laravel's paginator labels contain HTML entities; render them as text. */
function pageLabel(label: string): string {
    return label.replace('&laquo;', '«').replace('&raquo;', '»');
}

const selectClass =
    'h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none';

export default function Users({ users, filters, roles }: Props) {
    return (
        <>
            <Head title="Users" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Users
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            {users.total.toLocaleString()} accounts
                        </p>
                    </div>
                    <Form
                        {...index.form()}
                        method="get"
                        className="flex flex-wrap items-end gap-3"
                    >
                        <div className="grid gap-1">
                            <Label htmlFor="search">Search</Label>
                            <Input
                                id="search"
                                name="search"
                                defaultValue={filters.search}
                                placeholder="Name, username or email"
                                className="w-56"
                            />
                        </div>
                        <div className="grid gap-1">
                            <Label htmlFor="role">Role</Label>
                            <select
                                id="role"
                                name="role"
                                defaultValue={filters.role}
                                className={selectClass}
                            >
                                <option value="">All roles</option>
                                {roles.map((role) => (
                                    <option key={role.name} value={role.name}>
                                        {role.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="grid gap-1">
                            <Label htmlFor="status">Status</Label>
                            <select
                                id="status"
                                name="status"
                                defaultValue={filters.status}
                                className={selectClass}
                            >
                                <option value="">Any status</option>
                                {Object.entries(statusLabel).map(
                                    ([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ),
                                )}
                            </select>
                        </div>
                        <Button type="submit" variant="outline">
                            Filter
                        </Button>
                    </Form>
                </div>

                <div className="overflow-x-auto rounded-2xl border border-border bg-card">
                    <table className="w-full text-left text-sm">
                        <thead className="border-b border-border bg-muted/50 text-xs text-muted-foreground uppercase">
                            <tr>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    User
                                </th>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    Email
                                </th>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    Roles
                                </th>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    Joined
                                </th>
                                <th
                                    scope="col"
                                    className="px-4 py-3 font-medium"
                                >
                                    Last login
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {users.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className="px-4 py-10 text-center text-muted-foreground"
                                    >
                                        No users match these filters.
                                    </td>
                                </tr>
                            )}
                            {users.data.map((user) => (
                                <tr key={user.id}>
                                    <td className="px-4 py-3">
                                        <div className="font-medium">
                                            {user.name}
                                        </div>
                                        <div className="text-muted-foreground">
                                            @{user.username}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        {user.email ?? (
                                            <span className="text-muted-foreground">
                                                School-managed
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-wrap gap-1">
                                            {user.roles.map((role) => (
                                                <Badge
                                                    key={role}
                                                    variant="secondary"
                                                >
                                                    {role}
                                                </Badge>
                                            ))}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <Badge
                                            variant={
                                                user.status === 'active'
                                                    ? 'outline'
                                                    : 'destructive'
                                            }
                                        >
                                            {statusLabel[user.status]}
                                        </Badge>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {user.created_at
                                            ? new Date(
                                                  user.created_at,
                                              ).toLocaleDateString()
                                            : '—'}
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {user.last_login_at
                                            ? new Date(
                                                  user.last_login_at,
                                              ).toLocaleString()
                                            : 'Never'}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {users.last_page > 1 && (
                    <nav
                        aria-label="Pagination"
                        className="flex flex-wrap gap-1"
                    >
                        {users.links.map((link, i) =>
                            link.url ? (
                                <Link
                                    key={i}
                                    href={link.url}
                                    preserveScroll
                                    className={`rounded-md px-3 py-1.5 text-sm ${link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-accent'}`}
                                >
                                    {pageLabel(link.label)}
                                </Link>
                            ) : (
                                <span
                                    key={i}
                                    className="px-3 py-1.5 text-sm text-muted-foreground"
                                >
                                    {pageLabel(link.label)}
                                </span>
                            ),
                        )}
                    </nav>
                )}
            </div>
        </>
    );
}

Users.layout = {
    breadcrumbs: [
        { title: 'Admin', href: dashboard() },
        { title: 'Users', href: index() },
    ],
};
