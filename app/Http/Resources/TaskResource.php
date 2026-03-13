<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,

            'priority' => $this->priority ? [
                'value' => $this->priority->value,
                'label' => $this->priority->label(),
                'color' => $this->priority->color(),
            ] : null,

            'status' => $this->status ? [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ] : null,

            'due_date'    => $this->due_date?->format('Y-m-d'),
            'ai_summary'  => $this->ai_summary,

            'ai_priority' => $this->ai_priority ? [
                'value' => $this->ai_priority->value,
                'label' => $this->ai_priority->label(),
            ] : null,

            'assigned_to' => $this->whenLoaded('assignedUser', fn () => $this->assignedUser ? [
                'id'    => $this->assignedUser->id,
                'name'  => $this->assignedUser->name,
                'email' => $this->assignedUser->email,
            ] : null),

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id'    => $this->creator->id,
                'name'  => $this->creator->name,
                'email' => $this->creator->email,
            ] : null),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}