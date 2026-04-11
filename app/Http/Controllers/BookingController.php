<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServiceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_listing_id' => ['required', 'integer', 'exists:service_listings,id'],
            'requested_date'     => ['required', 'date', 'after:today'],
            'device_name'        => ['required', 'string', 'max:255'],
            'device_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $listing = ServiceListing::with('technicianProfile')->findOrFail($request->service_listing_id);

        // Only approved, active listings can be booked
        if (! $listing->isApproved() || ! $listing->is_active) {
            return response()->json(['success' => false, 'message' => 'This service is no longer available.'], 422);
        }

        $profileId = $listing->technician_profile_id;

        // Date conflict check — no two bookings on same date for same technician
        if (Booking::hasConflict($profileId, $request->requested_date)) {
            return response()->json([
                'success'  => false,
                'conflict' => true,
                'message'  => 'That date is no longer available. Please choose another.',
            ], 409);
        }

        Booking::create([
            'collector_id'          => Auth::id(),
            'technician_profile_id' => $profileId,
            'service_listing_id'    => $listing->id,
            'requested_date'        => $request->requested_date,
            'device_name'           => $request->device_name,
            'device_description'    => $request->device_description,
            'status'                => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking request sent. Awaiting technician approval.',
        ]);
    }
}
