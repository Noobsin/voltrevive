<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class CollectorDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Active/in-progress jobs
        $activeJobs = Job::whereHas('booking', fn($q) =>
                $q->where('collector_id', $userId)
            )
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with('booking.technicianProfile.user', 'booking.serviceListing')
            ->latest()
            ->get();

        // All bookings including pending/rejected for history
        $allBookings = Booking::where('collector_id', $userId)
            ->with('technicianProfile.user', 'serviceListing', 'job')
            ->latest()
            ->get();

        $stats = [
            'total_sent'   => $allBookings->count(),
            'completed'    => $allBookings->filter(fn($b) => $b->job && $b->job->status === 'completed')->count(),
            'under_repair' => $activeJobs->count(),
            'total_spent'  => Job::whereHas('booking', fn($q) =>
                    $q->where('collector_id', $userId)
                )
                ->where('status', 'completed')
                ->sum('payment_amount'),
        ];

        return view('collector-dashboard', compact('activeJobs', 'allBookings', 'stats'));
    }
}
