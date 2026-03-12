<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@taskmanager.com')->first();
        $john  = User::where('email', 'john@taskmanager.com')->first();
        $jane  = User::where('email', 'jane@taskmanager.com')->first();

        $tasks = [
            [
                'title'       => 'Setup project infrastructure',
                'description' => 'Configure Docker, CI/CD pipeline and deployment scripts.',
                'priority'    => 'high',
                'status'      => 'completed',
                'due_date'    => now()->addDays(1)->format('Y-m-d'),
                'assigned_to' => $john->id,
                'created_by'  => $admin->id,
                'ai_summary'  => 'Critical infrastructure task requiring immediate attention.',
                'ai_priority' => 'high',
            ],
            [
                'title'       => 'Design database schema',
                'description' => 'Create ERD and finalize all table structures.',
                'priority'    => 'high',
                'status'      => 'in_progress',
                'due_date'    => now()->addDays(3)->format('Y-m-d'),
                'assigned_to' => $jane->id,
                'created_by'  => $admin->id,
                'ai_summary'  => 'Important design task that blocks other development work.',
                'ai_priority' => 'high',
            ],
            [
                'title'       => 'Implement user authentication',
                'description' => 'Build login, registration and password reset flows.',
                'priority'    => 'medium',
                'status'      => 'pending',
                'due_date'    => now()->addDays(5)->format('Y-m-d'),
                'assigned_to' => $john->id,
                'created_by'  => $admin->id,
                'ai_summary'  => 'Authentication is a foundational feature for the application.',
                'ai_priority' => 'medium',
            ],
            [
                'title'       => 'Write API documentation',
                'description' => 'Document all REST endpoints using Swagger or Postman.',
                'priority'    => 'low',
                'status'      => 'pending',
                'due_date'    => now()->addDays(10)->format('Y-m-d'),
                'assigned_to' => $jane->id,
                'created_by'  => $admin->id,
                'ai_summary'  => 'Documentation task important for team collaboration.',
                'ai_priority' => 'low',
            ],
            [
                'title'       => 'Performance optimization',
                'description' => 'Profile and optimize slow database queries.',
                'priority'    => 'medium',
                'status'      => 'pending',
                'due_date'    => now()->addDays(7)->format('Y-m-d'),
                'assigned_to' => $john->id,
                'created_by'  => $admin->id,
                'ai_summary'  => 'Optimization task to improve application response times.',
                'ai_priority' => 'medium',
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}