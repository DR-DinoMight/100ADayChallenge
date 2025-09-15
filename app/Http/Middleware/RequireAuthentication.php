<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RequireAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via magic link
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the task tracker.');
        }

        // Check if session hasn't expired (24 hours)
        $authenticatedAt = Session::get('authenticated_at');
        if (! $authenticatedAt || now()->diffInHours($authenticatedAt) > 24) {
            Session::forget(['authenticated', 'user_id', 'user_email', 'user_name', 'authenticated_at']);

            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
