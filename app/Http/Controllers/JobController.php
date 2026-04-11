<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    // ── SHOW JOB DETAIL PAGE ─────────────────────────────────

    public function show($id)
    {
        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile.user',
            'booking.serviceListing',
            'messages.sender',
        ])->findOrFail($id);

        $user   = Auth::user();
        $techProfile = $job->booking->technicianProfile;

        // A collector visiting has no technicianProfile, so check safely
        $isTech = $techProfile && ($user->id === $techProfile->user_id);
        $isColl = $user->id === $job->booking->collector_id;

        abort_unless($isTech || $isColl, 403);

        return view('job-detail', compact('job', 'isTech', 'isColl'));
    }

    // ── TECHNICIAN ACCEPTS BOOKING → CREATES JOB ─────────────

    public function accept($bookingId)
    {
        $booking = Booking::with('technicianProfile', 'serviceListing')->findOrFail($bookingId);

        abort_unless(
            Auth::id() === $booking->technicianProfile->user_id,
            403
        );

        abort_if($booking->status !== 'pending', 422);

        $booking->update([
            'status'            => 'confirmed',
            'shipping_deadline' => now()->addDays(14),
        ]);

        $job = Job::create([
            'booking_id'     => $booking->id,
            'reference'      => Job::generateReference(),
            'status'         => 'confirmed',
            'jitsi_room_url' => null,
            'payment_amount' => (
                ($booking->serviceListing->price_min ?? 0) +
                ($booking->serviceListing->price_max ?? 0)
            ) / 2,
            'payment_status' => 'pending',
            'timeline_state' => ['confirm' => true],
        ]);

        return response()->json([
            'success'   => true,
            'job_id'    => $job->id,
            'reference' => $job->reference,
        ]);
    }

    // ── TECHNICIAN REJECTS BOOKING ────────────────────────────

    public function reject(Request $request, $bookingId)
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $booking = Booking::with('technicianProfile')->findOrFail($bookingId);

        abort_unless(
            Auth::id() === $booking->technicianProfile->user_id,
            403
        );

        abort_if($booking->status !== 'pending', 422);

        $booking->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json(['success' => true]);
    }

    // ── START REPAIR (confirmed → in_progress) ───────────────

    public function start($id)
    {
        $job = Job::with('booking.technicianProfile')->findOrFail($id);

        abort_unless(Auth::id() === $job->booking->technicianProfile->user_id, 403);
        abort_if($job->status !== 'confirmed', 422);

        $state                   = $job->timeline_state ?? [];
        $state['repair_started'] = true;

        $job->update([
            'status'         => 'in_progress',
            'timeline_state' => $state,
        ]);

        return response()->json(['success' => true, 'status' => 'in_progress']);
    }

    // ── MARK COMPLETE (in_progress → completed) ───────────────

    public function complete($id)
    {
        $job = Job::with('booking.technicianProfile')->findOrFail($id);

        abort_unless(Auth::id() === $job->booking->technicianProfile->user_id, 403);
        abort_if($job->status !== 'in_progress', 422);

        $state             = $job->timeline_state ?? [];
        $state['complete'] = true;

        $job->update([
            'status'         => 'completed',
            'timeline_state' => $state,
        ]);

        $job->booking->technicianProfile->increment('completed_jobs_count');

        return response()->json(['success' => true, 'status' => 'completed']);
    }

    // ── CANCEL JOB (either party) ─────────────────────────────

    public function cancel($id)
    {
        $job = Job::with([
            'booking.technicianProfile.user',
            'booking.collector',
        ])->findOrFail($id);

        $isTech = $job->booking->technicianProfile &&
                  Auth::id() === $job->booking->technicianProfile->user_id;
        $isColl = Auth::id() === $job->booking->collector_id;

        abort_unless($isTech || $isColl, 403);
        abort_if(in_array($job->status, ['completed', 'cancelled']), 422);

        $job->update(['status' => 'cancelled']);
        $job->booking->update(['status' => 'cancelled']);


        return response()->json(['success' => true]);
    }

    // ── TICK TIMELINE STEP (technician only) ──────────────────

    public function tick(Request $request, $id)
    {
        $request->validate(['step' => ['required', 'string']]);

        $job = Job::with('booking.technicianProfile')->findOrFail($id);

        abort_unless(
            $job->booking->technicianProfile &&
            Auth::id() === $job->booking->technicianProfile->user_id,
            403
        );

        $allowedSteps = ['confirm', 'diag', 'shipped', 'pay', 'complete', 'ship'];
        abort_unless(in_array($request->step, $allowedSteps), 422);

        $state                = $job->timeline_state ?? [];
        $state[$request->step] = ! ($state[$request->step] ?? false);

        $job->update(['timeline_state' => $state]);

        return response()->json([
            'success' => true,
            'step'    => $request->step,
            'value'   => $state[$request->step],
        ]);
    }
}
