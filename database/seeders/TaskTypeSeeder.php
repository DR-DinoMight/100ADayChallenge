<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskType;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskTypes = [
            [
                'name' => 'push_ups',
                'display_name' => 'Push-ups',
                'daily_goal' => 100,
                'is_built_in' => true,
            ],
            [
                'name' => 'sit_ups',
                'display_name' => 'Sit-ups',
                'daily_goal' => 50,
                'is_built_in' => true,
            ],
            [
                'name' => 'squats',
                'display_name' => 'Squats',
                'daily_goal' => 100,
                'is_built_in' => true,
            ],
            [
                'name' => 'burpees',
                'display_name' => 'Burpees',
                'daily_goal' => 20,
                'is_built_in' => true,
            ],
            [
                'name' => 'pull_ups',
                'display_name' => 'Pull-ups',
                'daily_goal' => 20,
                'is_built_in' => true,
            ],
        ];

        foreach ($taskTypes as $taskType) {
            TaskType::updateOrCreate(
                ['name' => $taskType['name']],
                $taskType
            );
        }
    }
}
