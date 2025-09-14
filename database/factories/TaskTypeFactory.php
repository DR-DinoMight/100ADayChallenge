<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskType>
 */
class TaskTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'push_ups',
            'sit_ups',
            'squats',
            'burpees',
            'pull_ups',
            'jumping_jacks',
            'lunges',
            'planks',
            'mountain_climbers',
            'dips',
        ]);

        return [
            'name' => $name,
            'display_name' => ucwords(str_replace('_', ' ', $name)),
            'daily_goal' => $this->faker->numberBetween(10, 200),
            'is_built_in' => $this->faker->boolean(70), // 70% chance of being built-in
        ];
    }

    /**
     * Create a built-in task type.
     */
    public function builtIn(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_built_in' => true,
        ]);
    }

    /**
     * Create a custom task type.
     */
    public function custom(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_built_in' => false,
        ]);
    }
}
