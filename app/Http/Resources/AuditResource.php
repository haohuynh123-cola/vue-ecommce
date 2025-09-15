<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'event' => $this->event,
            'old_values' => $this->old_values ? json_decode($this->old_values, true) : null,
            'new_values' => $this->new_values ? json_decode($this->new_values, true) : null,
            'url' => $this->url,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'auditable' => $this->whenLoaded('auditable', function () {
                return [
                    'id' => $this->auditable->id,
                    'type' => class_basename($this->auditable),
                    'name' => $this->auditable->name ?? $this->auditable->title ?? class_basename($this->auditable) . ' #' . $this->auditable->id,
                ];
            }),
        ];
    }
}
