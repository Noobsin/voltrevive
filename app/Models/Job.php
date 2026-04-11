<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    // Point to repair_jobs table to avoid conflict with Laravel queue jobs
    protected $table = 'repair_jobs';

    protected $fillable = [
        'booking_id',
        'reference',
        'status',
        'jitsi_room_url',
        'payment_amount',
        'payment_status',
        'ssl_transaction_id',
        'ssl_val_id',
        'timeline_state',
        'estimated_completion',
    ];

    protected $casts = [
        'timeline_state'       => 'array',
        'payment_amount'       => 'decimal:2',
        'estimated_completion' => 'date',
    ];

    // Default timeline — all steps false until technician ticks them
    protected $attributes = [
        'timeline_state' => null,
    ];

    // ── RELATIONSHIPS ─────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'repair_job_id')->orderBy('created_at');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'repair_job_id');
    }

    // ── FACTORY METHODS ───────────────────────────────────────

    public static function generateReference(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'VR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }



    /**
     * Accessor: $job->meeting_url reads jitsi_room_url column.
     * Keeps Blade/controllers decoupled from the underlying column name.
     */
    public function getMeetingUrlAttribute(): ?string
    {
        return $this->jitsi_room_url;
    }

    // ── TIMELINE HELPERS ──────────────────────────────────────

    public function tickStep(string $step, bool $done = true): void
    {
        $state        = $this->timeline_state ?? [];
        $state[$step] = $done;
        $this->update(['timeline_state' => $state]);
    }

    public function isStepDone(string $step): bool
    {
        return (bool) ($this->timeline_state[$step] ?? false);
    }

    public function completedStepsCount(): int
    {
        return collect($this->timeline_state ?? [])->filter()->count();
    }

    // ── STATUS HELPERS ────────────────────────────────────────

    public function isConfirmed(): bool   { return $this->status === 'confirmed'; }
    public function isInProgress(): bool  { return $this->status === 'in_progress'; }
    public function isCompleted(): bool   { return $this->status === 'completed'; }
    public function isCancelled(): bool   { return $this->status === 'cancelled'; }
    public function isPaymentPaid(): bool { return $this->payment_status === 'paid'; }
}
