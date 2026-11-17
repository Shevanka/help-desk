<x-app-layout title="Dashboard">
    <x-slot name="header">
        <x-page-title subtitle="Pantau laporan kendala teknis Anda">Halo, {{ auth()->user()->name }}</x-page-title>
    </x-slot>
    <x-slot name="actions">
        <x-button-link :href="route('tickets.create')">+ Buat Tiket</x-button-link>
    </x-slot>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Tiket Saya" :value="$stats['total']" tone="indigo" :href="route('tickets.index')" />
        <x-stat-card label="Open" :value="$stats['open']" tone="sky" :href="route('tickets.index', ['status' => 'open'])" />
        <x-stat-card label="In Progress" :value="$stats['in_progress']" tone="amber" :href="route('tickets.index', ['status' => 'in_progress'])" />
        <x-stat-card label="Resolved" :value="$stats['resolved']" tone="emerald" hint="Menunggu konfirmasi Anda" :href="route('tickets.index', ['status' => 'resolved'])" />
    </div>

    <x-card title="Riwayat Laporan Terbaru" :padding="false" class="mt-6">
        <x-slot name="headerActions">
            <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat semua</a>
        </x-slot>
        <x-ticket-table :tickets="$recent" :show-requester="false" empty="Anda belum membuat laporan. Klik “Buat Tiket” untuk melaporkan kendala." />
    </x-card>
</x-app-layout>
