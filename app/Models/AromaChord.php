<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $short_description
 * @property string $color
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaChord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AromaChord extends Model implements HasMedia
{
    use InteractsWithMedia;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'title',
        'short_description',
        'color',
        'seo_title',
        'seo_description',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }
}
