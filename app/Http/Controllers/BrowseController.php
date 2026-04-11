<?php

namespace App\Http\Controllers;

use App\Models\ServiceListing;

class BrowseController extends Controller
{
    public function index()
    {
        $listings = ServiceListing::where('status', 'approved')
            ->where('is_active', true)
            ->with('technicianProfile.user')
            ->latest()
            ->get();

        return view('browse', compact('listings'));
    }
}
