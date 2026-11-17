{{-- Shared subject / category / description fields for create & edit. --}}
<div>
    <x-input-label for="subject" value="Subject" />
    <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" maxlength="255" required autofocus
                  :value="old('subject', $ticket?->subject)" placeholder="Contoh: Internet Connection Unavailable" />
    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
</div>

<div>
    <x-input-label for="category_id" value="Kategori" />
    <select id="category_id" name="category_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="" disabled @selected(! old('category_id', $ticket?->category_id))>— Pilih kategori —</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) old('category_id', $ticket?->category_id) === $category->id)>
                {{ $category->name }}{{ $category->is_active ? '' : ' (nonaktif)' }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Deskripsi Masalah" />
    <textarea id="description" name="description" rows="6" required maxlength="5000"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Jelaskan kendala, lokasi, dan langkah yang sudah dicoba...">{{ old('description', $ticket?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
