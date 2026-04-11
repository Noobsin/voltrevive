<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Job;
use App\Models\Payment;
use App\Models\ServiceListing;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->technicianProfile;

        abort_unless($profile, 403, 'Technician profile not found.');

        // Pending booking requests awaiting accept/reject
        $pendingBookings = Booking::where('technician_profile_id', $profile->id)
            ->where('status', 'pending')
            ->with('collector', 'serviceListing')
            ->latest()
            ->get();

        // Active jobs (confirmed or in_progress)
        $activeJobs = Job::whereHas('booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with('booking.collector', 'booking.serviceListing')
            ->latest()
            ->get();

        // Completed jobs for history
        $completedJobs = Job::whereHas('booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->where('status', 'completed')
            ->with('booking.collector', 'booking.serviceListing')
            ->latest()
            ->take(20)
            ->get();

        // Revenue stats — based on actual confirmed payments (not job estimates)
        $totalRevenue = Payment::whereHas('job.booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->where('status', 'paid')
            ->sum('amount');

        $monthRevenue = Payment::whereHas('job.booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $yearRevenue = Payment::whereHas('job.booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $stats = [
            'total_revenue'  => $totalRevenue,
            'month_revenue'  => $monthRevenue,
            'year_revenue'   => $yearRevenue,
            'completed_jobs' => $profile->completed_jobs_count,
            'avg_rating'     => $profile->avg_rating,
            'pending_count'  => $pendingBookings->count(),
        ];

        $myListings = ServiceListing::where('technician_profile_id', $profile->id)
            ->latest()
            ->get();

        return view('technician-dashboard', compact(
            'pendingBookings', 'activeJobs', 'completedJobs', 'stats', 'profile', 'myListings'
        ));
    }
}
