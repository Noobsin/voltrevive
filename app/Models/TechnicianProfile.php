<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicianProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'bio',
        'location',
        'specialisation',
        'years_experience',
        'availability_windows',
        'avg_rating',
        'completed_jobs_count',
        'withdrawable_balance',
    ];

    protected $casts = [
        'availability_windows'  => 'array',
        'avg_rating'            => 'decimal:2',
        'withdrawable_balance'  => 'decimal:2',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    /** The user account for this technician */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The application that led to this profile being created */
    public function application()
    {
        return $this->belongsTo(TechnicianApplication::class, 'application_id');
    }

    /** All service listings created by this technician */
    public function serviceListings()
    {
        return $this->hasMany(ServiceListing::class);
    }

    /** Only approved, active service listings (used for Browse page) */
    public function activeListings()
    {
        return $this->hasMany(ServiceListing::class)
                    ->where('status', 'approved')
                    ->where('is_active', true);
    }

    /** All bookings for this technician */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /** All reviews received by this technician */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── HELPERS ──────────────────────────────────────────────

    /**
     * Recalculate and save the average rating from all reviews.
     * Call this after a new review is submitted.
     */
    public function recalculateRating(): void
    {
        $avg = $this->reviews()->avg('rating') ?? 0;
        $this->update(['avg_rating' => round($avg, 2)]);
    }

    /**
     * Returns an array of available date strings for the next 30 days
     * based on the technician's availability_windows settings,
     * excluding dates that already have Pending or Confirmed bookings.
     */
    public function getAvailableDates(): array
    {
        $windows      = $this->availability_windows;            // e.g. {"days":["Tue","Wed","Thu"]}
        $allowedDays  = $windows['days'] ?? [];

        // Dates already blocked by existing bookings
        $blocked = $this->bookings()
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->pluck('requested_date')
                        ->map(fn($d) => $d->format('Y-m-d'))
                        ->toArray();

        $available = [];
        $today     = now();

        for ($i = 1; $i <= 30; $i++) {
            $date    = $today->copy()->addDays($i);
            $dayName = $date->format('D');                      // "Mon", "Tue", etc.

            if (in_array($dayName, $allowedDays) && ! in_array($date->format('Y-m-d'), $blocked)) {
                $available[] = $date->format('l, M j, Y');     // "Tuesday, Mar 18, 2026"
            }
        }

        return $available;
    }
}
