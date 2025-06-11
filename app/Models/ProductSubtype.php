<?php

namespace App\Models;

use App\Enums\Product\ProductSubtypeTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 
 *
 * @property int $id
 * @property string|null $external_id
 * @property int $product_id
 * @property ProductSubtypeTypeEnum $product_subtype_type
 * @property float $price
 * @property int $qty
 * @property int|null $popularity
 * @property int|null $product_subtype_format_id
 * @property int $volume
 * @property int|null $bottle_volume
 * @property string|null $volume_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $product_subtype_type_title
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSubtypeFormat|null $productSubtypeFormat
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereBottleVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype wherePopularity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereProductSubtypeFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereProductSubtypeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtype whereVolumeText($value)
 * @mixin \Eloquent
 */
class ProductSubtype extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'external_id',
        'product_id',
        'product_subtype_type',
        'price',
        'qty',
        'popularity',
        'product_subtype_format_id',
        'volume',
        'bottle_volume',
        'volume_text'
    ];

    protected $casts = [
        'product_subtype_type' => ProductSubtypeTypeEnum::class,
        'price' => 'float',
        'volume' => 'integer',
        'bottle_volume' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSubtypeFormat(): BelongsTo
    {
        return $this->belongsTo(ProductSubtypeFormat::class);
    }

    public function getProductSubtypeTypeTitleAttribute(): string
    {
        return $this->product_subtype_type->label();
    }
}
