@php
    $exts = App\Support\AttachmentPolicy::EXTENSIONS;
    $accept = App\Support\AttachmentPolicy::acceptAttribute();
@endphp

<div x-data="{ name: '', tooBig: false }">
    <x-input-label for="attachment" :value="isset($ticket) && $ticket?->hasAttachment() ? 'Ganti Lampiran (opsional)' : 'Lampiran (opsional)'" />
    <label for="attachment"
           class="mt-1 flex cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-slate-300 px-4 py-5 text-center hover:border-indigo-400 hover:bg-slate-50">
        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg>
        <span class="text-sm text-slate-700" x-text="name || 'Pilih file gambar atau dokumen'">Pilih file gambar atau dokumen</span>
        <span class="text-xs text-slate-500">{{ strtoupper(implode(', ', $exts)) }} · maks. 5 MB</span>
    </label>
    <input id="attachment" name="attachment" type="file" accept="{{ $accept }}" class="sr-only"
           @change="const f = $event.target.files[0]; name = f ? f.name : ''; tooBig = f && f.size > 5 * 1024 * 1024">
    <p x-show="tooBig" x-cloak class="mt-2 text-sm text-red-600">File melebihi 5 MB dan akan ditolak.</p>
    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
</div>
