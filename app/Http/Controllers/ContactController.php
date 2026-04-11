<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // ── SHOW CONTACT PAGE ─────────────────────────────────────

    public function index()
    {
        return view('contact');
    }

    // ── HANDLE FORM SUBMISSION (JSON fetch from blade) ────────

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ContactInquiry::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Your message has been sent! We'll get back to you shortly.",
        ]);
    }
}
