<?php

namespace App\Http\Controllers;

use App\Models\MagicLink;
use App\Mail\MagicLinkMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function requestMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if this is the authorized email (you can configure this)
        $authorizedEmails = config('auth.authorized_emails', ['your-email@example.com']);

        if (!in_array($email, $authorizedEmails)) {
            throw ValidationException::withMessages([
                'email' => 'This email address is not authorized to access the task tracker.',
            ]);
        }

        // Create magic link
        $magicLink = MagicLink::createForEmail($email);

        // Send email
        Mail::to($email)->send(new MagicLinkMail($magicLink));

        return back()->with('success', 'Magic link sent! Check your email for the secure login link.');
    }

    public function verifyMagicLink(string $token)
    {
        $magicLink = MagicLink::where('token', $token)->first();

        if (!$magicLink || !$magicLink->isValid()) {
            return redirect()->route('login')->with('error', 'Invalid or expired magic link.');
        }

        // Mark as used
        $magicLink->markAsUsed();

        // Set session
        Session::put('authenticated', true);
        Session::put('user_email', $magicLink->email);
        Session::put('authenticated_at', now());

        return redirect()->route('task-tracker')->with('success', 'Welcome back! You are now logged in.');
    }

    public function logout()
    {
        Session::forget(['authenticated', 'user_email', 'authenticated_at']);
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
