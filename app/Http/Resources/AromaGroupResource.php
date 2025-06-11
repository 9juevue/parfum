<?php

namespace App\Http\Resources;

use App\Models\AromaGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AromaGroup
 */
class AromaGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'color' => $this->color,
            'image_url' => $this->getFirstMediaUrl('image'),
        ];
    }
}
