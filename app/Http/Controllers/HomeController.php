<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $currentJobId = null;

        if (Auth::check() && Auth::user()->isTechnician()) {
            $profile = Auth::user()->technicianProfile;
            if ($profile) {
                $activeJob = Job::whereHas('booking', fn($q) =>
                        $q->where('technician_profile_id', $profile->id)
                    )
                    ->whereIn('status', ['confirmed', 'in_progress'])
                    ->latest()
                    ->first();

                $currentJobId = $activeJob?->id;
            }
        }

        return view('home', compact('currentJobId'));
    }
}
