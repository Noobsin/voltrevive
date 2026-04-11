<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ── REGISTER ─────────────────────────────────────────────

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['required', 'string', 'max:30'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique'       => 'An account with this email already exists.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $role = $request->input('role') === 'technician' ? 'technician' : 'collector';

        $user = User::create([
            'name'     => trim($request->first_name . ' ' . $request->last_name),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ]);

        // Every technician needs a profile row immediately so service
        // listings can reference it via technician_profile_id FK.
        if ($role === 'technician') {
            TechnicianProfile::create([
                'user_id'              => $user->id,
                'application_id'       => null,
                'specialisation'       => 'General',
                'years_experience'     => 0,
                'availability_windows' => ['days' => []],
            ]);
        }

        Auth::login($user);
        return redirect('/');
    }

    // ── LOGIN ─────────────────────────────────────────────────

    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->isAdmin()) return redirect('/admin');
            return redirect('/');
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password. Please try again.'])
            ->onlyInput('email');
    }

    // ── LOGOUT ───────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}