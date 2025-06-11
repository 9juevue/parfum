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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AromaGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AromaGroup extends Model implements HasMedia
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
