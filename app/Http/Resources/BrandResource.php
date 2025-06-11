<?php

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Brand
 */
class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'country_id' => $this->country_id,
            'image_url' => $this->getFirstMediaUrl('image'),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'synonyms' => SynonymResource::collection($this->whenLoaded('synonyms')),
        ];
    }
}
