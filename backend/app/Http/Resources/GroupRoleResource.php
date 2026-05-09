<?php

namespace App\Http\Resources;

use App\Enum\RolePermissions;
use App\Models\Group;
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
        $user = $request->user();
        $group = $this->group();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->when($this->includePermissions && ($user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES) || $user->hasGroupPermission($group, RolePermissions::ROLES_MANAGE)), $this->permissions),
            'group' => new GroupResource($this->whenLoaded('group')),
            'members' => GroupMemberResource::collection($this->whenLoaded('members'))
        ];
    }
}
