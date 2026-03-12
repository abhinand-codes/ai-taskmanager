<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(): Response
    {
        $stats = $this->taskService->getStats();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}