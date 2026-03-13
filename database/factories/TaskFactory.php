<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority'    => $this->faker->randomElement(['low', 'medium', 'high']),
            'status'      => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'due_date'    => $this->faker->dateTimeBetween('now', '+30 days'),
            'assigned_to' => null,
            'created_by'  => User::factory(),
            'ai_summary'  => null,
            'ai_priority' => null,
        ];
    }
}