<?php

namespace App\Http\Resources;

use App\Models\ProductSubtypeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductSubtypeFormat
 */
class ProductSubtypeFormatResource extends JsonResource
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
            'title' => $this->title,
            'product_subtype_format' => $this->product_subtype_format,
            'description' => $this->description,
            'hint' => $this->hint,
            'image_url' => $this->getFirstMediaUrl('image'),
        ];
    }
}
