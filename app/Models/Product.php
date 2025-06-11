<?php

namespace App\Models;

use App\Enums\Product\ProductCategoryEnum;
use App\Enums\Product\ProductGenderEnum;
use App\Enums\Product\ProductSubtypeCategoryEnum;
use App\Observers\ProductObserver;
use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 *
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $sku
 * @property string|null $tag
 * @property string $description
 * @property ProductSubtypeCategoryEnum $subtype_category_type
 * @property ProductCategoryEnum $category_type
 * @property int $brand_id
 * @property int|null $year
 * @property ProductGenderEnum|null $gender_type
 * @property string|null $video
 * @property int|null $longevity
 * @property int|null $sillage
 * @property int|null $season
 * @property int|null $time_of_day
 * @property int|null $gender
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaChord> $aromaChords
 * @property-read int|null $aroma_chords_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaGroup> $aromaGroups
 * @property-read int|null $aroma_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaNote> $baseNotes
 * @property-read int|null $base_notes_count
 * @property-read \App\Models\Brand $brand
 * @property-read string $brands
 * @property-read string $category_type_title
 * @property-read string|null $favorite_image_url
 * @property-read string|null $gender_type_title
 * @property-read string|null $subtype_category_type_title
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaNote> $middleNotes
 * @property-read int|null $middle_notes_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubtype> $parfumBoxSubtypes
 * @property-read int|null $parfum_box_subtypes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Perfumer> $perfumers
 * @property-read int|null $perfumers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $similarProducts
 * @property-read int|null $similar_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $similarTo
 * @property-read int|null $similar_to_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubtype> $subtypes
 * @property-read int|null $subtypes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSynonym> $synonyms
 * @property-read int|null $synonyms_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaNote> $topNotes
 * @property-read int|null $top_notes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereGenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLongevity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSillage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSubtypeCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTimeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereYear($value)
 * @mixin \Eloquent
 */
#[ObservedBy([ProductObserver::class])]
class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSlug;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'slug',
        'title',
        'sku',
        'tag',
        'description',
        'brand_id',
        'category_type',
        'subtype_category_type',
        'year',
        'gender_type',
        'video',
        'longevity',
        'sillage',
        'season',
        'time_of_day',
        'gender',
        'is_active'
    ];

    protected $casts = [
        'gender_type' => ProductGenderEnum::class,
        'category_type' => ProductCategoryEnum::class,
        'subtype_category_type' => ProductSubtypeCategoryEnum::class,
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'pageable')->withDefault();
    }

    public function subtypes(): HasMany
    {
        return $this->hasMany(ProductSubtype::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function synonyms(): HasMany
    {
        return $this->hasMany(ProductSynonym::class);
    }

    public function parfumBoxSubtypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductSubtype::class,
            'parfum_box_subtypes',
            'product_id',
            'product_subtype_id'
        );
    }

    public function similarProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'similar_products',
            'product_id',
            'similar_product_id'
        );
    }

    public function similarTo(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'similar_products',
            'similar_product_id',
            'product_id'
        );
    }

    public function perfumers(): BelongsToMany
    {
        return $this->belongsToMany(
            Perfumer::class,
            'product_perfumers',
            'product_id',
            'perfumer_id'
        );
    }

    public function topNotes(): BelongsToMany
    {
        return $this->belongsToMany(
            AromaNote::class,
            'product_top_notes',
            'product_id',
            'aroma_note_id'
        );
    }

    public function middleNotes(): BelongsToMany
    {
        return $this->belongsToMany(
            AromaNote::class,
            'product_middle_notes',
            'product_id',
            'aroma_note_id'
        );
    }

    public function baseNotes(): BelongsToMany
    {
        return $this->belongsToMany(
            AromaNote::class,
            'product_base_notes',
            'product_id',
            'aroma_note_id'
        );
    }

    public function aromaGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            AromaGroup::class,
            'product_aroma_group',
            'product_id',
            'aroma_group_id'
        );
    }

    public function aromaChords(): BelongsToMany
    {
        return $this->belongsToMany(
            AromaChord::class,
            'product_aroma_chord',
            'product_id',
            'aroma_chord_id'
        );
    }

    public function getCategoryTypeTitleAttribute(): string
    {
        return $this->category_type->label();
    }


    public function getSubtypeCategoryTypeTitleAttribute(): ?string
    {
        return $this->subtype_category_type->label();
    }

    public function getGenderTypeTitleAttribute(): ?string
    {
        return $this->gender_type?->label();
    }

    public function getFavoriteImageUrlAttribute(): ?string
    {
        $images = $this->getMedia('images')
            ->sortBy('order_column');

        $favorite = $images->first(function (Media $media) {
            return $media->getCustomProperty('is_favorite', false) === true;
        });

        if ($favorite) {
            return $favorite->getFullUrl();
        }

        $first = $images->first();

        return $first ? $first->getFullUrl() : null;
    }

    public function getBrandsAttribute(): string
    {
        return match ($this->category_type) {
            ProductCategoryEnum::GiftSet, ProductCategoryEnum::Product => $this->brand->title,
            default => 'В разработке',
        };
    }
}
