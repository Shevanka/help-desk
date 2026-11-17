<?php

namespace App\Http\Requests\Concerns;

use App\Support\AttachmentPolicy;

/**
 * Attachment rules (NFR-01): checks size, the client extension AND the
 * MIME type detected from the file content, so a renamed .php/.exe is rejected.
 */
trait ValidatesAttachment
{
    protected function attachmentRules(): array
    {
        $ext = implode(',', AttachmentPolicy::EXTENSIONS);

        return [
            'nullable',
            'file',
            'max:'.AttachmentPolicy::MAX_KB,
            'extensions:'.$ext,
            'mimes:'.$ext,
        ];
    }

    protected function attachmentMessages(): array
    {
        return [
            'attachment.max' => 'Ukuran lampiran maksimal 5 MB.',
            'attachment.extensions' => 'Format lampiran harus: '.implode(', ', AttachmentPolicy::EXTENSIONS).'.',
            'attachment.mimes' => 'Isi file tidak sesuai dengan format yang diizinkan.',
            'attachment.uploaded' => 'Lampiran gagal diunggah (mungkin melebihi batas ukuran server).',
        ];
    }
}
