<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CohereController extends Controller
{
    // ── POST /api/rewrite-description ────────────────────────

    public function rewrite(Request $request)
    {
        $request->validate([
            'description' => ['required', 'string', 'min:10', 'max:400'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $apiKey = config('services.cohere.api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'AI rewrite service is not configured.',
            ], 503);
        }

        $rewritten = $this->callCohere(
            $request->description,
            $request->device_name ?? '',
            $apiKey
        );

        if (! $rewritten) {
            return response()->json([
                'success' => false,
                'message' => 'Could not rewrite description. Please try again.',
            ], 503);
        }

        return response()->json([
            'success'   => true,
            'rewritten' => $rewritten,
        ]);
    }

    // ── COHERE API CALL ───────────────────────────────────────

    private function callCohere(string $description, string $deviceName, string $apiKey): ?string
    {
        $deviceContext = $deviceName ? " for a {$deviceName}" : '';

        $prompt = "You are a vintage electronics repair specialist. A collector has described a fault{$deviceContext} in informal language. Rewrite their description as a concise, professional technical repair request in 2-3 sentences. Use precise technical language that a repair technician would expect. Do not add information that was not in the original. Return only the rewritten text with no preamble or explanation.\n\nOriginal description: {$description}";

        try {
            $response = Http::withoutVerifying() // Remove before production
                ->timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ])
                ->post('https://api.cohere.com/v2/chat', [
                    'model'      => 'command-r-08-2024', // Free-tier compatible
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 300,
                ]);

            if (! $response->successful()) {
                Log::warning('CohereController: API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // v2 standard shape: message.content[0].text
            $content = $data['message']['content'][0] ?? null;

            if (is_array($content)) {
                $text = $content['text'] ?? null;
            } elseif (is_string($content)) {
                $text = $content;
            } else {
                // fallbacks for other response shapes
                $text = $data['text']
                     ?? $data['generations'][0]['text']
                     ?? null;
            }

            return $text ? trim((string) $text) : null;

        } catch (\Exception $e) {
            Log::error('CohereController: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
