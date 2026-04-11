<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // ── PUBLIC PAGE ───────────────────────────────────────────

    public function index()
    {
        $events = Event::where('event_date', '>=', now())
            ->with('creator')
            ->orderBy('event_date')
            ->get();

        // Tag each event with whether the current user has RSVPed
        $userId = Auth::id();
        $rsvpedIds = $userId
            ? EventRsvp::where('user_id', $userId)
                ->whereIn('event_id', $events->pluck('id'))
                ->pluck('event_id')
                ->toArray()
            : [];

        $stats = [
            'upcoming' => $events->count(),
            'rsvps'    => $events->sum('attendee_count'),
            'cities'   => $events->pluck('location')
                ->map(fn($l) => trim(strtok($l, ',')))
                ->unique()->count(),
        ];

        return view('events', compact('events', 'rsvpedIds', 'stats'));
    }

    // ── RSVP (AJAX POST) ──────────────────────────────────────

    public function rsvp(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->isSoldOut()) {
            return response()->json(['success' => false, 'message' => 'This event is sold out.'], 422);
        }

        // Prevent duplicate RSVPs
        $existing = EventRsvp::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return response()->json([
                'success'        => true,
                'already_rsvped' => true,
                'reference'      => $existing->reference,
                'attendee_count' => $event->attendee_count,
            ]);
        }

        $rsvp = EventRsvp::create([
            'event_id' => $id,
            'user_id'  => Auth::id(),
        ]);

        $event->refresh();

        return response()->json([
            'success'        => true,
            'already_rsvped' => false,
            'reference'      => $rsvp->reference,
            'attendee_count' => $event->attendee_count,
            'tickets_left'   => $event->ticketsRemaining(),
        ]);
    }

    // ── ADMIN: CREATE EVENT ───────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'event_type'   => ['required', 'in:Swap Meet,Repair Café,Exhibition,Workshop'],
            'event_date'   => ['required', 'date', 'after:today'],
            'location'     => ['required', 'string', 'max:255'],
            'ticket_count' => ['required', 'integer', 'min:1'],
            'description'  => ['nullable', 'string'],
        ]);

        Event::create([
            'created_by'   => Auth::id(),
            'title'        => $request->title,
            'event_type'   => $request->event_type,
            'description'  => $request->description,
            'event_date'   => $request->event_date,
            'location'     => $request->location,
            'ticket_count' => $request->ticket_count,
        ]);

        return response()->json(['success' => true, 'message' => 'Event published to Community Event Board!']);
    }
}
