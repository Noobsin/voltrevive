<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** Show the edit profile page */
    public function edit()
    {
        return view('profile-edit', ['user' => Auth::user()]);
    }

    /** Handle profile update (name, email, phone, avatar) */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'  => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect('/profile/edit')->with('success', 'Profile updated successfully.');
    }

    /** Handle password change */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => ['required'],
            'password'                  => ['required', 'min:8', 'confirmed'],
            'password_confirmation'     => ['required'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect('/profile/edit')->with('success', 'Password changed successfully.');
    }
}
