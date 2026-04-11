<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    // ── GET /api/exchange-rate ────────────────────────────────

    public function show()
    {
        $apiKey = config('services.exchangerate.api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Exchange rate service is not configured.',
            ], 503);
        }

        $rate = Cache::remember('exchange_rate_bdt_usd', now()->addHours(24), function () use ($apiKey) {
            return $this->fetchRate($apiKey);
        });

        if (! $rate) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch exchange rate.',
            ], 503);
        }

        return response()->json([
            'success'    => true,
            'rate'       => $rate,       // 1 BDT = X USD
            'updated_at' => now()->format('M j, Y'),
        ]);
    }

    // ── FETCH FROM EXCHANGERATE-API ───────────────────────────

    private function fetchRate(string $apiKey): ?float
    {
        try {
            $response = Http::withoutVerifying() // Remove before production
                ->get("https://v6.exchangerate-api.com/v6/{$apiKey}/pair/BDT/USD");

            if (! $response->successful()) {
                Log::warning('ExchangeRateController: API error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            if (($data['result'] ?? '') !== 'success') {
                Log::warning('ExchangeRateController: API returned error result', $data);
                return null;
            }

            return (float) $data['conversion_rate'];

        } catch (\Exception $e) {
            Log::error('ExchangeRateController: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
