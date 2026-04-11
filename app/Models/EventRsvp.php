<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class EventRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'reference',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── EVENTS ───────────────────────────────────────────────

    protected static function booted(): void
    {
        // Auto-generate reference and increment attendee count on creation
        static::creating(function (EventRsvp $rsvp) {
            $rsvp->reference = 'EVT-' . now()->year . '-' . strtoupper(Str::random(5));
        });

        static::created(function (EventRsvp $rsvp) {
            $rsvp->event->increment('attendee_count');
        });

        // Decrement attendee count if RSVP is ever removed
        static::deleted(function (EventRsvp $rsvp) {
            $rsvp->event->decrement('attendee_count');
        });
    }
}
