<?php

namespace App\Services;

use App\Jobs\GenerateAISummaryJob;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $repo,
        private AIService $aiService
    ) {}

    public function index(array $filters = []): LengthAwarePaginator
    {
        if (!Auth::user()->isAdmin()) {
            $filters['assigned_to'] = Auth::id();
        }

        return $this->repo->all($filters);
    }

    public function store(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();

            $task = $this->repo->create($data);

            GenerateAISummaryJob::dispatch($task)->onQueue('default');

            return $task;
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            $task = $this->repo->update($id, $data);

            if (isset($data['title']) || isset($data['description'])) {
                GenerateAISummaryJob::dispatch($task)->onQueue('default');
            }

            return $task;
        });
    }

    public function updateStatus(int $id, string $status): Task
    {
        return $this->repo->update($id, ['status' => $status]);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function find(int $id): Task
    {
        return $this->repo->find($id);
    }

    public function getStats(): array
    {
        return $this->repo->getStats();
    }
}