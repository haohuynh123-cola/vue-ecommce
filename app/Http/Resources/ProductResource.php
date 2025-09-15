<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'manage_stock' => $this->manage_stock,
            'in_stock' => $this->in_stock,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'images' => $this->images,
            'attributes' => $this->attributes,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'category_id' => $this->category_id,
            'old_values' => $this->old_values ? json_decode($this->old_values, true) : null,
            'new_values' => $this->new_values ? json_decode($this->new_values, true) : null,
            'url' => $this->url,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
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
