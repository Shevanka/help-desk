<x-app-layout title="Dashboard">
    <x-slot name="header">
        <x-page-title subtitle="Tiket yang ditugaskan kepada Anda">Dashboard Teknisi</x-page-title>
    </x-slot>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Ditugaskan" :value="$stats['total']" tone="indigo" :href="route('tickets.index')" />
        <x-stat-card label="Belum Ditangani" :value="$stats['open']" tone="sky" hint="Status Open" :href="route('tickets.index', ['status' => 'open'])" />
        <x-stat-card label="In Progress" :value="$stats['in_progress']" tone="amber" :href="route('tickets.index', ['status' => 'in_progress'])" />
        <x-stat-card label="Diselesaikan" :value="$stats['resolved'] + $stats['closed']" tone="emerald" hint="Resolved + Closed" :href="route('tickets.index', ['status' => 'resolved'])" />
    </div>

    <x-card title="Tugas Terbaru" :padding="false" class="mt-6">
        <x-slot name="headerActions">
            <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat semua</a>
        </x-slot>
        <x-ticket-table :tickets="$recent" :show-technician="false" empty="Belum ada tiket yang ditugaskan kepada Anda." />
    </x-card>
</x-app-layout>
