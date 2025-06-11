<?php

namespace App\Http\Resources;

use App\Enums\Product\ProductSubtypeFormatEnum;
use App\Models\ProductSubtype;
use App\Models\ProductSubtypeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductSubtype
 */
class ProductSubtypeResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_subtype_type' => $this->product_subtype_type,
            'price' => $this->price,
            'qty' => $this->qty,
            'popularity' => $this->popularity,
            'product_subtype_format_id' => $this->product_subtype_format_id,
            'volume' => $this->volume,
            'bottle_volume' => $this->bottle_volume,
            'volume_text' => $this->volume_text,
            'product_subtype_type_title' => $this->product_subtype_type_title,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'product_subtype_format_type' => ProductSubtypeFormatResource::make($this->whenLoaded('productSubtypeFormat')),
        ];
    }
}
