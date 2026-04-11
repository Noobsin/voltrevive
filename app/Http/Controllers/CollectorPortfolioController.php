<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class CollectorPortfolioController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $completedJobs = Job::whereHas('booking', fn($q) =>
                $q->where('collector_id', $userId)
            )
            ->where('status', 'completed')
            ->with([
                'booking.technicianProfile.user',
                'booking.serviceListing',
                'review',
            ])
            ->latest()
            ->get();

        $activeJobs = Job::whereHas('booking', fn($q) =>
                $q->where('collector_id', $userId)
            )
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with(['booking.technicianProfile.user', 'booking.serviceListing'])
            ->latest()
            ->get();

        $stats = [
            'total_restored' => $completedJobs->count() + $activeJobs->count(),
            'completed'      => $completedJobs->count(),
            'in_progress'    => $activeJobs->count(),
        ];

        return view('collector-portfolio', compact('completedJobs', 'activeJobs', 'stats'));
    }
}
