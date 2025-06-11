<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property int $perfumer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym wherePerfumerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerfumerSynonym whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PerfumerSynonym extends Model
{
    protected $fillable = [
        'title',
        'perfumer_id',
    ];
}
