<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->index(
            $request->only(['status', 'priority', 'search'])
        );

        $tasksData = TaskResource::collection($tasks)->response()->getData(true);

        return Inertia::render('Tasks/Index', [
            'tasks'   => array_merge($tasksData, [
                'links' => $tasks->linkCollection()->toArray(),
            ]),
            'filters' => $request->only(['status', 'priority', 'search']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Task::class);

        $users = User::select('id', 'name', 'email')->get();

        return Inertia::render('Tasks/Form', [
            'users' => $users,
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->store($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task): Response
    {
        $task->load(['assignedUser', 'creator']);
        $this->authorize('view', $task);

        return Inertia::render('Tasks/Show', [
            'task' => (new TaskResource($task))->resolve(),
        ]);
    }

    public function edit(Task $task): Response
    {
        $task->load(['assignedUser', 'creator']);
        $this->authorize('update', $task);

        $users = User::select('id', 'name', 'email')->get();

        return Inertia::render('Tasks/Form', [
            'task'  => (new TaskResource($task))->resolve(),
            'users' => $users,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Authorization handled in UpdateTaskRequest
        $this->taskService->update($task->id, $request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task->id);

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
