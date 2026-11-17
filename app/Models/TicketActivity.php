<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivity extends Model
{
    public const UPDATED_AT = null;

    public const CREATED = 'created';
    public const UPDATED = 'updated';
    public const ASSIGNED = 'assigned';
    public const STATUS_CHANGED = 'status_changed';
    public const PRIORITY_CHANGED = 'priority_changed';
    public const COMMENTED = 'commented';
    public const ATTACHMENT = 'attachment';

    protected $fillable = ['ticket_id', 'user_id', 'action', 'old_value', 'new_value', 'description'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
