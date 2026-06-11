<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            
            'image_url' => $this->image_url,
            
            'git_url' => $this->git_url,
            'default_branch' => $this->default_branch,
            'last_pulled_at' => $this->last_pulled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            
            'group' => new GroupResource($this->whenLoaded('group')),
            'members' => ProjectMemberResource::collection($this->whenLoaded('members')),
            'secrets' => new ProjectSecretsResource($this->whenLoaded('secrets')),
        ];
    }
}
