@php
    $editing = $user->exists;
    $isSelf = $editing && $user->is(auth()->user());
@endphp

<x-app-layout :title="$editing ? 'Edit User' : 'Tambah User'">
    <x-slot name="header">
        <x-page-title :subtitle="$editing ? $user->email : null">{{ $editing ? 'Edit User' : 'Tambah User' }}</x-page-title>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-card>
            <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-6">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div>
                    <x-input-label for="name" value="Nama" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" required autofocus :value="old('name', $user->name)" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required :value="old('email', $user->email)" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" value="Role" />
                    @if ($isSelf)
                        <input type="hidden" name="role" value="{{ $user->role->value }}">
                        <p class="mt-1 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $user->role->label() }} — Anda tidak dapat mengubah role akun Anda sendiri.</p>
                    @else
                        <select id="role" name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (App\Enums\Role::cases() as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->role?->value ?? 'requester') === $role->value)>{{ $role->label() }}</option>
                            @endforeach
                        </select>
                    @endif
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                @unless ($editing)
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                        </div>
                    </div>
                @endunless

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                    <x-button-link variant="secondary" :href="route('admin.users.index')">Batal</x-button-link>
                    <x-primary-button>{{ $editing ? 'Simpan' : 'Buat Akun' }}</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
