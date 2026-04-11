<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // ── SHARED AUTH HELPER ────────────────────────────────────

    private function authoriseJob(Job $job): void
    {
        $user        = Auth::user();
        $techProfile = $job->booking->technicianProfile;
        $isTech      = $techProfile && ($user->id === $techProfile->user_id);
        $isColl      = $user->id === $job->booking->collector_id;

        abort_unless($isTech || $isColl, 403);
    }

    // ── POST /jobs/{id}/messages ──────────────────────────────

    public function store(Request $request, int $jobId)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile',
        ])->findOrFail($jobId);

        $this->authoriseJob($job);

        $message = Message::create([
            'repair_job_id' => $job->id,
            'sender_id'     => Auth::id(),
            'body'          => $request->body,
        ]);

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($message),
        ]);
    }

    // ── GET /jobs/{id}/messages?after={id} ────────────────────
    // Returns all messages, or only those after a given ID (for polling)

    public function poll(Request $request, int $jobId)
    {
        $job = Job::with([
            'booking.collector',
            'booking.technicianProfile',
        ])->findOrFail($jobId);

        $this->authoriseJob($job);

        $query = Message::with('sender')
            ->where('repair_job_id', $job->id);

        if ($request->filled('after')) {
            $query->where('id', '>', (int) $request->after);
        }

        $messages = $query->orderBy('created_at')
            ->get()
            ->map(fn($m) => $this->formatMessage($m));

        return response()->json(['messages' => $messages]);
    }

    // ── FORMAT HELPER ─────────────────────────────────────────

    private function formatMessage(Message $m): array
    {
        return [
            'id'             => $m->id,
            'sender_id'      => $m->sender_id,
            'sender_name'    => $m->sender->name,
            'sender_initial' => $m->sender->initial(),
            'body'           => $m->body,
            'time'           => $m->timeLabel(),
        ];
    }
}
