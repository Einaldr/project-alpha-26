<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\RolePermissions;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User|null $user */
        $user = auth('sanctum')->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'group_type' => $this->type,
            'icon_url' => $this->icon_url,
            
            'owner' => new UserResource($this->whenLoaded('owner')),

            'billing_email' => $this->when($user && $user?->hasGroupPermission($this->resource, RolePermissions::GROUP_UPDATE), $this->billing_email),

            'parent' => new GroupResource($this->whenLoaded('parent')),

            'children' => GroupResource::collection($this->whenLoaded('children')),

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
