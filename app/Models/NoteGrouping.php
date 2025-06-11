<?php

namespace App\Models;

use App\Observers\NoteGroupingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([NoteGroupingObserver::class])]
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AromaNote> $aromaNotes
 * @property-read int|null $aroma_notes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteGrouping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NoteGrouping extends Model
{
    protected $fillable = [
        'title'
    ];

    public function aromaNotes(): HasMany
    {
        return $this->hasMany(AromaNote::class);
    }
}
