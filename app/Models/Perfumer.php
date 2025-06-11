<?php

namespace App\Models;

use App\Observers\PerfumerObserver;
use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([PerfumerObserver::class])]
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $short_description
 * @property int $country_id
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PerfumerSynonym> $synonyms
 * @property-read int|null $synonyms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Perfumer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Perfumer extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSlug;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'country_id',
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

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'pageable')->withDefault();
    }

    public function synonyms(): HasMany
    {
        return $this->hasMany(PerfumerSynonym::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
