<?php

namespace App\Http\Controllers;

use App\Models\TechnicianProfile;

class CompareController extends Controller
{
    public function index()
    {
        // Load all technicians with approved active listings
        $profiles = TechnicianProfile::with(['user', 'activeListings'])
            ->whereHas('activeListings')
            ->get();

        // Build category map matching the compare page's filter slugs
        $catSlugMap = [
            'Synthesizer'      => 'synthesizer',
            'Vintage Radio'    => 'radio',
            'Hi-Fi Audio'      => 'hifi',
            'Retro Gaming'     => 'gaming',
            'Cameras'          => 'cameras',
            'Vintage Computer' => 'computer',
            'Other'            => 'other',
        ];

        // Build technician data for JS
        $techsData = $profiles->map(function ($profile) use ($catSlugMap) {
            $categories = $profile->activeListings
                ->pluck('category')
                ->unique()
                ->map(fn($cat) => $catSlugMap[$cat] ?? strtolower(str_replace(' ', '-', $cat)))
                ->values()
                ->toArray();

            $priceMin = $profile->activeListings->min('price_min') ?? 0;
            $priceMax = $profile->activeListings->max('price_max') ?? 0;

            $stars = str_repeat('★', (int) round($profile->avg_rating))
                   . str_repeat('☆', 5 - (int) round($profile->avg_rating));

            $yearsSince = now()->year - ($profile->user->created_at->year ?? now()->year);

            return [
                'id'       => $profile->user_id,
                'name'     => $profile->user->name,
                'location' => $profile->location ?? 'Unknown',
                'initial'  => strtoupper(substr($profile->user->name, 0, 1)),
                'rating'   => (float) $profile->avg_rating,
                'stars'    => $stars,
                'jobs'     => $profile->completed_jobs_count,
                'since'    => $profile->user->created_at->year ?? now()->year,
                'price'    => '$' . number_format($priceMin, 0) . '–$' . number_format($priceMax, 0),
                'cats'     => $profile->activeListings->pluck('category')->unique()->values()->toArray(),
                'catSlugs' => $categories,
            ];
        })->values();

        // Category counts for filter chips
        $catCounts = [];
        foreach ($techsData as $t) {
            foreach ($t['catSlugs'] as $slug) {
                $catCounts[$slug] = ($catCounts[$slug] ?? 0) + 1;
            }
        }

        return view('comparison', compact('techsData', 'catCounts'));
    }
}
