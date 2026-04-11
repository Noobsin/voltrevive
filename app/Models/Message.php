<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'sender_id',
        'body',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function job()
    {
        return $this->belongsTo(Job::class, 'repair_job_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── HELPERS ──────────────────────────────────────────────

    /** Returns the sender's first initial for avatar display */
    public function senderInitial(): string
    {
        return strtoupper(substr($this->sender->name, 0, 1));
    }

    /** Formatted timestamp for display in the message thread */
    public function timeLabel(): string
    {
        return $this->created_at->format('M j · g:i A');
    }
}
