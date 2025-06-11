<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property int $brand_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandSynonym whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BrandSynonym extends Model
{
    protected $fillable = [
        'title',
        'brand_id'
    ];
}
