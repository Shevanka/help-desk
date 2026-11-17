<x-app-layout title="Categories">
    <x-slot name="header">
        <x-page-title subtitle="Kelola kategori permasalahan">Kategori</x-page-title>
    </x-slot>
    <x-slot name="actions">
        <x-button-link :href="route('admin.categories.create')">+ Tambah Kategori</x-button-link>
    </x-slot>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="hidden px-4 py-3 md:table-cell">Deskripsi</th>
                        <th class="px-4 py-3 text-right">Jumlah Tiket</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                            <td class="hidden max-w-md truncate px-4 py-3 text-slate-600 md:table-cell">{{ $category->description ?: '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-slate-900">{{ $category->tickets_count }}</td>
                            <td class="px-4 py-3">
                                @if ($category->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="ml-3 inline"
                                      onsubmit="return confirm('{{ $category->tickets_count > 0 ? 'Kategori ini dipakai tiket dan akan dinonaktifkan. Lanjutkan?' : 'Hapus kategori ini?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-red-600 hover:text-red-500">
                                        {{ $category->tickets_count > 0 ? 'Nonaktifkan' : 'Hapus' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $categories->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
