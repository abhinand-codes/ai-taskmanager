<?php

namespace App\Models;

use App\Enums\AIPriority;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
        'ai_summary',
        'ai_priority',
    ];

    protected function casts(): array
    {
        return [
            'priority'    => TaskPriority::class,
            'status'      => TaskStatus::class,
            'ai_priority' => AIPriority::class,
            'due_date'    => 'date',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeFilter($query, array $filters): void
    {
        $query
            ->when(
                $filters['status'] ?? null,
                fn($q, $v) => $q->where('status', $v)
            )
            ->when(
                $filters['priority'] ?? null,
                fn($q, $v) => $q->where('priority', $v)
            )
            ->when(
                $filters['search'] ?? null,
                fn($q, $v) => $q->where('title', 'like', "%{$v}%")
            )
            ->when(
                $filters['assigned_to'] ?? null,
                fn($q, $v) => $q->where('assigned_to', $v)
            );
    }
}