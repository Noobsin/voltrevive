<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'collector_id',
        'technician_profile_id',
        'service_listing_id',
        'requested_date',
        'device_name',
        'device_description',
        'status',
        'rejection_reason',
        'shipping_deadline',
    ];

    protected $casts = [
        'requested_date'   => 'date',
        'shipping_deadline' => 'datetime',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function technicianProfile()
    {
        return $this->belongsTo(TechnicianProfile::class);
    }

    public function serviceListing()
    {
        return $this->belongsTo(ServiceListing::class);
    }

    /** The job created when this booking is confirmed */
    public function job()
    {
        return $this->hasOne(Job::class);
    }

    // ── CONFLICT CHECK ────────────────────────────────────────

    /**
     * Check whether the requested date conflicts with any existing
     * Pending or Confirmed booking for the same technician.
     *
     * Returns true if there IS a conflict (date is not available).
     */
    public static function hasConflict(int $technicianProfileId, string $requestedDate): bool
    {
        return static::where('technician_profile_id', $technicianProfileId)
                     ->whereIn('status', ['pending', 'confirmed'])
                     ->where('requested_date', $requestedDate)
                     ->exists();
    }

    // ── HELPERS ──────────────────────────────────────────────

    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isConfirmed(): bool  { return $this->status === 'confirmed'; }
    public function isRejected(): bool   { return $this->status === 'rejected'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    /**
     * Check if cancellation is within 24 hours of the shipping deadline.
     * Used to decide whether to fire the Twilio SMS alert.
     */
    public function isCancellationWithin24Hours(): bool
    {
        if (! $this->shipping_deadline) {
            return false;
        }

        return now()->diffInHours($this->shipping_deadline, false) <= 24
            && now()->lt($this->shipping_deadline);
    }
}
