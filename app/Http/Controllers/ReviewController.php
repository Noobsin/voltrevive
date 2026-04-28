<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function show($jobId)
    {
        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile.user',
            'booking.serviceListing',
        ])->findOrFail($jobId);

        // Only the collector of this job can leave a review
        abort_unless(Auth::id() === $job->booking->collector_id, 403);
        abort_unless($job->status === 'completed', 403, 'Job is not yet completed.');

        // Prevent duplicate reviews
        $alreadyReviewed = Review::where('repair_job_id', $job->id)->exists();

        return view('review', compact('job', 'alreadyReviewed'));
    }

    public function store(Request $request, $jobId)
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:600'],
        ]);

        $job = Job::with('booking.technicianProfile')->findOrFail($jobId);

        abort_unless(Auth::id() === $job->booking->collector_id, 403);
        abort_unless($job->status === 'completed', 403);

        // Prevent duplicates
        abort_if(Review::where('repair_job_id', $job->id)->exists(), 422);

        Review::create([
            'repair_job_id'          => $job->id,
            'collector_id'           => Auth::id(),
            'technician_profile_id'  => $job->booking->technician_profile_id,
            'rating'                 => $request->rating,
            'comment'                => $request->comment,
        ]);

        return response()->json(['success' => true]);
    }
}