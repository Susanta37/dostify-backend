import { Head } from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-2xl font-semibold">VoiceConnect Admin</h1>
                    <p className="mt-2 text-muted-foreground">
                        Social audio platform admin dashboard. Manage users, hosts,
                        wallets, coin packages, gifts, and more.
                    </p>
                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {['Users', 'Active Rooms', 'Revenue'].map((label) => (
                        <div
                            key={label}
                            className="relative overflow-hidden rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border"
                        >
                            <p className="text-sm text-muted-foreground">{label}</p>
                            <p className="mt-2 text-3xl font-semibold">—</p>
                        </div>
                    ))}
                </div>
                <div className="relative min-h-[40vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
