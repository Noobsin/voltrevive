<?php

namespace App\Http\Controllers;

use App\Models\User;

class TechnicianController extends Controller
{
    public function show($id)
    {
        $user    = User::findOrFail($id);
        $profile = $user->technicianProfile;

        if (! $profile) {
            abort(404, 'Technician profile not found.');
        }

        $listings       = $profile->activeListings()->get();
        $availableDates = $profile->getAvailableDates();

        // Load reviews with collector info and the device name via the job
        $reviews = \App\Models\Review::where('technician_profile_id', $profile->id)
            ->with(['collector', 'job.booking'])
            ->latest()
            ->get();

        // Rating breakdown for the bar chart (5★ down to 1★)
        $ratingBreakdown = [];
        $total = $reviews->count();
        for ($star = 5; $star >= 1; $star--) {
            $count = $reviews->where('rating', $star)->count();
            $ratingBreakdown[$star] = [
                'count' => $count,
                'pct'   => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return view('technician-profile', compact(
            'user', 'profile', 'listings', 'availableDates', 'reviews', 'ratingBreakdown'
        ));
    }
}