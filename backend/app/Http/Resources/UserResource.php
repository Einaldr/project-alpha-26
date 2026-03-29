<?php

namespace App\Http\Resources;

use App\Enum\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
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
            'email' => $this->when($isMe, $this->email),
            'status' => $this->getNormalizedStatus($isMe),
            'tos_version' => $this->when($isMe, $this->tos_version),
            'privacy_policy_version' => $this->when($isMe, $this->privacy_policy_version),
            'joined_at' => $this->created_at->toIso8601String(),
        ];
    }

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
