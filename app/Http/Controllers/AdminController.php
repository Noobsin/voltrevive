<?php

namespace App\Http\Controllers;

use App\Models\ServiceListing;
use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\Job;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ── DASHBOARD ─────────────────────────────────────────────

    public function index()
    {
        $pendingListings = ServiceListing::where('status', 'pending')
            ->with('technicianProfile.user')
            ->latest()
            ->get();

        $technicians = TechnicianProfile::with('user')
            ->latest()
            ->get();

        $stats = [
            'pending_listings'       => $pendingListings->count(),
            'total_technicians'      => $technicians->count(),
            'total_users'            => User::where('role', '!=', 'admin')->count(),
            'jobs_completed_month'   => Job::where('status', 'completed')
                                           ->whereMonth('updated_at', now()->month)
                                           ->whereYear('updated_at', now()->year)
                                           ->count(),
            'upcoming_events'        => Event::where('event_date', '>=', now())->count(),
            'total_payments'         => Payment::where('status', 'paid')->count(),
            'total_revenue'          => Payment::where('status', 'paid')->sum('amount'),
        ];

        // Recent payments for the Payments section
        $payments = Payment::with([
                'job.booking.technicianProfile.user',
                'collector',
            ])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->take(50)
            ->get();

        return view('admin', compact('pendingListings', 'technicians', 'stats', 'payments'));
    }

    // ── APPROVE LISTING ───────────────────────────────────────

    public function approveListing($id)
    {
        $listing = ServiceListing::findOrFail($id);
        $listing->update(['status' => 'approved']);

        return response()->json(['success' => true, 'message' => 'Listing approved — now live in Browse.']);
    }

    // ── REJECT LISTING ────────────────────────────────────────

    public function rejectListing(Request $request, $id)
    {
        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $listing = ServiceListing::findOrFail($id);
        $listing->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Listing rejected.']);
    }
}
