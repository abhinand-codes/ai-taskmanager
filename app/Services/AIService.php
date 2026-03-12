<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $model;
    private bool $mockMode;

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.key', '');
        $this->model    = config('services.gemini.model', 'gemini-1.5-flash');
        $this->mockMode = (bool) config('services.gemini.mock', false);
    }

    public function generateSummary(Task $task): array
    {
        if ($this->mockMode || empty($this->apiKey)) {
            return $this->mockResponse($task);
        }

        try {
            return $this->callGemini($task);
        } catch (\Throwable $e) {
            Log::error('AIService Gemini call failed: ' . $e->getMessage());
            return $this->mockResponse($task);
        }
    }

    private function callGemini(Task $task): array
    {
        $prompt = $this->buildPrompt($task);

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API error: ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        $data = json_decode($text, true);

        if (!isset($data['summary'], $data['priority'])) {
            throw new \RuntimeException('Invalid Gemini response structure');
        }

        $validPriorities = ['low', 'medium', 'high'];
        $priority = in_array($data['priority'], $validPriorities)
            ? $data['priority']
            : 'medium';

        return [
            'ai_summary'  => $data['summary'],
            'ai_priority' => $priority,
        ];
    }

    private function buildPrompt(Task $task): string
    {
        $dueDate    = $task->due_date?->format('Y-m-d') ?? 'Not set';
        $priority   = $task->priority->value;
        $status     = $task->status->value;
        $desc       = $task->description ?? 'No description provided';

        return <<<PROMPT
You are a task management assistant. Analyze the following task and return ONLY a JSON object.

Task Title: {$task->title}
Description: {$desc}
Current Priority: {$priority}
Due Date: {$dueDate}
Status: {$status}

Return ONLY valid JSON in this exact format with no extra text:
{
    "summary": "A concise 1-2 sentence summary of the task and its importance",
    "priority": "low|medium|high"
}

Base the priority recommendation on urgency, impact, and due date proximity.
PROMPT;
    }

    private function mockResponse(Task $task): array
    {
        return [
            'ai_summary'  => "Task '{$task->title}' has been analyzed. Based on the description and due date, this task should be addressed with the recommended priority level.",
            'ai_priority' => $task->priority->value,
        ];
    }
}