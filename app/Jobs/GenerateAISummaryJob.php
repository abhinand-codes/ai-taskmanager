<?php

namespace App\Jobs;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAISummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10;
    public int $timeout = 60;

    public function __construct(private Task $task) {}

    public function handle(AIService $aiService, TaskRepositoryInterface $repo): void
    {
        Log::info("GenerateAISummaryJob started for Task #{$this->task->id}");

        $aiData = $aiService->generateSummary($this->task);
        $repo->update($this->task->id, $aiData);

        Log::info("GenerateAISummaryJob completed for Task #{$this->task->id}", $aiData);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateAISummaryJob FAILED for Task #{$this->task->id}: " . $exception->getMessage());
    }
}