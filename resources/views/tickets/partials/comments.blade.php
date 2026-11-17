<x-card id="comments" :title="'Komentar ('.$ticket->comments->count().')'">
    <ul class="space-y-5">
        @forelse ($ticket->comments as $comment)
            @php
                $author = $comment->user;
                $isStaff = ! $author->isRequester();
            @endphp
            <li class="flex gap-3">
                <div @class([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold',
                    'bg-indigo-100 text-indigo-700' => $isStaff,
                    'bg-slate-200 text-slate-700' => ! $isStaff,
                ])>{{ strtoupper(mb_substr($author->name, 0, 1)) }}</div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-baseline gap-x-2">
                        <span class="text-sm font-medium text-slate-900">{{ $author->name }}</span>
                        <span class="text-xs text-slate-500">{{ $author->role->label() }}</span>
                        <span class="text-xs text-slate-400" title="{{ $comment->created_at->format('d M Y H:i') }}">· {{ $comment->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="mt-1 whitespace-pre-line rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $comment->message }}</p>
                </div>
            </li>
        @empty
            <li class="text-sm text-slate-500">Belum ada komentar.</li>
        @endforelse
    </ul>

    @can('comment', $ticket)
        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-6 border-t border-slate-200 pt-5">
            @csrf
            <label for="message" class="sr-only">Komentar</label>
            <textarea id="message" name="message" rows="3" required maxlength="3000"
                      class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Tulis komentar atau pembaruan...">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <div class="mt-3 flex justify-end">
                <x-primary-button>Kirim Komentar</x-primary-button>
            </div>
        </form>
    @else
        <p class="mt-6 border-t border-slate-200 pt-4 text-xs text-slate-500">Tiket sudah ditutup; komentar baru tidak dapat ditambahkan.</p>
    @endcan
</x-card>
