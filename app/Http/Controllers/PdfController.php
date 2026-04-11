<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    // ── GET /events/{id}/rsvp-slip ────────────────────────────

    public function rsvpSlip($eventId)
    {
        $event = Event::findOrFail($eventId);
        $user  = Auth::user();

        // Verify the user has actually RSVPed
        $rsvp = EventRsvp::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->first();

        if (! $rsvp) {
            return response()->json([
                'success' => false,
                'message' => 'You have not RSVPed to this event.',
            ], 403);
        }

        $apiKey = config('services.pdfco.api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'PDF service is not configured.',
            ], 503);
        }

        // Cache per user+event — only generate once
        $cacheKey = 'rsvp_slip_' . $user->id . '_' . $eventId;

        $pdfUrl = Cache::remember($cacheKey, now()->addDays(7), function () use ($event, $rsvp, $user, $apiKey) {
            return $this->generatePdf($event, $rsvp, $user, $apiKey);
        });

        if (! $pdfUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Could not generate PDF. Please try again.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'url'     => $pdfUrl,
        ]);
    }

    // ── BUILD HTML & CALL PDF.CO API ──────────────────────────

    private function generatePdf($event, $rsvp, $user, string $apiKey): ?string
    {
        $html = $this->buildSlipHtml($event, $rsvp, $user);

        try {
            $response = Http::withoutVerifying() // Remove before production
                ->withHeaders(['x-api-key' => $apiKey])
                ->post('https://api.pdf.co/v1/pdf/convert/from/html', [
                    'html'     => $html,
                    'name'     => 'VoltRevive-RSVP-' . $rsvp->reference . '.pdf',
                    'margins'  => '20px 20px 20px 20px',
                    'paperSize'=> 'Letter',
                    'async'    => false,
                ]);

            if (! $response->successful()) {
                Log::warning('PdfController: PDF.co API error', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'event_id' => $event->id,
                ]);
                return null;
            }

            $data = $response->json();

            if (! empty($data['error'])) {
                Log::warning('PdfController: PDF.co returned error', ['error' => $data['message'] ?? 'unknown']);
                return null;
            }

            return $data['url'] ?? null;

        } catch (\Exception $e) {
            Log::error('PdfController: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ── RSVP SLIP HTML TEMPLATE ───────────────────────────────

    private function buildSlipHtml($event, $rsvp, $user): string
    {
        $eventName  = htmlspecialchars($event->title);
        $eventDate  = $event->event_date->format('l, F j, Y \a\t g:i A');
        $eventType  = htmlspecialchars($event->event_type);
        $location   = htmlspecialchars($event->location);
        $attendee   = htmlspecialchars($user->name);
        $email      = htmlspecialchars($user->email);
        $reference  = htmlspecialchars($rsvp->reference);
        $generated  = now()->format('M j, Y');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background:#fff; color:#1a1a1a; }
    .slip { max-width:600px; margin:0 auto; border:2px solid #d4891a; border-radius:12px; overflow:hidden; }
    .slip-header { background:#d4891a; color:#161310; padding:2rem 2.5rem; text-align:center; }
    .slip-header .logo { font-size:1.1rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; opacity:0.8; margin-bottom:0.5rem; }
    .slip-header h1 { font-size:1.8rem; font-weight:900; margin-bottom:0.3rem; }
    .slip-header p { font-size:0.85rem; opacity:0.75; }
    .slip-body { padding:2rem 2.5rem; }
    .field { margin-bottom:1.25rem; }
    .field-label { font-size:0.65rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#888; margin-bottom:0.2rem; }
    .field-value { font-size:1rem; font-weight:600; color:#1a1a1a; }
    .divider { border:none; border-top:1px dashed #ddd; margin:1.5rem 0; }
    .ref-box { background:#fdf6ec; border:1px solid #d4891a; border-radius:8px; padding:1rem 1.5rem; text-align:center; margin-bottom:1.5rem; }
    .ref-label { font-size:0.65rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#888; margin-bottom:0.3rem; }
    .ref-num { font-size:1.6rem; font-weight:900; color:#d4891a; letter-spacing:0.1em; }
    .footer { background:#fafafa; border-top:1px solid #eee; padding:1rem 2.5rem; text-align:center; font-size:0.72rem; color:#aaa; }
    .badge { display:inline-block; background:#fdf6ec; border:1px solid #d4891a; color:#a06010; padding:0.2rem 0.75rem; border-radius:20px; font-size:0.75rem; font-weight:700; }
  </style>
</head>
<body>
  <div class="slip">
    <div class="slip-header">
      <div class="logo">⚡ VoltRevive</div>
      <h1>RSVP Confirmed!</h1>
      <p>Community Events · Keep this slip as your proof of registration</p>
    </div>
    <div class="slip-body">
      <div class="field">
        <div class="field-label">Event</div>
        <div class="field-value">{$eventName}</div>
      </div>
      <div class="field">
        <div class="field-label">Type</div>
        <div class="field-value"><span class="badge">{$eventType}</span></div>
      </div>
      <div class="field">
        <div class="field-label">Date &amp; Time</div>
        <div class="field-value">{$eventDate}</div>
      </div>
      <div class="field">
        <div class="field-label">Venue</div>
        <div class="field-value">{$location}</div>
      </div>
      <hr class="divider"/>
      <div class="field">
        <div class="field-label">Attendee</div>
        <div class="field-value">{$attendee}</div>
      </div>
      <div class="field">
        <div class="field-label">Email</div>
        <div class="field-value">{$email}</div>
      </div>
      <hr class="divider"/>
      <div class="ref-box">
        <div class="ref-label">Booking Reference</div>
        <div class="ref-num">{$reference}</div>
      </div>
    </div>
    <div class="footer">
      Generated on {$generated} · voltrevive.com · Please present this slip at the event entrance
    </div>
  </div>
</body>
</html>
HTML;
    }
}
