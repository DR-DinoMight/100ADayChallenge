<?php

declare(strict_types=1);

use App\Models\MagicLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email' => 'test@example.com']);
});

it('allows normal magic link requests', function () {
    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(MagicLink::count())->toBe(1);
    expect(MagicLink::first()->email)->toBe('test@example.com');
});

it('tracks IP address and user agent', function () {
    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 Test Browser',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $magicLink = MagicLink::first();
    expect($magicLink)->not->toBeNull();
    expect($magicLink->ip_address)->not->toBeNull();
    expect($magicLink->user_agent)->toBe('Mozilla/5.0 Test Browser');
});

it('blocks too many recent attempts from same email', function () {
    // Create 3 recent attempts
    for ($i = 0; $i < 3; $i++) {
        MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    }

    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');

    expect(MagicLink::isEmailBlocked('test@example.com'))->toBeTrue();
});

it('blocks too many recent attempts from same IP', function () {
    // First, make a request to see what IP the test environment uses
    $firstResponse = $this->post('/magic-link/request', [
        'email' => 'first@example.com',
    ]);

    $firstMagicLink = MagicLink::latest()->first();
    $testIp = $firstMagicLink->ip_address;

    // Create 4 more recent attempts from the same IP (total 5, which should trigger block)
    for ($i = 0; $i < 4; $i++) {
        $user = User::factory()->create(['email' => "user{$i}@example.com"]);
        MagicLink::createForUser($user, $testIp, 'Test Browser');
    }

    // Make the 6th request which should trigger the block (since limit is >= 5)
    $response = $this->post('/magic-link/request', [
        'email' => 'newuser@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');

    expect(MagicLink::isIpBlocked($testIp))->toBeTrue();
});

it('blocks daily limit exceeded for email', function () {
    // Create 10 attempts for today
    for ($i = 0; $i < 10; $i++) {
        MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    }

    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');

    expect(MagicLink::isEmailBlocked('test@example.com'))->toBeTrue();
});

it('blocks daily limit exceeded for IP', function () {
    // First, make a request to see what IP the test environment uses
    $firstResponse = $this->post('/magic-link/request', [
        'email' => 'first@example.com',
    ]);

    $firstMagicLink = MagicLink::latest()->first();
    $testIp = $firstMagicLink->ip_address;

    // Create 19 more attempts for today from the same IP (total 20, which should trigger block)
    for ($i = 0; $i < 19; $i++) {
        $user = User::factory()->create(['email' => "user{$i}@example.com"]);
        MagicLink::createForUser($user, $testIp, 'Test Browser');
    }

    // Make the 21st request which should trigger the block (since limit is >= 20)
    $response = $this->post('/magic-link/request', [
        'email' => 'newuser@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');

    expect(MagicLink::isIpBlocked($testIp))->toBeTrue();
});

it('enforces cooldown period between requests', function () {
    // Create a recent attempt (within 2 minutes)
    MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');

    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');
});

it('allows requests after cooldown period', function () {
    // Create an old attempt (more than 2 minutes ago) with a test IP
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.100', 'Test Browser');
    $magicLink->update(['created_at' => now()->subMinutes(3)]);

    $response = $this->post('/magic-link/request', [
        'email' => 'cooldown-test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $response->assertSessionMissing('error');
});

it('blocks magic link verification for blocked links', function () {
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    $magicLink->block('Test block reason', 24);

    $response = $this->get("/magic-link/verify/{$magicLink->token}");

    $response->assertRedirect('/login');
    $response->assertSessionHas('error');
});

it('allows magic link verification for non-blocked links', function () {
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');

    $response = $this->get("/magic-link/verify/{$magicLink->token}");

    $response->assertRedirect('/tracker');
    $response->assertSessionHas('success');
});

it('tracks attempt counts correctly', function () {
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');

    expect($magicLink->attempt_count)->toBe(1);

    $magicLink->incrementAttempts();
    $magicLink->refresh();

    expect($magicLink->attempt_count)->toBe(2);
    expect($magicLink->last_attempt_at)->not->toBeNull();
});

it('can unblock email addresses', function () {
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    $magicLink->block('Test block reason', 24);

    expect(MagicLink::isEmailBlocked('test@example.com'))->toBeTrue();

    $magicLink->unblock();

    expect(MagicLink::isEmailBlocked('test@example.com'))->toBeFalse();
});

it('can unblock IP addresses', function () {
    $magicLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    $magicLink->block('Test block reason', 24);

    expect(MagicLink::isIpBlocked('192.168.1.1'))->toBeTrue();

    $magicLink->unblock();

    expect(MagicLink::isIpBlocked('192.168.1.1'))->toBeFalse();
});

it('cleans up expired magic links', function () {
    // Create expired magic link
    $expiredLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    $expiredLink->update(['expires_at' => now()->subHour()]);

    // Create blocked expired link
    $blockedLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    $blockedLink->update([
        'blocked' => true,
        'blocked_until' => now()->subHour(),
    ]);

    // Create valid link
    $validLink = MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');

    expect(MagicLink::count())->toBe(3);

    $deletedCount = MagicLink::cleanupExpired();

    expect($deletedCount)->toBe(2);
    expect(MagicLink::count())->toBe(1);
    expect(MagicLink::first()->id)->toBe($validLink->id);
});

it('respects different rate limits for email vs IP', function () {
    // Create 2 attempts from same email but different IPs (should be allowed)
    for ($i = 0; $i < 2; $i++) {
        MagicLink::createForUser($this->user, "192.168.1.{$i}", 'Test Browser');
    }

    $response = $this->post('/magic-link/request', [
        'email' => 'rate-limit-test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $response->assertSessionMissing('error');
});

it('handles edge case of exactly at limit', function () {
    // Create exactly 3 recent attempts (should trigger block on next attempt)
    for ($i = 0; $i < 3; $i++) {
        MagicLink::createForUser($this->user, '192.168.1.1', 'Test Browser');
    }

    $response = $this->post('/magic-link/request', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $response->assertSessionMissing('success');
});
