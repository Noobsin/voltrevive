<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_profile_id',
        'title',
        'category',
        'supported_models',
        'description',
        'price_min',
        'price_max',
        'before_image',
        'after_image',
        'status',
        'rejection_reason',
        'is_active',
        'available_days',
    ];

    protected $casts = [
        'supported_models' => 'array',
        'available_days'   => 'array',
        'price_min'        => 'decimal:2',
        'price_max'        => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function technicianProfile()
    {
        return $this->belongsTo(TechnicianProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ── SCOPES ────────────────────────────────────────────────

    /** Only listings visible on the Browse page */
    public function scopePublic($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    /** Filter by category */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /** Filter by minimum star rating (join to technician avg_rating) */
    public function scopeMinRating($query, float $minRating)
    {
        return $query->whereHas('technicianProfile', function ($q) use ($minRating) {
            $q->where('avg_rating', '>=', $minRating);
        });
    }

    /** Keyword search across title, supported_models, and technician name */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhereJsonContains('supported_models', $keyword)
              ->orWhereHas('technicianProfile.user', function ($q2) use ($keyword) {
                  $q2->where('name', 'like', "%{$keyword}%");
              });
        });
    }

    // ── HELPERS ──────────────────────────────────────────────

    public function priceRangeLabel(): string
    {
        return '$' . number_format($this->price_min) . ' – $' . number_format($this->price_max);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
