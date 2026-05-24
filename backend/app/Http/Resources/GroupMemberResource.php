<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userResource = new UserResource($this->user);
        $userResource->asMember();
        return [
            'member_id' => $this->id,
            'user' => $userResource,
            'roles' => GroupRoleResource::collection($this->roles),
        ];
    }
}
