<?php

namespace App\Http\Resources;

use App\Enum\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource
 * 
 * A Json resource representation of the User eloquent model.
 * 
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    private $renderAsMember = false;
    public function asMember() {
        $this->renderAsMember = true;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $isLoggedIn = auth('sanctum')->check();

        $isMe = $isLoggedIn && $request->user('sanctum')->id === $this->id;


        return[
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when(($isMe || $this->renderAsMember), $this->email),
            'status' => $this->getNormalizedStatus($isMe),
            'tos_version' => $this->when($isMe, $this->tos_version),
            'privacy_policy_version' => $this->when($isMe, $this->privacy_policy_version),
            'joined_at' => $this->created_at->toIso8601String(),
        ];
    }

    /**
     * Utility function returning human-readable version of AccountStatus.
     * @param bool $isMe Is the requester the same user as the eloquent model requested.
     * @return string Normalized status
     */
    private function getNormalizedStatus(bool $isMe): string
    {
        if ($this->trashed()) {
            return 'Deleted';
        }

        if ($isMe) {
            return $this->account_status?->label() ?? 'Unknown';
        }

        if ($this->account_status !== AccountStatus::ACTIVE) {
            return 'Deleted';
        }

        return 'Active';
    }
}
