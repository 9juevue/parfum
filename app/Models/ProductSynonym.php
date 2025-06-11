<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSynonym whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductSynonym extends Model
{
    protected $fillable = [
        'title',
        'product_id'
    ];
}
