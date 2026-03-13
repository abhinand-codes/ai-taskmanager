# AI TaskManager

A Laravel 12 task management system with AI-powered summaries using Google Gemini.

## Stack

- Laravel 12, PHP 8.2
- Vue 3 + Inertia.js + Tailwind CSS
- MySQL 8
- Docker + Nginx
- Google Gemini AI
- Pest (testing)

## Architecture

Repository → Service → Controller → Resource → Inertia (Vue)

- `app/Repositories/` — data access layer
- `app/Services/` — business logic
- `app/Policies/` — authorization
- `app/Jobs/` — async AI summary generation
- `app/Http/Resources/` — API transformation

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

Visit: http://localhost:8080

## Credentials

| Role  | Email                     | Password |
|-------|---------------------------|----------|
| Admin | admin@taskmanager.com     | password |
| User  | john@taskmanager.com      | password |
| User  | jane@taskmanager.com      | password |

## Features

- **Admin**: Create, edit, delete, assign tasks
- **User**: View and update status of assigned tasks
- **AI**: Gemini generates summary and priority suggestion per task
- **Queue**: AI job runs async via database queue worker
- **Dashboard**: Stats cards + Chart.js doughnut chart
- **Filters**: Search, status, priority filtering with pagination

## Testing
```bash
docker exec taskmanager_app php artisan test
```

15 tests, 52 assertions — all passing.

## AI Integration

- Provider: Google Gemini (`gemini-1.5-flash`)
- Triggered on task create and title/description update
- Falls back to mock response if API unavailable
- Set `AI_MOCK_MODE=true` to disable real API calls
