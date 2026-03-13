<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Task $task): bool
    {
        return $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return false;
    }

    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}