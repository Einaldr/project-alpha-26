<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectSecretsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Return the public connection type (e.g. 'none', 'https', 'ssh')
            'auth_type' => $this->auth_type->value ?? $this->auth_type ?? 'none',
            
            // Simply return if a token is set, protecting the raw token from being exposed
            'is_configured' => !empty($this->access_token),
            
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
