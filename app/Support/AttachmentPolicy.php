<?php

namespace App\Support;

/**
 * Single source of truth for which ticket attachments are accepted.
 * Used by the form requests (validation) and the upload input (accept / hint).
 */
final class AttachmentPolicy
{
    public const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public const MAX_KB = 5120; // 5 MB

    public static function acceptAttribute(): string
    {
        return implode(',', array_map(fn ($e) => '.'.$e, self::EXTENSIONS));
    }
}
