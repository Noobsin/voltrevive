<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;

class TechnicianPortfolioController extends Controller
{
    public function show($userId)
    {
        $user    = User::findOrFail($userId);
        $profile = $user->technicianProfile;

        abort_unless($profile, 404);

        $completedJobs = Job::whereHas('booking', fn($q) =>
                $q->where('technician_profile_id', $profile->id)
            )
            ->where('status', 'completed')
            ->with(['booking.collector', 'booking.serviceListing', 'review'])
            ->latest()
            ->get();

        // Category counts for filter chips
        $catCounts = $completedJobs
            ->groupBy(fn($j) => $j->booking->serviceListing->category ?? 'Other')
            ->map->count();

        return view('technician-portfolio', compact('user', 'profile', 'completedJobs', 'catCounts'));
    }
}
