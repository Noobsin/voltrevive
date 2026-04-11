<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairWallPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'category',
        'description',
    ];

    // ── RELATIONSHIPS ─────────────────────────────────────────

    /** The collector who posted this appeal */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── HELPERS ──────────────────────────────────────────────

    /** e.g. "3 days ago" */
    public function timeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * The collector's phone number for the contact modal.
     * Technicians click "Contact Collector" which reveals this number.
     */
    public function collectorPhone(): ?string
    {
        return $this->user->phone;
    }
}
