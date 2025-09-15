<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MagicLink;
use App\Mail\MagicLinkMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check for abuse patterns
        $abuseCheck = $this->checkForAbuse($email, $ipAddress);
        if ($abuseCheck['blocked']) {
            return back()->with('error', $abuseCheck['message']);
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $this->extractNameFromEmail($email),
                'password' => '', // Empty password for magic link auth (will be nullable after migration)
            ]
        );

        // Create magic link for the user with IP and user agent tracking
        $magicLink = MagicLink::createForUser($user, $ipAddress, $userAgent);

        // Send email
        Mail::to($email)->send(new MagicLinkMail($magicLink));

        return back()->with('success', 'Magic link sent! Check your email for the secure login link.');
    }

    private function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];

        return ucfirst(str_replace(['.', '_', '-'], ' ', $localPart));
    }

    public function verifyMagicLink(string $token)
    {
        $magicLink = MagicLink::where('token', $token)
            ->notBlocked()
            ->with('user')
            ->first();

        if (! $magicLink || ! $magicLink->isValid()) {
            return redirect()->route('login')->with('error', 'Invalid or expired magic link.');
        }

        // Check if the magic link is blocked
        if ($magicLink->isBlocked()) {
            return redirect()->route('login')->with('error', 'This magic link has been blocked due to suspicious activity.');
        }

        // Mark as used
        $magicLink->markAsUsed();

        // Set session with user information
        Session::put('authenticated', true);
        Session::put('authenticated_at', now());
        Auth::login($magicLink->user);

        return redirect()->route('task-tracker')->with('success', 'Welcome back! You are now logged in.');
    }

    public function logout()
    {
        Session::forget(['authenticated', 'user_id', 'user_email', 'user_name', 'authenticated_at']);

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function checkForAbuse(string $email, string $ipAddress): array
    {
        // Check if email or IP is currently blocked
        if (MagicLink::isEmailBlocked($email)) {
            return [
                'blocked' => true,
                'message' => 'This email address has been temporarily blocked due to suspicious activity. Please try again later.',
            ];
        }

        if (MagicLink::isIpBlocked($ipAddress)) {
            return [
                'blocked' => true,
                'message' => 'Your IP address has been temporarily blocked due to suspicious activity. Please try again later.',
            ];
        }

        // Rate limiting checks
        $recentEmailAttempts = MagicLink::getRecentAttemptsForEmail($email, 15); // 15 minutes
        $recentIpAttempts = MagicLink::getRecentAttemptsForIp($ipAddress, 15);
        $dailyEmailAttempts = MagicLink::getDailyAttemptsForEmail($email);
        $dailyIpAttempts = MagicLink::getDailyAttemptsForIp($ipAddress);

        // Block if too many recent attempts from same email
        if ($recentEmailAttempts >= 3) {
            $this->blockEmail($email, 'Too many recent attempts from email');

            return [
                'blocked' => true,
                'message' => 'Too many recent attempts from this email address. Please wait 15 minutes before trying again.',
            ];
        }

        // Block if too many recent attempts from same IP
        if ($recentIpAttempts >= 5) {
            $this->blockIp($ipAddress, 'Too many recent attempts from IP');

            return [
                'blocked' => true,
                'message' => 'Too many recent attempts from your IP address. Please wait 15 minutes before trying again.',
            ];
        }

        // Block if too many daily attempts from same email
        if ($dailyEmailAttempts >= 10) {
            $this->blockEmail($email, 'Daily limit exceeded for email');

            return [
                'blocked' => true,
                'message' => 'Daily limit exceeded for this email address. Please try again tomorrow.',
            ];
        }

        // Block if too many daily attempts from same IP
        if ($dailyIpAttempts >= 20) {
            $this->blockIp($ipAddress, 'Daily limit exceeded for IP');

            return [
                'blocked' => true,
                'message' => 'Daily limit exceeded for your IP address. Please try again tomorrow.',
            ];
        }

        // Cooldown period check (minimum 2 minutes between requests for same email)
        $lastAttempt = MagicLink::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($lastAttempt) {
            return [
                'blocked' => true,
                'message' => 'Please wait at least 2 minutes between magic link requests.',
            ];
        }

        return ['blocked' => false, 'message' => ''];
    }

    private function blockEmail(string $email, string $reason): void
    {
        MagicLink::where('email', $email)
            ->where('blocked', false)
            ->update([
                'blocked' => true,
                'blocked_until' => now()->addHours(24),
                'block_reason' => $reason,
            ]);
    }

    private function blockIp(string $ipAddress, string $reason): void
    {
        MagicLink::where('ip_address', $ipAddress)
            ->where('blocked', false)
            ->update([
                'blocked' => true,
                'blocked_until' => now()->addHours(24),
                'block_reason' => $reason,
            ]);
    }
}
