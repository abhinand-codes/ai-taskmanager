# AI TaskManager

A Laravel 12 task management system with AI-powered summaries and priority suggestions using Google Gemini.

## Screenshots

### Login
![Login](https://github.com/user-attachments/assets/8611b1b8-0a58-46be-9bf5-6678b7b51d1d)

### Dashboard
![Dashboard](https://github.com/user-attachments/assets/e0a4efe8-b440-4d3a-805e-7d551c752346)

### Tasks List
![Tasks List](https://github.com/user-attachments/assets/16a8d7f0-a46a-48c4-9a67-a0821a0fda65)

### Create Task
![Create Task](https://github.com/user-attachments/assets/6ea5027d-a269-446a-9843-32de09ea2adb)

### Task Detail with AI Summary
![Task Detail](https://github.com/user-attachments/assets/383325a6-f82f-426d-80f7-19ce13ab561b)

## Stack

- Laravel 12, PHP 8.3
- Vue 3 + Inertia.js + Tailwind CSS
- MySQL 8
- Docker + Nginx
- Google Gemini AI (`gemini-1.5-flash`)
- Pest (feature testing)

## Architecture
```
app/
├── Http/
│   ├── Controllers/        # TaskController, DashboardController
│   ├── Requests/           # StoreTaskRequest, UpdateTaskRequest
│   ├── Resources/          # TaskResource
│   └── Middleware/
├── Models/                 # User, Task
├── Repositories/
│   ├── Contracts/          # TaskRepositoryInterface
│   └── Eloquent/           # TaskRepository
├── Services/
│   ├── TaskService.php     # Business logic, transactions
│   └── AIService.php       # Gemini API, prompt, parsing
├── Jobs/
│   └── GenerateAISummaryJob.php
├── Policies/
│   └── TaskPolicy.php
├── Enums/
│   ├── TaskPriority.php
│   ├── TaskStatus.php
│   └── AIPriority.php
└── Providers/
    └── RepositoryServiceProvider.php
```

**Flow:** Request → FormRequest (validate) → Controller → Service → Repository → Model

No direct Eloquent calls in controllers. All data access goes through the Repository layer.

## AI Integration

### Provider
Google Gemini (`gemini-1.5-flash`) via HTTP API.

### Trigger
AI summary is generated automatically when a task is **created** or when its **title/description is updated**. It runs as an async queued job (`GenerateAISummaryJob`) so it never blocks the HTTP response.

### Exact Prompt Sent to Gemini
```
Analyze this task and provide:
1. A brief 1-2 sentence summary of what needs to be done
2. A suggested priority level (low, medium, or high) based on the task details

Task Title: {title}
Task Description: {description}

Respond in this exact JSON format:
{
  "summary": "your summary here",
  "priority": "low|medium|high"
}
```

### Response Parsing
The AIService parses the JSON response, extracts `summary` and `priority`, and updates the task via the repository. If the API call fails or returns malformed JSON, a mock fallback response is used — ensuring the task is never left without an AI summary.

### Mock Fallback
```php
return [
    'ai_summary'  => 'Task requires attention. Please review and complete as soon as possible.',
    'ai_priority' => 'medium',
];
```

Set `AI_MOCK_MODE=true` in `.env` to always use the mock (useful for local dev without an API key).

## Setup

### Requirements
- Docker Desktop
- Git

### Installation
```bash
git clone https://github.com/abhinand-codes/ai-taskmanager.git
cd ai-taskmanager
cp .env.example .env
# Add your GEMINI_API_KEY to .env
docker compose up -d
docker exec taskmanager_app composer install
docker exec taskmanager_app php artisan key:generate
docker exec taskmanager_app php artisan migrate:fresh --seed
docker exec taskmanager_app npm install
docker exec taskmanager_app npm run build
```

Visit: **http://localhost:8080**

## Credentials

| Role  | Email                     | Password |
|-------|---------------------------|----------|
| Admin | admin@taskmanager.com     | password |
| User  | john@taskmanager.com      | password |
| User  | jane@taskmanager.com      | password |

## Features

| Feature | Details |
|---------|---------|
| **Admin** | Create, edit, delete, assign any task |
| **User** | View and update status of assigned tasks only |
| **AI Summary** | Gemini generates a summary + priority on task create/update |
| **Queue** | AI job runs async via database queue worker |
| **Dashboard** | Stats cards (total, completed, pending, high priority) + Chart.js doughnut |
| **Filters** | Search by title, filter by status and priority, paginated |
| **Policies** | TaskPolicy enforces role-based access on every action |
| **Caching** | Dashboard stats cached for 5 minutes via `Cache::remember` |

## API Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/tasks` | List tasks with filters | ✅ |
| POST | `/tasks` | Create task | Admin |
| GET | `/tasks/{id}` | View task detail | ✅ |
| PUT | `/tasks/{id}` | Update task | Admin |
| DELETE | `/tasks/{id}` | Delete task (soft) | Admin |
| PATCH | `/tasks/{id}/status` | Update status | Assigned user |
| GET | `/tasks/{id}/ai-summary` | Get AI summary | ✅ |

## Testing
```bash
docker exec taskmanager_app php artisan test
```

**40 tests, 113 assertions — all passing.**

Covers: authentication, role-based access, CRUD operations, policy enforcement, validation.

## Bonus Features Completed

- ✅ Queued AI job (`GenerateAISummaryJob`) with retry logic (3 attempts, 10s backoff)
- ✅ Repository caching on dashboard stats
- ✅ 40 feature tests with Pest
- ✅ Docker (app, nginx, mysql, queue worker — 4 containers)
- ✅ Clean conventional commits
- ✅ Soft deletes on tasks