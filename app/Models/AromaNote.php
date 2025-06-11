<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 
 *
 * @property int $id
 * @property string $title_ru
 * @property string $title_en
 * @property string $color
 * @property string|null $short_description
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property int $note_grouping_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\NoteGrouping $noteGrouping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereNoteGroupingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereTitleRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AromaNote extends Model implements HasMedia
{
    use InteractsWithMedia;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'title_ru',
        'title_en',
        'color',
        'short_description',
        'seo_title',
        'seo_description',
        'note_grouping_id'
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function noteGrouping(): BelongsTo
    {
        return $this->belongsTo(NoteGrouping::class);
    }
}
