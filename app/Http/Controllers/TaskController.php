<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->index(
            $request->only(['status', 'priority', 'search'])
        );

        return Inertia::render('Tasks/Index', [
            'tasks'   => TaskResource::collection($tasks),
            'filters' => $request->only(['status', 'priority', 'search']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Task::class);

        $users = \App\Models\User::select('id', 'name', 'email')->get();

        return Inertia::render('Tasks/Form', [
            'users' => $users,
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $this->taskService->store($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(int $id): Response
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    public function edit(int $id): Response
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);

        $users = \App\Models\User::select('id', 'name', 'email')->get();

        return Inertia::render('Tasks/Form', [
            'task'  => new TaskResource($task),
            'users' => $users,
        ]);
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);

        $this->taskService->update($id, $request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('delete', $task);

        $this->taskService->delete($id);

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $this->taskService->updateStatus($id, $request->status);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function aiSummary(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);

        return response()->json([
            'ai_summary'  => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ]);
    }
}