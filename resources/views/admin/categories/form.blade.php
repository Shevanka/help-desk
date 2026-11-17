@php $editing = $category->exists; @endphp

<x-app-layout :title="$editing ? 'Edit Kategori' : 'Tambah Kategori'">
    <x-slot name="header">
        <x-page-title>{{ $editing ? 'Edit Kategori' : 'Tambah Kategori' }}</x-page-title>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-card>
            <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-6">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div>
                    <x-input-label for="name" value="Nama Kategori" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" maxlength="100" required autofocus :value="old('name', $category->name)" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Deskripsi (opsional)" />
                    <textarea id="description" name="description" rows="3" maxlength="1000"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           @checked(old('is_active', $category->is_active))>
                    <span class="text-sm text-slate-700">Aktif (dapat dipilih saat membuat tiket)</span>
                </label>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                    <x-button-link variant="secondary" :href="route('admin.categories.index')">Batal</x-button-link>
                    <x-primary-button>{{ $editing ? 'Simpan' : 'Tambah' }}</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
