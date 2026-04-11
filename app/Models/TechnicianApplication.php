<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicianApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialisation',
        'years_experience',
        'certifications_path',
        'availability_windows',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'availability_windows' => 'array',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The technician profile created when this application is approved */
    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class, 'application_id');
    }

    // ── HELPERS ──────────────────────────────────────────────

    public function isUnderReview(): bool { return $this->status === 'under_review'; }
    public function isApproved(): bool    { return $this->status === 'approved'; }
    public function isRejected(): bool    { return $this->status === 'rejected'; }
}
