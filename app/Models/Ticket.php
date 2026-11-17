<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Support\AttachmentPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'technician_id',
        'category_id',
        'subject',
        'description',
        'priority',
        'status',
        'attachment',
        'attachment_name',
        'resolved_at',
        'closed_at',
    ];

    protected $attributes = [
        'status' => 'open',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'ticket_number';
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->latest('created_at')->latest('id');
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment);
    }

    public function attachmentIsImage(): bool
    {
        return $this->hasAttachment()
            && in_array(strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION)), AttachmentPolicy::IMAGE_EXTENSIONS, true);
    }

    /**
     * Search & filter used by the ticket list and CSV export.
     *
     * @param  array{q?:string|null, status?:string|array|null, category?:int|string|null, priority?:string|null,
     *               technician?:int|string|null, from?:string|null, to?:string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn (Builder $w) => $w
                    ->where('subject', 'like', $like)
                    ->orWhere('ticket_number', 'like', $like)
                    ->orWhere('description', 'like', $like));
            })
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => is_array($status)
                ? $q->whereIn('status', $status)
                : $q->where('status', $status))
            ->when($filters['category'] ?? null, fn (Builder $q, $id) => $q->where('category_id', (int) $id))
            ->when($filters['priority'] ?? null, fn (Builder $q, $p) => $q->where('priority', $p))
            ->when($filters['technician'] ?? null, fn (Builder $q, $tech) => $tech === 'none'
                ? $q->whereNull('technician_id')
                : $q->where('technician_id', (int) $tech))
            ->when($filters['from'] ?? null, fn (Builder $q, $d) => $q->where('created_at', '>=', Carbon::parse($d)->startOfDay()))
            ->when($filters['to'] ?? null, fn (Builder $q, $d) => $q->where('created_at', '<=', Carbon::parse($d)->endOfDay()));
    }

    /**
     * Restrict a query to the tickets the given user is allowed to see.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->isAdmin() => $query,
            $user->isTechnician() => $query->where('technician_id', $user->id),
            default => $query->where('user_id', $user->id),
        };
    }
}
