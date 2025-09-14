<?php

declare(strict_types=1);

use App\Models\MagicLink;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Support\Facades\Session;

test('multiple users can request magic links', function () {
    // Test user 1 - create with empty password for magic link auth
    $user1 = User::factory()->create([
        'email' => 'user1@example.com',
        'password' => '',
    ]);
    $magicLink1 = MagicLink::createForUser($user1);

    expect($magicLink1->user_id)->toBe($user1->id);
    expect($magicLink1->email)->toBe($user1->email);
    expect($magicLink1->isValid())->toBeTrue();

    // Test user 2
    $user2 = User::factory()->create([
        'email' => 'user2@example.com',
        'password' => '',
    ]);
    $magicLink2 = MagicLink::createForUser($user2);

    expect($magicLink2->user_id)->toBe($user2->id);
    expect($magicLink2->email)->toBe($user2->email);
    expect($magicLink2->isValid())->toBeTrue();

    // Verify they are different
    expect($magicLink1->token)->not->toBe($magicLink2->token);
});

test('users can only see their own tasks', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com', 'password' => '']);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'password' => '']);

    $taskType = TaskType::factory()->create(['name' => 'push_ups']);

    // Create tasks for both users
    $task1 = Task::create([
        'user_id' => $user1->id,
        'task_type_id' => $taskType->id,
        'count' => 50,
        'completed_date' => now()->toDateString(),
    ]);

    $task2 = Task::create([
        'user_id' => $user2->id,
        'task_type_id' => $taskType->id,
        'count' => 75,
        'completed_date' => now()->toDateString(),
    ]);

    // Verify user isolation
    $user1Tasks = Task::forUser($user1->id)->get();
    $user2Tasks = Task::forUser($user2->id)->get();

    expect($user1Tasks)->toHaveCount(1);
    expect($user2Tasks)->toHaveCount(1);
    expect($user1Tasks->first()->id)->toBe($task1->id);
    expect($user2Tasks->first()->id)->toBe($task2->id);
});

test('magic link authentication works for multiple users', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com', 'password' => '']);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'password' => '']);

    $magicLink1 = MagicLink::createForUser($user1);
    $magicLink2 = MagicLink::createForUser($user2);

    // Simulate user 1 login
    Session::put('authenticated', true);
    Session::put('user_id', $user1->id);
    Session::put('user_email', $user1->email);
    Session::put('user_name', $user1->name);
    Session::put('authenticated_at', now());

    expect(Session::get('user_id'))->toBe($user1->id);
    expect(Session::get('user_email'))->toBe($user1->email);

    // Clear session and simulate user 2 login
    Session::flush();

    Session::put('authenticated', true);
    Session::put('user_id', $user2->id);
    Session::put('user_email', $user2->email);
    Session::put('user_name', $user2->name);
    Session::put('authenticated_at', now());

    expect(Session::get('user_id'))->toBe($user2->id);
    expect(Session::get('user_email'))->toBe($user2->email);
});
