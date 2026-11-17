@php
    $roleTabs = ['' => 'Semua'] + collect(App\Enums\Role::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])->all();
    $roleBadge = [
        'admin' => 'bg-rose-100 text-rose-800',
        'technician' => 'bg-indigo-100 text-indigo-800',
        'requester' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<x-app-layout title="User Management">
    <x-slot name="header">
        <x-page-title :subtitle="$users->total().' akun'">User Management</x-page-title>
    </x-slot>
    <x-slot name="actions">
        <x-button-link :href="route('admin.users.create')">+ Tambah User</x-button-link>
    </x-slot>

    <x-card :padding="false">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-4 py-3">
            @foreach ($roleTabs as $value => $label)
                @php $active = (string) ($filters['role'] ?? '') === (string) $value; @endphp
                <a href="{{ route('admin.users.index', array_filter(['role' => $value])) }}"
                   @class(['whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium', 'bg-indigo-50 text-indigo-700' => $active, 'text-slate-600 hover:bg-slate-100' => ! $active])>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="hidden px-4 py-3 sm:table-cell">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="hidden px-4 py-3 text-right md:table-cell">Tiket Dibuat</th>
                        <th class="hidden px-4 py-3 text-right md:table-cell">Tiket Ditangani</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $u->name }}
                                @if ($u->is(auth()->user())) <span class="text-xs font-normal text-slate-400">(Anda)</span> @endif
                            </td>
                            <td class="hidden px-4 py-3 text-slate-600 sm:table-cell">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleBadge[$u->role->value] }}">{{ $u->role->label() }}</span>
                            </td>
                            <td class="hidden px-4 py-3 text-right tabular-nums md:table-cell">{{ $u->tickets_count }}</td>
                            <td class="hidden px-4 py-3 text-right tabular-nums md:table-cell">
                                @if ($u->isTechnician())
                                    <a href="{{ route('tickets.index', ['technician' => $u->id]) }}" class="text-indigo-600 hover:text-indigo-500">{{ $u->assigned_tickets_count }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.edit', $u) }}" class="font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $users->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
