<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'event_type',
        'description',
        'event_date',
        'location',
        'ticket_count',
        'attendee_count',
    ];

    protected $casts = [
        'event_date'      => 'datetime',
        'ticket_count'    => 'integer',
        'attendee_count'  => 'integer',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    /** Check if a specific user has already RSVPed */
    public function hasRsvp(int $userId): bool
    {
        return $this->rsvps()->where('user_id', $userId)->exists();
    }

    // ── HELPERS ──────────────────────────────────────────────

    public function ticketsRemaining(): int
    {
        return max(0, $this->ticket_count - $this->attendee_count);
    }

    public function isSoldOut(): bool
    {
        return $this->ticketsRemaining() === 0;
    }

    public function formattedDate(): string
    {
        return $this->event_date->format('D, M j, Y · g:i A');
    }
}
