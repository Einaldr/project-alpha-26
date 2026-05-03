<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupRoleResource extends JsonResource
{
    protected bool $includePermissions = false;

    public function includePermissions(): self
    {
        $this->includePermissions = true;
        return $this;
    }

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
            'permissions' => $this->when($this->includePermissions, $this->permissions),
            'members' => GroupMemberResource::collection($this->whenLoaded('members'))
        ];
    }
}
