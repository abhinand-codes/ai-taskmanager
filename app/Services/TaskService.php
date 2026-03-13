<?php

namespace App\Services;

use App\Jobs\GenerateAISummaryJob;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private AIService $aiService,
    ) {}

    public function index(array $filters = []): LengthAwarePaginator
    {
        if (!Auth::user()->isAdmin()) {
            $filters['assigned_to'] = Auth::id();
        }

        return $this->taskRepository->all($filters);
    }

    public function store(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();

            $task = $this->taskRepository->create($data);

            GenerateAISummaryJob::dispatch($task)->onQueue('default');

            return $task;
        });
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->taskRepository->find($id);

        $needsAI = isset($data['title']) || isset($data['description']);

        $updated = $this->taskRepository->update($id, $data);

        if ($needsAI) {
            GenerateAISummaryJob::dispatch($updated)->onQueue('default');
        }

        return $updated;
    }

    public function updateStatus(int $id, string $status): Task
    {
        return $this->taskRepository->update($id, ['status' => $status]);
    }

    public function delete(int $id): bool
    {
        return $this->taskRepository->delete($id);
    }

    public function find(int $id): Task
    {
        return $this->taskRepository->find($id);
    }

    public function getStats(): array
    {
        return $this->taskRepository->getStats();
    }
}