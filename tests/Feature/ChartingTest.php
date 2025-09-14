<?php

declare(strict_types=1);

use App\Livewire\TaskTracker;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Livewire\Livewire;

test('chart data is generated correctly for daily progress', function () {
    $user = User::factory()->create(['email' => 'test@example.com', 'password' => '']);
    $taskType = TaskType::factory()->create(['name' => 'push_ups', 'daily_goal' => 100]);

    // Create some test tasks
    Task::create([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 50,
        'completed_date' => now()->subDays(2)->toDateString(),
    ]);

    Task::create([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 75,
        'completed_date' => now()->subDays(1)->toDateString(),
    ]);

    Task::create([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 100,
        'completed_date' => now()->toDateString(),
    ]);

    // Simulate user session
    session(['user_id' => $user->id]);

    $component = Livewire::test(TaskTracker::class)
        ->set('selectedTaskTypeId', $taskType->id);

    $chartData = $component->get('chartData');

    expect($chartData)->not->toBeNull();
    expect($chartData['labels'])->toHaveCount(30);
    expect($chartData['data'])->toHaveCount(30);
    expect($chartData['goal'])->toBe(100);

    // Check that we have data for the last 3 days
    $lastThreeDays = array_slice($chartData['data'], -3);
    expect($lastThreeDays)->toContain(50, 75, 100);
});

test('weekly chart data is generated correctly', function () {
    $user = User::factory()->create(['email' => 'test@example.com', 'password' => '']);
    $taskType = TaskType::factory()->create(['name' => 'push_ups', 'daily_goal' => 100]);

    // Create tasks for the current week
    Task::create([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 50,
        'completed_date' => now()->startOfWeek()->toDateString(),
    ]);

    Task::create([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 75,
        'completed_date' => now()->startOfWeek()->addDays(1)->toDateString(),
    ]);

    // Simulate user session
    session(['user_id' => $user->id]);

    $component = Livewire::test(TaskTracker::class)
        ->set('selectedTaskTypeId', $taskType->id);

    $weeklyChartData = $component->get('weeklyChartData');

    expect($weeklyChartData)->not->toBeNull();
    expect($weeklyChartData['labels'])->toHaveCount(12);
    expect($weeklyChartData['data'])->toHaveCount(12);

    // Check that we have data for the current week
    $currentWeekData = end($weeklyChartData['data']);
    expect($currentWeekData)->toBe(125); // 50 + 75
});

test('charts are not displayed when no task type is selected', function () {
    $user = User::factory()->create(['email' => 'test@example.com', 'password' => '']);

    // Simulate user session
    session(['user_id' => $user->id]);

    $component = Livewire::test(TaskTracker::class);

    $chartData = $component->get('chartData');
    $weeklyChartData = $component->get('weeklyChartData');

    expect($chartData)->toBeNull();
    expect($weeklyChartData)->toBeNull();
});

test('time of day chart data is generated correctly', function () {
    $user = User::factory()->create(['email' => 'test@example.com', 'password' => '']);
    $taskType = TaskType::factory()->create(['name' => 'push_ups', 'daily_goal' => 100]);

    // Create tasks at different times of day
    $today = now()->toDateString();

    \DB::table('tasks')->insert([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 50,
        'completed_date' => $today,
        'created_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 6, 30, 0),
        'updated_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 6, 30, 0),
    ]);

    \DB::table('tasks')->insert([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 30,
        'completed_date' => $today,
        'created_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 12, 15, 0),
        'updated_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 12, 15, 0),
    ]);

    \DB::table('tasks')->insert([
        'user_id' => $user->id,
        'task_type_id' => $taskType->id,
        'count' => 20,
        'completed_date' => $today,
        'created_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 18, 45, 0),
        'updated_at' => \Carbon\Carbon::create(now()->year, now()->month, now()->day, 18, 45, 0),
    ]);

    // Simulate user session
    session(['user_id' => $user->id]);

    $component = Livewire::test(TaskTracker::class)
        ->set('selectedTaskTypeId', $taskType->id);

    $timeOfDayChartData = $component->get('timeOfDayChartData');

    expect($timeOfDayChartData)->not->toBeNull();
    expect($timeOfDayChartData['labels'])->toHaveCount(24);
    expect($timeOfDayChartData['data'])->toHaveCount(24);

    // Check that we have data for the correct hours
    expect($timeOfDayChartData['data'][6])->toBe(50); // 06:00 hour
    expect($timeOfDayChartData['data'][12])->toBe(30); // 12:00 hour
    expect($timeOfDayChartData['data'][18])->toBe(20); // 18:00 hour

    // Check that other hours are 0
    expect($timeOfDayChartData['data'][0])->toBe(0);
    expect($timeOfDayChartData['data'][23])->toBe(0);
});

test('charts are not displayed when user is not authenticated', function () {
    $taskType = TaskType::factory()->create(['name' => 'push_ups', 'daily_goal' => 100]);

    $component = Livewire::test(TaskTracker::class)
        ->set('selectedTaskTypeId', $taskType->id);

    $chartData = $component->get('chartData');
    $weeklyChartData = $component->get('weeklyChartData');
    $timeOfDayChartData = $component->get('timeOfDayChartData');

    expect($chartData)->toBeNull();
    expect($weeklyChartData)->toBeNull();
    expect($timeOfDayChartData)->toBeNull();
});
