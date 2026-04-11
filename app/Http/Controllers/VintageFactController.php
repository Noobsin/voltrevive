<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VintageFactController extends Controller
{
    // Curated list of vintage electronics Wikipedia article slugs
    private const ARTICLES = [
        'Regency_TR-1',
        'Sony_Walkman',
        'Moog_synthesizer',
        'Roland_Juno-106',
        'Yamaha_CS-80',
        'Fairlight_CMI',
        'ARP_2600',
        'Zenith_Trans-Oceanic',
        'Roland_TB-303',
        'Minimoog',
        'Atari_2600',
        'Commodore_64',
        'Nintendo_Entertainment_System',
        'Technics_SL-1200',
        'Roland_TR-808',
        'Polaroid_SX-70',
        'Leica_M3',
        'Linn_LM-1',
        'Sequential_Circuits_Prophet-5',
        'Korg_MS-20',
        'Braun_(company)',
        'Ampex',
        'Nagra_(audio)',
        'Studer',
    ];

    // How many facts to return per request
    private const FACTS_PER_PAGE = 5;

    // ── GET /api/vintage-fact ─────────────────────────────────

    public function show()
    {
        // Pick a rotating window of 5 articles based on day of year
        $dayOfYear  = (int) now()->format('z');
        $total      = count(self::ARTICLES);
        $startIndex = ($dayOfYear * self::FACTS_PER_PAGE) % $total;

        // Get 5 article slugs wrapping around the array
        $slugs = [];
        for ($i = 0; $i < self::FACTS_PER_PAGE; $i++) {
            $slugs[] = self::ARTICLES[($startIndex + $i) % $total];
        }

        $cacheKey = 'vintage_facts_day_' . $dayOfYear;

        $facts = Cache::remember($cacheKey, now()->addHours(24), function () use ($slugs) {
            $results = [];
            foreach ($slugs as $slug) {
                $fact = $this->fetchFromWikipedia($slug);
                if ($fact) {
                    $results[] = $fact;
                }
            }
            return $results;
        });

        if (empty($facts)) {
            return response()->json(['success' => false, 'message' => 'Could not load facts.'], 503);
        }

        return response()->json([
            'success' => true,
            'facts'   => $facts,
        ]);
    }

    // ── WIKIPEDIA API CALL ────────────────────────────────────

    private function fetchFromWikipedia(string $article): ?array
    {
        try {
            $response = Http::withoutVerifying() // SSL cert fix for local Windows dev — remove before production
                ->withHeaders([
                    'User-Agent' => 'VoltRevive/1.0 (https://voltrevive.com; contact@voltrevive.com)',
                ])->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$article}");

            if (! $response->successful()) {
                Log::warning("VintageFactController: Wikipedia API failed for {$article}", [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data    = $response->json();
            $extract = $data['extract'] ?? null;

            if (! $extract) {
                return null;
            }

            // Trim to first 2 sentences for a clean widget-sized fact
            $sentences = preg_split('/(?<=[.!?])\s+/', $extract);
            $text      = implode(' ', array_slice($sentences, 0, 2));

            // Clean up parenthetical pronunciation guides e.g. (/ˈmoʊɡ/)
            $text = preg_replace('/\(\/[^)]+\/\)\s*/', '', $text);
            $text = trim($text);

            $title = $data['title'] ?? str_replace('_', ' ', $article);

            return [
                'text'   => $text,
                'source' => $title . ' · Wikipedia',
                'url'    => $data['content_urls']['desktop']['page']
                            ?? "https://en.wikipedia.org/wiki/{$article}",
            ];

        } catch (\Exception $e) {
            Log::error("VintageFactController: Exception for {$article}", [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
