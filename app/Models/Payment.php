<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'collector_id',
        'amount',
        'card_last_four',
        'cardholder_name',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ── RELATIONSHIPS ─────────────────────────────────────────

    public function job()
    {
        return $this->belongsTo(Job::class, 'repair_job_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}
