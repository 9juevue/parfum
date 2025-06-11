<?php

namespace App\Models;

use App\Observers\BrandObserver;
use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[ObservedBy([BrandObserver::class])]
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $short_description
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property int $country_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BrandSynonym> $synonyms
 * @property-read int|null $synonyms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSlug;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'slug',
        'title',
        'short_description',
        'seo_title',
        'seo_description',
        'country_id'
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
        return $this->hasMany(BrandSynonym::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
