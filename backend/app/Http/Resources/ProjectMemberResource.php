<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Standardizing on 'member_id' to clarify this is the relationship ID, not the user's ID
            'member_id' => $this->id,
            
            // Nested User details (protects password, email, etc.)
            'user' => new UserResource($this->whenLoaded('user')),
            
            // Project-level permissions (array of strings cast from your Enum)
            'permissions' => $this->permissions,
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
