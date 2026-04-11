<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── ROLE HELPERS ──────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTechnician(): bool
    {
        // Role is set to 'technician' at registration when joining as technician
        return $this->role === 'technician';
    }

    // Every user is a collector by default
    public function isCollector(): bool
    {
        return true;
    }

    // Returns the display role label for the navbar
    public function roleLabel(): string
    {
        if ($this->isAdmin())      return 'Admin';
        if ($this->isTechnician()) return 'Technician';
        return 'Collector';
    }

    // Returns the first initial for the avatar circle
    public function initial(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class);
    }

    public function technicianApplications()
    {
        return $this->hasMany(TechnicianApplication::class);
    }

    public function latestApplication()
    {
        return $this->hasOne(TechnicianApplication::class)->latestOfMany();
    }

    public function bookingsAsCollector()
    {
        return $this->hasMany(Booking::class, 'collector_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'collector_id');
    }

    public function eventRsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function repairWallPosts()
    {
        return $this->hasMany(RepairWallPost::class);
    }
}