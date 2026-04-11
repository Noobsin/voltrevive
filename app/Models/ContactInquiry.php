<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'recaptcha_score',
        'is_read',
    ];

    protected $casts = [
        'recaptcha_score' => 'decimal:2',
        'is_read'         => 'boolean',
    ];

    // ── SCOPES ────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // ── HELPERS ──────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
