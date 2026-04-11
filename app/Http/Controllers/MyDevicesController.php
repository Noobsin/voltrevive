<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class MyDevicesController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('collector_id', Auth::id())
            ->with([
                'technicianProfile.user',
                'serviceListing',
                'job',
            ])
            ->latest()
            ->get();

        $total    = $bookings->count();
        $active   = $bookings->filter(fn($b) => $b->job && in_array($b->job->status, ['confirmed','in_progress']))->count();
        $completed = $bookings->filter(fn($b) => $b->job && $b->job->status === 'completed')->count();

        return view('my-devices', compact('bookings', 'total', 'active', 'completed'));
    }
}
