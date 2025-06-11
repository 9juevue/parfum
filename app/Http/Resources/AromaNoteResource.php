<?php

namespace App\Http\Resources;

use App\Models\AromaNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AromaNote
 */
class AromaNoteResource extends JsonResource
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
            'title_ru' => $this->title_ru,
            'title_en' => $this->title_en,
            'short_description' => $this->short_description,
            'seo_description' => $this->seo_description,
            'color' => $this->color,
            'note_grouping_id' => $this->note_grouping_id,
            'image_url' => $this->getFirstMediaUrl('image'),
            'note_grouping' =>  NoteGroupingResource::make($this->whenLoaded('noteGrouping')),
        ];
    }
}
