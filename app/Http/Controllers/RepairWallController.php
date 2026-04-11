<?php

namespace App\Http\Controllers;

use App\Models\RepairWallPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairWallController extends Controller
{
    // ── SHOW PAGE ─────────────────────────────────────────────

    public function index()
    {
        $posts = RepairWallPost::with('user')
            ->latest()
            ->get();

        $totalPosts    = $posts->count();
        $recentPosts   = $posts->where('created_at', '>=', now()->subDays(7))->count();

        return view('repair-wall', compact('posts', 'totalPosts', 'recentPosts'));
    }

    // ── STORE NEW POST ────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'device_name' => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string'],
            'description' => ['required', 'string', 'max:400'],
        ]);

        RepairWallPost::create([
            'user_id'     => Auth::id(),
            'device_name' => $request->device_name,
            'category'    => $request->category,
            'description' => $request->description,
        ]);

        return redirect('/repair-wall')
            ->with('success', 'Your rescue appeal has been posted!');
    }
}
