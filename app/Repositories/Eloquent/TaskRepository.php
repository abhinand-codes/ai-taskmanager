<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Task::with(['assignedUser', 'creator'])
            ->filter($filters)
            ->latest()
            ->paginate(10);
    }

    public function find(int $id): Task
    {
        return Task::with(['assignedUser', 'creator'])->findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->find($id);
        $task->update($data);
        $this->clearCache();
        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        $task = $this->find($id);
        $this->clearCache();
        return $task->delete();
    }

    public function getStats(): array
    {
        return Cache::remember('task_stats', 300, function () {
            return [
                'total'       => Task::count(),
                'completed'   => Task::where('status', 'completed')->count(),
                'pending'     => Task::where('status', 'pending')->count(),
                'in_progress' => Task::where('status', 'in_progress')->count(),
                'high'        => Task::where('priority', 'high')->count(),
            ];
        });
    }

    private function clearCache(): void
    {
        Cache::forget('task_stats');
    }
}