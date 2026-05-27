<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'actor' => $this->user_id ? new UserResource($this->whenLoaded('user')) : [
                'id' => null,
                'name' => 'System',
            ], 
            'action' => $this->action->value,
            'payload' => $this->payload,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
