<?php

namespace App\Http\Controllers;

use App\Models\ServiceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceListingController extends Controller
{
    // ── SHOW FORM ─────────────────────────────────────────────

    public function create()
    {
        $profile = Auth::user()->technicianProfile;

        // Guard: should never happen since middleware protects this route,
        // but safety net in case profile row is missing for legacy accounts.
        if (! $profile) {
            return redirect('/')->with('error', 'Technician profile not found. Please contact support.');
        }

        return view('services.create', compact('profile'));
    }

    // ── STORE ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        $profile = Auth::user()->technicianProfile;

        if (! $profile) {
            return redirect('/')->with('error', 'Technician profile not found.');
        }

        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string'],
            'supported_models' => ['required', 'string'],   // JSON string from hidden input
            'description'      => ['required', 'string'],
            'price_min'        => ['required', 'numeric', 'min:0'],
            'price_max'        => ['required', 'numeric', 'min:0', 'gte:price_min'],
            'before_image'     => ['required', 'image', 'max:4096'],
            'after_image'      => ['required', 'image', 'max:4096'],
            'availability_days'=> ['required', 'string'],   // JSON string from hidden input
        ], [
            'price_max.gte'        => 'Max price must be greater than or equal to min price.',
            'before_image.required'=> 'A Before photo is required.',
            'after_image.required' => 'An After photo is required.',
            'before_image.image'   => 'Before photo must be an image file.',
            'after_image.image'    => 'After photo must be an image file.',
        ]);

        // Decode the supported models JSON string from the hidden input
        $models = json_decode($request->supported_models, true) ?? [];
        if (empty($models)) {
            return back()->withErrors(['supported_models' => 'Please add at least one supported device model.'])->withInput();
        }

        // Decode availability days
        $days = json_decode($request->availability_days, true) ?? [];
        if (empty($days)) {
            return back()->withErrors(['availability_days' => 'Please select at least one available day.'])->withInput();
        }

        // Store images in storage/app/public/listings/
        $beforePath = $request->file('before_image')->store('listings', 'public');
        $afterPath  = $request->file('after_image')->store('listings', 'public');

        // Always update the technician profile availability to reflect the latest listing
        $profile->update(['availability_windows' => ['days' => $days]]);

        ServiceListing::create([
            'technician_profile_id' => $profile->id,
            'title'                 => $request->title,
            'category'              => $request->category,
            'supported_models'      => $models,
            'description'           => $request->description,
            'price_min'             => $request->price_min,
            'price_max'             => $request->price_max,
            'before_image'          => $beforePath,
            'after_image'           => $afterPath,
            'available_days'        => $days,
            'status'                => 'pending',
        ]);

        return redirect('/technician-dashboard')
            ->with('success', 'Listing submitted for admin review. You\'ll be notified once approved.');
    }
}