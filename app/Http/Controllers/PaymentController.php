<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // ── GET /jobs/{id}/pay — Show Payment Form ────────────────

    public function show($jobId)
    {
        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile.user',
            'booking.serviceListing',
        ])->findOrFail($jobId);

        // Only the collector of this job may pay
        abort_unless(Auth::id() === $job->booking->collector_id, 403);

        // Device must be marked as shipped before payment is allowed
        abort_unless(
            $job->isStepDone('shipped'),
            403,
            'Payment is not available until the technician has shipped your device.'
        );

        // Already paid — send back with info message
        if ($job->payment_status === 'paid') {
            return redirect('/jobs/' . $jobId)
                ->with('info', 'This job has already been paid.');
        }

        return view('payment', compact('job'));
    }

    // ── POST /jobs/{id}/pay — Process Payment ─────────────────

    public function process(Request $request, $jobId)
    {
        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile.user',
        ])->findOrFail($jobId);

        abort_unless(Auth::id() === $job->booking->collector_id, 403);
        abort_unless($job->isStepDone('shipped'), 403);
        abort_if($job->payment_status === 'paid', 422);

        $request->validate([
            'cardholder_name' => ['required', 'string', 'max:100'],
            'card_number'     => ['required', 'string', 'min:13', 'max:19'],
            'expiry'          => ['required', 'string', 'regex:/^\d{2}\/\d{2}$/'],
            'cvv'             => ['required', 'string', 'min:3', 'max:4'],
        ]);

        // Strip spaces/dashes — store last 4 only (never the full number)
        $digits   = preg_replace('/\D/', '', $request->card_number);
        $lastFour = substr($digits, -4);

        // Create payment record
        Payment::create([
            'repair_job_id'   => $job->id,
            'collector_id'    => Auth::id(),
            'amount'          => $job->payment_amount,
            'card_last_four'  => $lastFour,
            'cardholder_name' => $request->cardholder_name,
            'status'          => 'paid',
            'paid_at'         => now(),
        ]);

        // Mark payment_status on job and tick the 'pay' timeline step
        $state        = $job->timeline_state ?? [];
        $state['pay'] = true;

        $job->update([
            'payment_status' => 'paid',
            'timeline_state' => $state,
        ]);

        return redirect('/jobs/' . $jobId)
            ->with('success', 'Payment of ৳' . number_format($job->payment_amount, 0) . ' received! The technician has been notified.');
    }
}
