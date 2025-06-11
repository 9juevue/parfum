<?php

namespace App\Http\Resources;

use App\Models\AromaChord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AromaChord
 */
class AromaChordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'seo_description' => $this->seo_description,
            'color' => $this->color,
            'image_url' => $this->getFirstMediaUrl('image'),
        ];
    }
}
