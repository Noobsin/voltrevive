<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        if (! Auth::user()->isTechnician()) {
            // Redirect to collector dashboard if logged in but not a technician
            return redirect('/collector-dashboard')
                ->with('error', 'You need an approved Technician profile to access that page.');
        }

        return $next($request);
    }
}
