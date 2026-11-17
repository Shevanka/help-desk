@php
    $user = auth()->user();
    $ticketsLabel = match (true) {
        $user->isAdmin() => 'All Tickets',
        $user->isTechnician() => 'Assigned Tickets',
        default => 'My Tickets',
    };
@endphp

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500 text-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
        </div>
        <div>
            <p class="text-base font-semibold leading-tight text-white">HelpDesk</p>
            <p class="text-xs text-slate-400">IT Support Ticketing</p>
        </div>
        <button type="button" @click="sidebarOpen = false" class="ml-auto rounded p-1 text-slate-400 hover:text-white lg:hidden" aria-label="Tutup menu">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Dashboard</x-sidebar-link>
        <x-sidebar-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index', 'tickets.show', 'tickets.edit')" icon="ticket">{{ $ticketsLabel }}</x-sidebar-link>

        @can('create', App\Models\Ticket::class)
            <x-sidebar-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')" icon="plus">Create Ticket</x-sidebar-link>
        @endcan

        @if ($user->isTechnician())
            <x-sidebar-link :href="route('tickets.history')" :active="request()->routeIs('tickets.history')" icon="history">Ticket History</x-sidebar-link>
        @endif

        @if ($user->isAdmin())
            <p class="px-3 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-500">Administrasi</p>
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="users">User Management</x-sidebar-link>
            <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="tag">Categories</x-sidebar-link>
            <x-sidebar-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')" icon="chart">Reports</x-sidebar-link>
        @endif

        <p class="px-3 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-500">Akun</p>
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">Profile</x-sidebar-link>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-semibold text-white">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ $user->role->label() }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="rounded-md p-2 text-slate-400 hover:bg-white/10 hover:text-white" aria-label="Logout">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
