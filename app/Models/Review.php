<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'collector_id',
        'technician_profile_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function job()
    {
        return $this->belongsTo(Job::class, 'repair_job_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function technicianProfile()
    {
        return $this->belongsTo(TechnicianProfile::class);
    }

    // ── EVENTS ───────────────────────────────────────────────

    /**
     * After a review is saved, recalculate the technician's average rating.
     * This keeps avg_rating on technician_profiles always up to date.
     */
    protected static function booted(): void
    {
        static::created(function (Review $review) {
            $review->technicianProfile->recalculateRating();
        });
    }

    // ── HELPERS ──────────────────────────────────────────────

    /** Returns a string of filled/empty star characters e.g. "★★★★☆" */
    public function starDisplay(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
