<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin Product
 */
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
            'title' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'tag' => $this->tag,
            'description' => $this->description,
            'category_type' => $this->category_type,
            'subtype_category_type' => $this->subtype_category_type,
            'brand_id' => $this->brand_id,
            'year' => $this->year,
            'gender_type' => $this->gender_type,
            'video' => $this->video,
            'longevity' => $this->longevity,
            'sillage' => $this->sillage,
            'season' => $this->season,
            'time_of_day' => $this->time_of_day,
            'gender' => $this->gender,
            'is_active' => (bool) $this->is_active,
            'category_type_title' => $this->category_type_title,
            'subtype_category_type_title' => $this->subtype_category_type_title,
            'gender_type_title' => $this->gender_type_title,
            'favorite_image_url' => $this->favorite_image_url,
            'brands' => $this->brands,

            'subtypes' => ProductSubtypeResource::collection($this->whenLoaded('subtypes')),

            'images' => $this->getMedia('images')
                ->map(fn(Media $media) => $media->getFullUrl())
                ->toArray(),

            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'synonyms' => SynonymResource::collection($this->whenLoaded('synonyms')),

            'perfumers' => PerfumerResource::collection($this->whenLoaded('perfumers')),
            'aroma_groups' => AromaGroupResource::collection($this->whenLoaded('aromaGroups')),
            'aroma_chords' => AromaChordResource::collection($this->whenLoaded('aromaChords')),

            'top_notes' => AromaNoteResource::collection($this->whenLoaded('topNotes')),
            'middle_notes' => AromaNoteResource::collection($this->whenLoaded('middleNotes')),
            'base_notes' => AromaNoteResource::collection($this->whenLoaded('baseNotes')),

//            'parfum_box_subtypes' => ProductSubtypeResource::collection($this->whenLoaded('parfumBoxSubtypes')),

            'similar_products' => ProductResource::collection($this->whenLoaded('similarProducts')),
            'similar_from'       => ProductResource::collection($this->whenLoaded('similarTo')),
        ];
    }
}
