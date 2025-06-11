<?php

namespace App\Models;

use App\Enums\Product\ProductSubtypeFormatEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 
 *
 * @property int $id
 * @property ProductSubtypeFormatEnum $product_subtype_format
 * @property string $description
 * @property string|null $hint
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $title
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereHint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereProductSubtypeFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSubtypeFormat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductSubtypeFormat extends Model implements HasMedia
{
    use InteractsWithMedia;
    use \App\Traits\Models\HasMedia;

    protected $fillable = [
        'product_subtype_format',
        'description',
        'hint'
    ];

    protected $casts = [
        'product_subtype_format' => ProductSubtypeFormatEnum::class
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile();
    }

    public function getTitleAttribute(): string
    {
        return $this->product_subtype_format->label();
    }
}
